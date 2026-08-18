@props([
    /* Judul besar di dalam kartu — biasanya sama dengan subjek emailnya. */
    'judul' => '',
    /* Baris pratinjau yang tampil di daftar inbox sebelum email dibuka. */
    'preheader' => '',
])

@php
    /*
     * Semua gaya ditulis inline: Gmail, Outlook, dan sebagian besar klien
     * email membuang <style> di <head>, jadi kelas CSS tidak bisa dipakai.
     * Tata letaknya pakai <table> karena flex/grid tidak didukung merata.
     */
    $instansi = config('ppid.kontak.instansi');
    $emailKontak = config('ppid.kontak.email');
    /*
     * Alamat situs pemohon — bukan config('app.url'), karena berkas ini juga
     * dipakai api-ppid yang app.url-nya menunjuk API, bukan portal.
     */
    $situs = rtrim(config('ppid.situs_url') ?: config('app.url'), '/');

    $warna = [
        'kertas' => '#F3ECDD',
        'kartu' => '#FFFFFF',
        'garis' => '#E9DFC9',
        'utama' => '#175A3C',
        'utamaGelap' => '#10462F',
        'teks' => '#1F2A24',
        'redup' => '#5B6660',
    ];
@endphp
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
       style="margin:0;padding:0;background-color:{{ $warna['kertas'] }};">
    <tr>
        <td align="center" style="padding:24px 12px;">

            {{-- Preheader: dibaca klien email untuk pratinjau, tidak ikut tampil. --}}
            @if ($preheader !== '')
                <div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;height:0;width:0;">
                    {{ $preheader }}
                </div>
            @endif

            <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0"
                   style="width:100%;max-width:600px;background-color:{{ $warna['kartu'] }};border:1px solid {{ $warna['garis'] }};border-radius:10px;overflow:hidden;">

                {{-- Kop: logo Food Station. --}}
                <tr>
                    <td align="center" style="padding:28px 24px 8px 24px;">
                        <a href="{{ $situs }}" style="text-decoration:none;">
                            <img src="{{ $situs }}/assets/images/logo/logo_fs.png"
                                 alt="{{ $instansi }}"
                                 width="180"
                                 style="display:block;width:180px;max-width:70%;height:auto;border:0;">
                        </a>
                    </td>
                </tr>

                @if ($judul !== '')
                    <tr>
                        <td style="padding:8px 32px 0 32px;">
                            <h1 style="margin:0;font-family:Arial,Helvetica,sans-serif;font-size:20px;line-height:28px;font-weight:bold;color:{{ $warna['utama'] }};">
                                {{ $judul }}
                            </h1>
                        </td>
                    </tr>
                @endif

                <tr>
                    <td style="padding:16px 32px 28px 32px;font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:24px;color:{{ $warna['teks'] }};">
                        {{ $slot }}
                    </td>
                </tr>

                {{-- Kaki: penanda pengirim + peringatan email otomatis. --}}
                <tr>
                    <td style="padding:20px 32px;background-color:{{ $warna['kertas'] }};border-top:1px solid {{ $warna['garis'] }};font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:20px;color:{{ $warna['redup'] }};">
                        <p style="margin:0 0 6px 0;color:{{ $warna['utamaGelap'] }};font-weight:bold;">
                            {{ __('PPID :instansi', ['instansi' => $instansi]) }}
                        </p>
                        <p style="margin:0 0 6px 0;">
                            {{ __('Pertanyaan layanan informasi publik:') }}
                            <a href="mailto:{{ $emailKontak }}" style="color:{{ $warna['utama'] }};">{{ $emailKontak }}</a>
                            &middot;
                            <a href="{{ $situs }}" style="color:{{ $warna['utama'] }};">{{ $situs }}</a>
                        </p>
                        <p style="margin:0;">
                            {{ __('Email ini dikirim otomatis oleh sistem PPID. Mohon tidak membalas ke alamat pengirim.') }}
                        </p>
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>
