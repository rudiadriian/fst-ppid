@props(['url'])

{{--
    Sebagian klien email memblokir tombol atau memotong tautannya. URL mentah
    ditampilkan apa adanya supaya masih bisa disalin manual.
--}}
<p style="margin:0 0 6px 0;font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:20px;color:#5B6660;">
    {{ __('Jika tombol di atas tidak berfungsi, silakan salin dan buka tautan berikut di peramban Anda:') }}
</p>
<p style="margin:0;font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:20px;word-break:break-all;">
    <a href="{{ $url }}" style="color:#175A3C;">{{ $url }}</a>
</p>
