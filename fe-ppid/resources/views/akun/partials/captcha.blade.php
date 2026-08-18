{{--
    Captcha + rem anti-bot untuk formulir Daftar dan Masuk.

    Tiga hal sekaligus:
      1. gambar captcha beserta isian jawabannya;
      2. isian umpan (honeypot) yang disembunyikan dari mata dan pembaca layar —
         hanya bot pengisi-otomatis yang akan mengisinya;
      3. penanda waktu terenkripsi, dipakai menolak kiriman yang datang lebih
         cepat daripada kemampuan mengetik manusia.

    Pemeriksaannya ada di App\Rules\CaptchaBenar dan App\Support\PerisaiFormulir.
--}}
@php
    use App\Support\PerisaiFormulir;

    $idGambar = 'captcha-gambar-' . ($idForm ?? 'form');
@endphp

@if (config('ppid.akun.captcha_aktif'))
    <div>
        <label for="captcha" class="{{ $labelClass }}">{{ __('Kode Captcha') }}</label>

        <div class="mt-1.5 flex flex-wrap items-center gap-3">
            <img id="{{ $idGambar }}"
                 src="{{ route('captcha') }}"
                 width="190" height="60"
                 alt="{{ __('Gambar kode captcha berisi lima huruf dan angka') }}"
                 class="rounded-xl border border-gray-200 dark:border-white/10 bg-white">

            <button type="button"
                    onclick="document.getElementById('{{ $idGambar }}').src='{{ route('captcha') }}?t='+Date.now()"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-[#10462F] dark:text-[#3E9C6C] border border-gray-200 dark:border-white/10 rounded-xl hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                {{ __('Ganti gambar') }}
            </button>
        </div>

        <input id="captcha" name="captcha" type="text" inputmode="text" autocomplete="off"
               required maxlength="8" class="{{ $inputClass }}"
               aria-describedby="captcha-bantuan">

        <p id="captcha-bantuan" class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
            {{ __('Ketik ulang kode pada gambar. Huruf besar/kecil tidak dibedakan. Sulit dibaca? Tekan Ganti gambar.') }}
        </p>

        @error('captcha')<p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
    </div>
@endif

{{-- Isian umpan: disembunyikan dan dijauhkan dari pembaca layar maupun tab. --}}
<div aria-hidden="true" style="position:absolute;left:-9999px;top:-9999px;height:0;overflow:hidden">
    <label for="{{ PerisaiFormulir::HONEYPOT }}">{{ __('Alamat surat') }}</label>
    <input id="{{ PerisaiFormulir::HONEYPOT }}" name="{{ PerisaiFormulir::HONEYPOT }}"
           type="text" tabindex="-1" autocomplete="off" value="">
</div>

<input type="hidden" name="{{ PerisaiFormulir::WAKTU }}" value="{{ PerisaiFormulir::token() }}">
