{{-- Hasil satu level: params $result (array). --}}
@foreach ($result['factors'] as $factor)
    <div class="mb-4">
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="rank-chip {{ $factor['rank'] == 1 ? 'top' : '' }}">{{ $factor['rank'] }}</span>
            <span class="fw-semibold flex-grow-1">{{ $factor['display_name'] }}</span>
            <span class="text-end">
                <span class="d-block factor-desc" style="line-height:1.1">Bobot Risiko</span>
                <span class="mono-code">{{ number_format($factor['percentage'], 2) }}%/100%</span>
            </span>
        </div>
        @if (!empty($factor['description']))
            <div class="factor-desc mb-2">{{ $factor['description'] }}</div>
        @endif
        <div class="weight-bar mb-2"><span style="width: {{ min(100, $factor['percentage']) }}%"></span></div>

        <div class="ps-1">
            @foreach ($factor['strategies'] as $s)
                <div class="strategy-item">
                    <div class="small">{{ $s['text'] }}</div>
                    @foreach ($s['references'] ?? [] as $ref)
                        @if (!empty($ref['sumber']) || !empty($ref['kutipan']) || !empty($ref['judul_sumber']))
                            @php
                                // Halaman sumber tidak ditampilkan — cukup bagian/section-nya.
                                $lokasi = \App\Support\SourceLocation::withoutPages($ref['lokasi'] ?? null);

                                $baris = '';
                                if (! empty($ref['sumber'])) {
                                    $baris .= '<span class="fw-semibold">'.e($ref['sumber']).'</span>';
                                }
                                if (! empty($ref['judul_sumber'])) {
                                    $baris .= '<span class="fst-italic">, &ldquo;'.e($ref['judul_sumber']).'&rdquo;</span>';
                                }
                                if (! empty($lokasi)) {
                                    $baris .= ' &mdash; '.e($lokasi);
                                }
                            @endphp
                            <div class="ref-quote">
                                <div>{!! $baris !!}</div>
                                @if (! empty($ref['kutipan']))
                                    <div class="ref-kutipan">&ldquo;{{ $ref['kutipan'] }}&rdquo;</div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
@endforeach
