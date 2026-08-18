{{--
    Reusable AHP-Express pairwise form (Saaty 6-poin, TANPA nilai setara/netral).
    Params:
      $formAction, $referenceField, $items[ [id,title,subtitle] ],
      $referenceId, $positions[id=>pos], $scaleLabels[1..6], $submitLabel

    Slider UI memakai indeks 1..10 (tanpa titik tengah netral):
      indeks 1..5  -> pos -5..-1  (elemen pembanding lebih penting)
      indeks 6..10 -> pos  1..5   (elemen referensi lebih penting)
--}}
@php
    $cid = 'ahp_'.uniqid();
    $ahpData = [
        'items'       => $items,
        'referenceId' => (int) $referenceId,
        'positions'   => (object) $positions,
        'labels'      => $scaleLabels,
    ];
@endphp

<form method="POST" action="{{ $formAction }}" id="{{ $cid }}_form">
    @csrf

    <div class="card mb-3">
        <div class="card-body">
            <label class="form-label small fw-semibold mb-1">Elemen Referensi</label>
            <p class="text-muted small mb-2">Pilih satu elemen sebagai referensi. Anda hanya membandingkannya terhadap {{ count($items) - 1 }} elemen lain (metode AHP-Express, konsistensi selalu 0). Setiap perbandingan wajib memihak salah satu sisi — <strong>tidak ada penilaian setara</strong>.</p>
            <select name="{{ $referenceField }}" id="{{ $cid }}_ref" class="form-select">
                @foreach ($items as $it)
                    <option value="{{ $it['id'] }}" {{ (int) $referenceId === (int) $it['id'] ? 'selected' : '' }}>{{ $it['title'] }}</option>
                @endforeach
            </select>
            <div id="{{ $cid }}_refdesc" class="factor-desc mt-2"></div>
        </div>
    </div>

    <div id="{{ $cid }}_rows"></div>

    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted small">Skala Saaty: 2 = sedikit lebih penting · 6 = mutlak lebih penting (tanpa nilai setara)</div>
        <button type="submit" class="btn btn-primary fw-semibold">{{ $submitLabel }}</button>
    </div>

    <script type="application/json" id="{{ $cid }}_data">@json($ahpData)</script>
</form>

<script>
(function () {
    const cid = @json($cid);
    const data = JSON.parse(document.getElementById(cid + '_data').textContent);
    const refSelect = document.getElementById(cid + '_ref');
    const rowsEl = document.getElementById(cid + '_rows');
    const items = data.items;
    const labels = data.labels;        // {2:'...',...}
    const positions = data.positions || {};

    // Konversi indeks slider (1..10) <-> posisi (-5..-1, 1..5), tanpa 0.
    // Kiri = referensi (pos > 0), kanan = pembanding (pos < 0):
    //   geser KIRI  -> referensi (kiri) makin penting
    //   geser KANAN -> pembanding (kanan) makin penting
    const idxToPos = (idx) => (idx <= 5 ? 6 - idx : 5 - idx);
    const posToIdx = (pos) => (pos > 0 ? 6 - pos : 5 - pos);

    function ratioText(pos, refTitle, otherTitle) {
        const intensity = Math.abs(pos) + 1;               // 2..6
        const favored = pos > 0 ? refTitle : otherTitle;
        const label = labels[intensity] || '';
        return `<span class="pairwise-value">${intensity}×</span>` +
               `<span class="text-muted small ms-2"><strong>${favored}</strong> — ${label}</span>`;
    }

    const refDescEl = document.getElementById(cid + '_refdesc');

    function render() {
        const refId = parseInt(refSelect.value);
        const ref = items.find(i => i.id === refId);
        if (refDescEl) refDescEl.textContent = ref.subtitle || '';
        rowsEl.innerHTML = '';
        items.filter(i => i.id !== refId).forEach(other => {
            // Default (belum dinilai): pos = 1 (referensi sedikit lebih penting).
            const prefilled = positions[other.id] !== undefined;
            const pos = prefilled ? parseInt(positions[other.id]) : 1;
            const idx = posToIdx(pos);
            const row = document.createElement('div');
            row.className = 'pairwise-row';
            row.innerHTML = `
                <div class="d-flex justify-content-between align-items-start mb-2 gap-3">
                    <span class="side ref">${ref.title}</span>
                    <span class="text-muted small pt-1">vs</span>
                    <span class="side other text-end">${other.title}</span>
                </div>
                ${other.subtitle ? `<div class="factor-desc text-end mb-2">${other.subtitle}</div>` : ''}
                <input type="range" class="pairwise-slider" min="1" max="10" step="1" value="${idx}"
                       data-touched="${prefilled ? '1' : '0'}">
                <input type="hidden" name="pos[${other.id}]" value="${pos}">
                <div class="d-flex justify-content-between mt-1">
                    <span class="text-muted" style="font-size:.7rem">◀ ${ref.title} lebih penting</span>
                    <span class="text-muted" style="font-size:.7rem">${other.title} lebih penting ▶</span>
                </div>
                <div class="text-center mt-1 disp"></div>
            `;
            rowsEl.appendChild(row);
            const slider = row.querySelector('input[type=range]');
            const hidden = row.querySelector('input[type=hidden]');
            const disp = row.querySelector('.disp');
            const update = () => {
                const p = idxToPos(parseInt(slider.value));
                hidden.value = p;
                disp.innerHTML = ratioText(p, ref.title, other.title);
            };
            slider.addEventListener('input', () => { slider.dataset.touched = '1'; update(); });
            update();
        });
    }

    // Konfirmasi bila masih ada perbandingan pada nilai default (belum disentuh).
    document.getElementById(cid + '_form').addEventListener('submit', function (e) {
        const untouched = rowsEl.querySelectorAll('.pairwise-slider[data-touched="0"]').length;
        if (untouched > 0) {
            const ok = confirm(
                untouched + ' dari ' + rowsEl.querySelectorAll('.pairwise-slider').length +
                ' perbandingan masih pada nilai default dan belum Anda ubah.\n\n' +
                'Apakah nilai tersebut memang disengaja?\n\n' +
                'OK = lanjut simpan · Batal = tinjau kembali penilaian.'
            );
            if (!ok) { e.preventDefault(); }
        }
    });

    refSelect.addEventListener('change', render);
    render();
})();
</script>
