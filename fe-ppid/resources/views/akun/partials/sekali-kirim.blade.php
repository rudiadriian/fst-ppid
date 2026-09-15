{{--
    Penanda kiriman tunggal untuk formulir pengajuan.

    Formulirnya cukup diberi atribut `data-sekali-kirim`; pengunci tombolnya
    dipasang `resources/js/app.js`. Isian di bawah ini yang menahan berkas
    kedua di server, lihat App\Support\SekaliKirim.
--}}
<input type="hidden" name="{{ \App\Support\SekaliKirim::FIELD }}" value="{{ \App\Support\SekaliKirim::token() }}">
