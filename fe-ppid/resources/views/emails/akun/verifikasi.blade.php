{{--
    Konfirmasi alamat email pendaftar baru.
    Isinya mengikuti naskah "Konfirmasi Email" dari PPID Food Station.
--}}
<x-email.layout :judul="__('Konfirmasi Email')"
                :preheader="__('Verifikasi alamat email Anda untuk mengaktifkan akun PPID Food Station.')">

    <p style="margin:0 0 14px 0;">{{ __('Halo :nama,', ['nama' => $nama]) }}</p>

    <p style="margin:0 0 14px 0;">{{ __('Terima kasih sudah mendaftar di PPID Food Station.') }}</p>

    <p style="margin:0 0 4px 0;">
        {{ __('Silakan verifikasi dan konfirmasi alamat email Anda dengan mengklik tombol di bawah ini:') }}
    </p>

    <x-email.tombol :url="$url">{{ __('Konfirmasi Akun →') }}</x-email.tombol>

    <x-email.tautan-cadangan :url="$url" />

    <p style="margin:18px 0 14px 0;">
        {{ __('Tautan ini berlaku :jam jam sejak email dikirim. Setelah lewat, mintalah tautan baru dari halaman verifikasi.', ['jam' => $berlakuJam]) }}
    </p>

    <p style="margin:0 0 14px 0;">
        {{ __('Jika Anda tidak merasa melakukan pendaftaran, silakan abaikan email ini atau hubungi kami melalui :email.', ['email' => config('ppid.kontak.email')]) }}
    </p>

    <p style="margin:0 0 14px 0;">{{ __('Terima kasih atas perhatian dan kerja samanya.') }}</p>

    <p style="margin:0;">
        {{ __('Salam,') }}<br>
        <strong>{{ __('PPID Food Station') }}</strong>
    </p>
</x-email.layout>
