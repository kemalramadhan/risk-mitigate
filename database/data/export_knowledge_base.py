#!/usr/bin/env python3
"""
Ekspor "Knowledge Base.xlsx" menjadi JSON terstruktur untuk di-seed ke MySQL.

Struktur sumber (merged-cell / grouped):
  - Kolom Domain, Faktor_Risiko, Risk_Level di-forward-fill.
  - Baris dengan "Strategi Mitigasi" tidak-null MEMULAI strategi baru.
  - Baris berikut dengan "Strategi Mitigasi" null namun memiliki Sumber
    merupakan referensi TAMBAHAN untuk strategi terakhir.

Output JSON:
[
  {
    "domain": "IT",
    "risk_level": "Low",
    "name": "Team_Experience_Level_Expert",
    "display_name": "Team Experience Level - Expert",
    "strategies": [
      {
        "text": "....",
        "references": [
          {"sumber": "...", "lokasi": "...", "kutipan": "..."}
        ]
      }
    ]
  }, ...
]
"""
import json
import re
import sys
import warnings
from pathlib import Path

warnings.filterwarnings("ignore")
import pandas as pd  # noqa: E402

BASE = Path(__file__).resolve().parents[2]
XLSX = BASE / "docs" / "knowledge_base_rev3.xlsx"
OUT = Path(__file__).resolve().parent / "knowledge_base.json"

# Nama sheet -> kode domain internal
SHEET_TO_DOMAIN = {"IT": "IT", "RnD": "RnD"}

# Kolom rev2 menambahkan "Judul_Sumber" (judul paper) setelah "Sumber".
COLS = [
    "Domain", "Faktor_Risiko", "Risk_Level", "Strategi",
    "Sumber", "Judul_Sumber", "Lokasi", "Kutipan",
]
NCOLS = len(COLS)


def clean(v):
    if v is None:
        return None
    if isinstance(v, float) and pd.isna(v):
        return None
    s = str(v).strip()
    return s if s and s.lower() != "nan" else None


def prettify(name: str) -> str:
    """Ubah 'Team_Experience_Level_Expert' -> 'Team Experience Level - Expert'.

    Menangani duplikasi prefix seperti
    'Complexity_Score_Complexity_Score_Low (2,4 - 5,89)'.
    """
    if not name:
        return name
    # Hilangkan duplikasi berurutan '<X>_<X>_' -> '<X>_'
    collapsed = re.sub(r"^(.*?)_\1_", r"\1_", name)
    return collapsed.replace("_", " ").strip()


def main():
    if not XLSX.exists():
        print(f"ERROR: tidak menemukan {XLSX}", file=sys.stderr)
        sys.exit(1)

    xl = pd.ExcelFile(XLSX)
    result = []

    for sheet, domain in SHEET_TO_DOMAIN.items():
        df = xl.parse(sheet).iloc[:, :NCOLS]
        df.columns = COLS
        # forward-fill grup
        df[["Domain", "Faktor_Risiko", "Risk_Level"]] = df[
            ["Domain", "Faktor_Risiko", "Risk_Level"]
        ].ffill()

        factors = {}  # (risk_level, name) -> factor dict (menjaga urutan)
        order = []
        current_strategy = None

        for _, row in df.iterrows():
            name = clean(row["Faktor_Risiko"])
            level = clean(row["Risk_Level"])
            strat = clean(row["Strategi"])
            sumber = clean(row["Sumber"])
            judul = clean(row["Judul_Sumber"])
            lokasi = clean(row["Lokasi"])
            kutipan = clean(row["Kutipan"])

            if not name or not level:
                continue

            key = (level, name)
            if key not in factors:
                factors[key] = {
                    "domain": domain,
                    "risk_level": level,
                    "name": name,
                    "display_name": prettify(name),
                    "strategies": [],
                }
                order.append(key)

            factor = factors[key]

            if strat:  # mulai strategi baru
                current_strategy = {"text": strat, "references": []}
                factor["strategies"].append(current_strategy)

            # lampirkan referensi (baik pada baris strategi maupun baris lanjutan)
            if current_strategy is not None and (sumber or judul or lokasi or kutipan):
                current_strategy["references"].append(
                    {"sumber": sumber, "judul_sumber": judul, "lokasi": lokasi, "kutipan": kutipan}
                )

        for key in order:
            result.append(factors[key])

    OUT.write_text(json.dumps(result, ensure_ascii=False, indent=2), encoding="utf-8")

    # Ringkasan + validasi
    by_domain_level = {}
    total_strategies = 0
    for f in result:
        by_domain_level.setdefault((f["domain"], f["risk_level"]), 0)
        by_domain_level[(f["domain"], f["risk_level"])] += 1
        total_strategies += len(f["strategies"])

    print(f"Ditulis: {OUT}")
    print(f"Total faktor risiko : {len(result)}")
    print(f"Total strategi      : {total_strategies}")
    print("Faktor per (domain, level):")
    ok = True
    for k in sorted(by_domain_level):
        n = by_domain_level[k]
        flag = "OK" if n == 9 else "!! EXPECTED 9"
        if n != 9:
            ok = False
        print(f"  {k[0]:4} {k[1]:9}: {n}  {flag}")
    sys.exit(0 if ok else 2)


if __name__ == "__main__":
    main()
