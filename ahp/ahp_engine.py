#!/usr/bin/env python3
"""
AHP Engine untuk "Risk Mitigate".

Mengimplementasikan metode pada paper:
  Françozo et al. (2023), "A web-based software for group decision with
  analytic hierarchy process", MethodsX 11, 102277.

Metode:
  1. AHP-Express (Leal, 2020): satu elemen dijadikan referensi lalu
     dibandingkan terhadap (n-1) elemen lain. Prioritas lokal diperoleh
     lewat transitivitas sehingga Consistency Ratio = 0.
        - Untuk referensi r: a_rj = w_r / w_j  (input judgment).
        - Set v_r = 1 ; v_j = 1 / a_rj ; lalu normalisasi.
  2. Group decision via AIP (Aggregation of Individual Priorities) dengan
     weighted geometric mean (Persamaan 9 pada paper):
        P_g(j) = Π_i  P_i(j) ^ w_i         (Σ w_i = 1)
     kemudian dinormalisasi.

Skala perbandingan: Saaty 6-poin (1..6) beserta resiprokalnya (1/2..1/6).
Engine hanya mengolah nilai rasio; validasi rentang skala dilakukan di sisi
aplikasi (Laravel).

I/O: membaca satu objek JSON dari STDIN, menulis satu objek JSON ke STDOUT.

Input:
{
  "operation": "formulation" | "expert_weights",
  "elements": [1, 2, 3, ...],          # id elemen (faktor risiko / user), terurut
  "participants": [
    {
      "id": 10,                        # id partisipan (user)
      "weight": 0.4,                   # bobot DM utk agregasi grup (opsional)
      "reference": 2,                  # id elemen referensi
      "judgments": {"1": 3, "3": 0.5}  # id_other -> nilai (w_ref / w_other)
    }
  ]
}

Output:
{
  "ok": true,
  "method": "single" | "group",
  "elements": [...],
  "individual": {"10": {"1": 0.2, "2": 0.5, "3": 0.3}, ...},
  "aggregated": {"1": 0.18, "2": 0.55, "3": 0.27},
  "ranking": [{"element": 2, "weight": 0.55, "rank": 1}, ...],
  "cr": 0.0
}
"""
import json
import math
import sys


def normalize(vec: dict) -> dict:
    total = sum(vec.values())
    if total <= 0:
        n = len(vec)
        return {k: 1.0 / n for k in vec} if n else {}
    return {k: v / total for k, v in vec.items()}


def ahp_express(reference, judgments: dict, elements: list) -> dict:
    """Hitung vektor prioritas lokal dari (n-1) judgment AHP-Express.

    judgments: {other_element_id(str): value}, value = w_ref / w_other.
    Elemen yang tidak dinilai diasumsikan setara (rasio 1).
    """
    raw = {}
    for el in elements:
        if el == reference:
            raw[el] = 1.0
        else:
            val = judgments.get(str(el), judgments.get(el, 1.0))
            try:
                val = float(val)
            except (TypeError, ValueError):
                val = 1.0
            if val <= 0:
                val = 1.0
            raw[el] = 1.0 / val
    return normalize(raw)


def aggregate_aip(individual: dict, dm_weights: dict, elements: list) -> dict:
    """Weighted geometric mean of individual priorities (AIP, Persamaan 9)."""
    # Normalisasi bobot DM (harus berjumlah 1); fallback ke bobot setara.
    w_total = sum(v for v in dm_weights.values() if v is not None)
    ids = list(individual.keys())
    if w_total <= 0:
        dm_weights = {pid: 1.0 / len(ids) for pid in ids}
    else:
        dm_weights = {pid: (dm_weights.get(pid) or 0.0) / w_total for pid in ids}

    agg = {}
    for el in elements:
        log_sum = 0.0
        for pid in ids:
            p = individual[pid].get(el, 0.0)
            if p <= 0:
                p = 1e-12
            log_sum += dm_weights[pid] * math.log(p)
        agg[el] = math.exp(log_sum)
    return normalize(agg)


# Random Index (Alonso & Lamata, 2005 / Saaty & Vargas) untuk CR.
RI = {1: 0.0, 2: 0.0, 3: 0.52, 4: 0.89, 5: 1.11, 6: 1.25, 7: 1.35,
      8: 1.40, 9: 1.45, 10: 1.49, 11: 1.51, 12: 1.54, 13: 1.56,
      14: 1.57, 15: 1.58}


def standard_ahp(elements: list, comparisons: list) -> dict:
    """AHP standar: matriks pairwise penuh (n×n) dari SEMUA pasangan (i, j).

    comparisons: [{"i": id_i, "j": id_j, "value": a_ij = w_i/w_j}, ...]
    Prioritas via geometric mean method; konsistensi via Consistency Ratio.
    """
    n = len(elements)
    pos = {e: k for k, e in enumerate(elements)}
    A = [[1.0] * n for _ in range(n)]

    for c in comparisons:
        i = pos.get(c.get("i"))
        j = pos.get(c.get("j"))
        if i is None or j is None:
            continue
        try:
            v = float(c.get("value", 1.0))
        except (TypeError, ValueError):
            v = 1.0
        if v <= 0:
            v = 1.0
        A[i][j] = v
        A[j][i] = 1.0 / v

    # Prioritas: rata-rata geometrik tiap baris, lalu dinormalisasi (Pers. 2).
    raw = []
    for i in range(n):
        prod = 1.0
        for j in range(n):
            prod *= A[i][j]
        raw.append(prod ** (1.0 / n))
    total = sum(raw)
    w = [x / total for x in raw] if total > 0 else [1.0 / n] * n

    # lambda_max = rata-rata (A·w)_i / w_i
    lam = 0.0
    for i in range(n):
        aw = sum(A[i][j] * w[j] for j in range(n))
        lam += aw / w[i] if w[i] > 0 else 0.0
    lam /= n

    ci = (lam - n) / (n - 1) if n > 1 else 0.0
    ri = RI.get(n, 1.49)
    cr = (ci / ri) if ri > 0 else 0.0

    priorities = {elements[k]: w[k] for k in range(n)}
    return {
        "priorities": priorities,
        "lambda_max": lam,
        "ci": ci,
        "ri": ri,
        "cr": cr,
    }


def run_standard(payload: dict) -> dict:
    elements = payload.get("elements", [])
    participants = payload.get("participants", [])
    if not elements:
        return {"ok": False, "error": "elements kosong"}
    if not participants:
        return {"ok": False, "error": "participants kosong"}

    p = participants[0]
    pid = str(p.get("id"))
    out = standard_ahp(elements, p.get("comparisons", []) or [])
    priorities = out["priorities"]

    return {
        "ok": True,
        "method": "single",
        "elements": elements,
        "individual": {pid: priorities},
        "aggregated": priorities,
        "ranking": build_ranking(priorities),
        "cr": round(out["cr"], 4),
        "consistency": {
            "lambda_max": round(out["lambda_max"], 4),
            "ci": round(out["ci"], 4),
            "ri": out["ri"],
            "cr": round(out["cr"], 4),
            "consistent": out["cr"] <= 0.1,
        },
    }


def build_ranking(vec: dict) -> list:
    ordered = sorted(vec.items(), key=lambda kv: kv[1], reverse=True)
    ranking = []
    for idx, (el, w) in enumerate(ordered, start=1):
        ranking.append({"element": el, "weight": w, "rank": idx})
    return ranking


def run(payload: dict) -> dict:
    # Mode standar: 1 partisipan, matriks pairwise penuh (AHP klasik).
    if payload.get("mode") == "standard":
        return run_standard(payload)

    elements = payload.get("elements", [])
    participants = payload.get("participants", [])

    if not elements:
        return {"ok": False, "error": "elements kosong"}
    if not participants:
        return {"ok": False, "error": "participants kosong"}

    individual = {}
    dm_weights = {}
    for p in participants:
        pid = p.get("id")
        reference = p.get("reference")
        if reference is None:
            return {"ok": False, "error": f"partisipan {pid} tidak memiliki referensi"}
        judgments = p.get("judgments", {}) or {}
        individual[str(pid)] = ahp_express(reference, judgments, elements)
        dm_weights[str(pid)] = p.get("weight")

    if len(participants) == 1:
        aggregated = individual[str(participants[0]["id"])]
        method = "single"
    else:
        aggregated = aggregate_aip(individual, dm_weights, elements)
        method = "group"

    return {
        "ok": True,
        "method": method,
        "elements": elements,
        "individual": individual,
        "aggregated": aggregated,
        "ranking": build_ranking(aggregated),
        "cr": 0.0,  # AHP-Express selalu konsisten sempurna
    }


def main():
    try:
        raw = sys.stdin.read()
        payload = json.loads(raw)
    except Exception as e:  # noqa: BLE001
        print(json.dumps({"ok": False, "error": f"input tidak valid: {e}"}))
        sys.exit(0)

    try:
        result = run(payload)
    except Exception as e:  # noqa: BLE001
        result = {"ok": False, "error": f"kalkulasi gagal: {e}"}

    sys.stdout.write(json.dumps(result, ensure_ascii=False))


if __name__ == "__main__":
    main()
