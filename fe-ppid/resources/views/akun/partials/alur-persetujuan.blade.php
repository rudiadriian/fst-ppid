{{--
    Alur persetujuan versi pemohon: tiga langkah, selalu tiga.

    Panel admin punya jenjang berlapis dengan putaran revisi yang bisa berulang
    beberapa kali. Semua itu urusan internal (langkah 101). Yang perlu diketahui
    pemohon cuma di mana berkasnya berada: sudah diajukan, sedang diproses, atau
    sudah selesai. Menampilkan tiap perpindahan internal membuat langkahnya
    bertambah-kurang tiap kali petugas bekerja, dan pemohon membaca perbaikan
    pekerjaan petugas sebagai masalah pada berkasnya sendiri.

    Dipakai rincian permohonan maupun keberatan; keduanya punya tiga tonggak
    yang sama meski kosakata statusnya berbeda.

    @param string      $tahap    'diajukan' | 'diproses' | 'selesai'
    @param array|null  $tanggal  ['diajukan' => Carbon|null, ...]
--}}
@php
    $langkah = [
        'diajukan' => __('Diajukan'),
        'diproses' => __('Diproses'),
        'selesai' => __('Selesai'),
    ];

    $urutan = array_keys($langkah);
    $posisi = array_search($tahap, $urutan, true);
    $posisi = $posisi === false ? 0 : $posisi;
    $tanggal = $tanggal ?? [];

    $keterangan = [
        'diajukan' => __('Permohonan Anda diterima dan tercatat.'),
        'diproses' => __('Sedang ditelaah dan disiapkan tanggapannya oleh petugas PPID.'),
        'selesai' => __('Penanganannya sudah tuntas. Hasilnya dapat dibaca pada rincian di atas.'),
    ];
@endphp

<ol class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    @foreach ($langkah as $kunci => $label)
        @php
            $index = array_search($kunci, $urutan, true);
            $lewat = $index < $posisi;
            $sekarang = $index === $posisi;
            $waktu = $tanggal[$kunci] ?? null;
        @endphp

        <li class="rounded-xl border p-4
            {{ $sekarang
                ? 'border-[#E87317] bg-[#E87317]/5'
                : ($lewat ? 'border-emerald-100 bg-emerald-50/60 dark:bg-emerald-500/5 dark:border-emerald-500/20' : 'border-gray-100 dark:border-white/10') }}">
            <div class="flex items-center gap-2 mb-1.5">
                <span class="flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold
                    {{ $lewat
                        ? 'bg-[#10462F] text-white'
                        : ($sekarang ? 'bg-[#E87317] text-white' : 'bg-gray-100 text-gray-400 dark:bg-white/10 dark:text-gray-500') }}">
                    {{ $lewat ? '✓' : $index + 1 }}
                </span>
                <span class="text-sm font-bold {{ $lewat || $sekarang ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500' }}">
                    {{ $label }}
                </span>
            </div>

            <p class="text-xs {{ $lewat || $sekarang ? 'text-gray-600 dark:text-gray-300' : 'text-gray-400 dark:text-gray-500' }}">
                {{ $keterangan[$kunci] }}
            </p>

            @if ($waktu)
                <p class="mt-1.5 text-xs font-semibold text-gray-500 dark:text-gray-400">
                    {{ $waktu->translatedFormat('d F Y H:i') }}
                </p>
            @endif
        </li>
    @endforeach
</ol>
