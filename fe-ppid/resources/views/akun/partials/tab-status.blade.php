{{-- Nav tab filter status.
     Butuh: $status (tab aktif, '' = Semua) dan $jumlahStatus (peta label → jumlah).
     Filter lain (cari, urut, per) ikut dibawa lewat query string. --}}
@php
    // Portal Pemohon hanya memakai dua kelompok besar (+ Semua); tahapan
    // internal seperti Revisi & Menunggu Persetujuan tetap tampil sebagai
    // label status di tiap baris, bukan sebagai tab tersendiri.
    $tab = array_merge([['label' => '', 'teks' => __('Semua')]],
        array_map(fn ($l) => ['label' => $l, 'teks' => __($l)], \App\Models\PermohonanInformasi::KELOMPOK_PORTAL));

    $tabOff = 'inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-gray-600 hover:bg-emerald-50 hover:text-[#10462F] transition-colors dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-[#3E9C6C]';
    $tabOn = 'inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold fs-gradient-accent text-white shadow-lg shadow-emerald-900/20';
@endphp

<div class="px-5 pt-5 flex flex-wrap gap-1.5 border-b border-gray-100 dark:border-white/10 pb-4" role="tablist">
    @foreach ($tab as $item)
        @php $aktif = $status === $item['label']; @endphp
        <a href="{{ request()->fullUrlWithQuery(['status' => $item['label'] ?: null, 'page' => 1]) }}"
           role="tab" @if ($aktif) aria-selected="true" @endif
           class="{{ $aktif ? $tabOn : $tabOff }}">
            {{ $item['teks'] }}
            <span class="px-2 py-0.5 rounded-full text-xs {{ $aktif ? 'bg-white/25' : 'bg-gray-100 dark:bg-white/10' }}">
                {{ $jumlahStatus[$item['label']] ?? 0 }}
            </span>
        </a>
    @endforeach
</div>
