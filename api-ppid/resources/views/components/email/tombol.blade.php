@props([
    'url',
    /* `utama` hijau (aksi yang diminta), `netral` untuk tautan sekunder. */
    'warna' => 'utama',
])

@php
    $latar = $warna === 'netral' ? '#5B6660' : '#175A3C';
@endphp

{{--
    Tombol dibuat dari <a> berlatar, bukan <button>: form dan JavaScript tidak
    berjalan di klien email. Lebar tabelnya dikunci "auto" supaya tombol tidak
    melebar penuh di Outlook.
--}}
<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:24px 0;">
    <tr>
        <td align="center" bgcolor="{{ $latar }}" style="border-radius:6px;">
            <a href="{{ $url }}"
               style="display:inline-block;padding:13px 28px;font-family:Arial,Helvetica,sans-serif;font-size:15px;font-weight:bold;line-height:20px;color:#FFFFFF;text-decoration:none;border-radius:6px;">
                {{ $slot }}
            </a>
        </td>
    </tr>
</table>
