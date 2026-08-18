@props([
    /* array<string label, string nilai>; nilai kosong dilewati. */
    'baris' => [],
])

@php
    $baris = array_filter($baris, fn ($nilai) => filled($nilai));
@endphp

@if ($baris !== [])
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           style="margin:20px 0;background-color:#FDFAF3;border:1px solid #E9DFC9;border-radius:8px;">
        @foreach ($baris as $label => $nilai)
            <tr>
                <td style="padding:10px 16px;font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:20px;color:#5B6660;width:38%;vertical-align:top;@if (!$loop->first) border-top:1px solid #E9DFC9; @endif">
                    {{ $label }}
                </td>
                <td style="padding:10px 16px;font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:20px;color:#1F2A24;font-weight:bold;vertical-align:top;@if (!$loop->first) border-top:1px solid #E9DFC9; @endif">
                    {{ $nilai }}
                </td>
            </tr>
        @endforeach
    </table>
@endif
