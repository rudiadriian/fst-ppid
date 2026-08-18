{{--
    Satu baris keterangan pada halaman Profil.

    Variabel: $label, $nilai, $catatan (opsional). Nilai kosong dicetak "—"
    supaya barisnya tetap ada — pemohon perlu tahu bahwa isian itu memang belum
    diisi, bukan hilang dari halaman.
--}}
<div class="py-3 grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-4">
    <dt class="text-sm text-gray-500 dark:text-gray-400">{{ $label }}</dt>
    <dd class="sm:col-span-2 text-sm font-semibold text-gray-900 dark:text-white break-words">
        {{ filled($nilai) ? $nilai : '—' }}
        @if (!empty($catatan))
            <span class="block mt-0.5 text-xs font-normal text-gray-500 dark:text-gray-400">{{ $catatan }}</span>
        @endif
    </dd>
</div>
