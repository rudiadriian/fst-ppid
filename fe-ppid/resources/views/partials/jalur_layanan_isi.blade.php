{{--
    Isi kartu Jalur Pelayanan.

    Dipisah karena pembungkusnya berbeda-beda — jalur Online berupa <a> menuju
    Portal Pemohon, jalur Langsung berupa <button> yang membuka panel Waktu
    Layanan — sedangkan isinya harus tetap sama persis.

    Variabel: $channel (label, desc, recommended), $ikon (path SVG),
    $petunjuk (teks aksi di kaki kartu; boleh null).
--}}
<span class="w-10 h-10 mb-4 bg-white text-[#E87317] rounded-full flex items-center justify-center flex-shrink-0 shadow-sm">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $ikon }}"></path></svg>
</span>

<span class="flex flex-wrap items-center gap-2 mb-2">
    <span class="text-[11px] font-bold uppercase tracking-[0.14em] text-white">{{ __($channel['label']) }}</span>
    @if ($channel['recommended'])
        <span class="text-[10px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-full bg-white text-[#9E470D]">{{ __('Direkomendasikan') }}</span>
    @endif
</span>

<span class="block text-sm font-normal text-white/90 leading-relaxed">{{ __($channel['desc']) }}</span>

@if ($petunjuk)
    <span class="mt-4 inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-white/90">
        {{ $petunjuk }}
        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
    </span>
@endif
