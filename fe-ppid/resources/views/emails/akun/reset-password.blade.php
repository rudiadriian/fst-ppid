{{--
    Tautan atur ulang password akun pemohon.

    Sengaja tidak memuat data akun apa pun selain nama: email reset bisa saja
    mendarat di kotak masuk orang yang bukan pemiliknya.
--}}
<x-email.layout :judul="__('Permintaan Reset Password')"
                :preheader="__('Tautan untuk membuat password baru akun PPID Food Station Anda.')">

    <p style="margin:0 0 14px 0;">{{ __('Halo :nama,', ['nama' => $nama]) }}</p>

    <p style="margin:0 0 14px 0;">
        {{ __('Kami menerima permintaan untuk mengatur ulang password akun PPID Food Station Anda.') }}
    </p>

    <p style="margin:0 0 4px 0;">
        {{ __('Silakan klik tombol di bawah ini untuk membuat password baru:') }}
    </p>

    <x-email.tombol :url="$url">{{ __('Reset Password →') }}</x-email.tombol>

    <x-email.tautan-cadangan :url="$url" />

    <p style="margin:18px 0 14px 0;">
        {{ __('Tautan ini berlaku :menit menit dan hanya bisa dipakai satu kali.', ['menit' => $berlakuMenit]) }}
    </p>

    <p style="margin:0 0 14px 0;">
        {{ __('Jika Anda tidak merasa melakukan permintaan reset password, silakan abaikan email ini. Password akun Anda tidak akan berubah tanpa tindakan dari Anda.') }}
    </p>

    <p style="margin:0 0 14px 0;">
        {{ __('Jika membutuhkan bantuan, silakan hubungi kami melalui :email.', ['email' => config('ppid.kontak.email')]) }}
    </p>

    <p style="margin:0 0 14px 0;">{{ __('Terima kasih.') }}</p>

    <p style="margin:0;">
        {{ __('Salam,') }}<br>
        <strong>{{ __('PPID Food Station') }}</strong>
    </p>
</x-email.layout>
