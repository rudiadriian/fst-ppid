<?php

/*
|--------------------------------------------------------------------------
| Server pengembangan cepat untuk api-ppid
|--------------------------------------------------------------------------
|
| `php artisan serve` polos di mesin Windows/XAMPP ini menghabiskan ~340 ms per
| permintaan hanya untuk membaca dan mengompilasi ulang berkas Laravel, sebelum
| satu baris kode aplikasi dijalankan. Angka itu terukur: satu permintaan ke
| /api/v1/health — rute yang tidak menyentuh basis data sama sekali — memakan
| 0,34 detik, sementara permintaan yang sama bila ditangani di dalam proses
| yang sudah panas hanya 17 ms.
|
| Skrip ini menyalakan tiga hal yang menghapus biaya tersebut:
|
|   1. OPcache, lewat opsi `-d` pada `php -S` yang dijalankan langsung dari
|      sini. php.ini milik XAMPP tidak disentuh, jadi proyek PHP lain di mesin
|      ini tidak ikut terpengaruh.
|   2. Cache konfigurasi — 30-an berkas config digabung jadi satu.
|   3. Cache rute — seluruh berkas rute digabung jadi satu tabel.
|
| Keduanya dibangun ulang setiap kali skrip dijalankan, sehingga perubahan pada
| `.env`, `config/*.php`, atau `routes/*.php` cukup diikuti restart server —
| tidak perlu mengingat `php artisan config:clear`.
|
| Pemakaian:  composer serve            (port 8001)
|             composer serve -- 8010    (port lain)
*/

$akar = dirname(__DIR__);
$php = PHP_BINARY;
$port = $argv[1] ?? '8001';

if (!preg_match('/^\d{2,5}$/', (string) $port)) {
    fwrite(STDERR, "Port tidak valid: {$port}\n");
    exit(1);
}

function jalankan(string $php, string $akar, array $argumen): int
{
    $perintah = array_merge([$php, $akar.DIRECTORY_SEPARATOR.'artisan'], $argumen);
    $baris = implode(' ', array_map('escapeshellarg', $perintah));

    passthru($baris, $kode);

    return $kode;
}

// Cache lama dibuang dulu supaya perubahan .env/config/routes sejak run
// terakhir benar-benar terbaca, bukan tertinggal di cache basi.
jalankan($php, $akar, ['optimize:clear']);

foreach (['config:cache', 'route:cache'] as $perintah) {
    if (jalankan($php, $akar, [$perintah]) !== 0) {
        fwrite(STDERR, "Gagal menjalankan {$perintah}.\n");
        exit(1);
    }
}

/*
 * Cache dibuang lagi begitu server berhenti.
 *
 * Tanpa ini, cache rute yang tertinggal akan ikut terbaca oleh siapa pun yang
 * lain kali menjalankan `php artisan serve` polos — dan rute yang baru
 * ditambahkan sejak itu diam-diam tidak terdaftar. Lebih baik keadaan diam
 * proyek ini tetap "tanpa cache".
 */
register_shutdown_function(static function () use ($php, $akar) {
    jalankan($php, $akar, ['optimize:clear']);
});

/*
 * Servernya dijalankan langsung, bukan lewat `php artisan serve`.
 *
 * `ServeCommand` menyaring environment yang diteruskan ke `php -S` memakai
 * daftar-putih `$passthroughVariables`, dan `PHP_INI_SCAN_DIR` tidak ada di
 * daftar itu — nilainya justru dihapus dari proses anak. Akibatnya OPcache
 * mati persis di proses yang melayani permintaan, tanpa tanda apa pun; yang
 * terlihat cuma server yang lambat lagi.
 *
 * Memanggil `php -S` sendiri menghapus perantara itu: berkas ini yang
 * menentukan ini mana yang dipakai, dan `public/../server.php` bawaan Laravel
 * tetap dipakai sebagai router supaya berkas statis di `public/` tetap
 * dilayani seperti biasa.
 */
$router = $akar.'/vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php';

$perintah = implode(' ', array_map('escapeshellarg', [
    $php,
    '-d', 'zend_extension=opcache',
    '-d', 'opcache.enable=1',
    '-d', 'opcache.enable_cli=1',
    '-d', 'opcache.memory_consumption=256',
    '-d', 'opcache.interned_strings_buffer=16',
    '-d', 'opcache.max_accelerated_files=20000',
    '-d', 'opcache.validate_timestamps=1',
    '-d', 'opcache.revalidate_freq=2',
    '-d', 'opcache.save_comments=1',
    '-S', '127.0.0.1:'.$port,
    '-t', $akar.DIRECTORY_SEPARATOR.'public',
    $router,
]));

echo PHP_EOL."OPcache + cache config/rute aktif. Server berjalan di http://127.0.0.1:{$port}".PHP_EOL;
echo 'Ubah .env / config / routes? Hentikan server (Ctrl+C) lalu jalankan lagi.'.PHP_EOL.PHP_EOL;

// `server.php` menentukan akar publik dari `getcwd()`, bukan dari `-t`, jadi
// direktori kerjanya harus benar-benar dipindah ke `public/`.
chdir($akar.DIRECTORY_SEPARATOR.'public');

passthru($perintah, $kode);

exit((int) $kode);
