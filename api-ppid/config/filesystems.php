<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        /*
         * Disk berkas publik PPID.
         *
         * Root-nya sengaja diarahkan ke folder storage milik `fe-ppid` supaya
         * berkas yang diunggah lewat CMS langsung bisa disajikan situs publik
         * tanpa proses sinkronisasi. Kalau nanti dipisah ke server berbeda,
         * cukup ganti MEDIA_ROOT/MEDIA_URL (atau tukar ke driver s3).
         */
        'media' => [
            'driver' => 'local',
            'root' => env('MEDIA_ROOT', base_path('../fe-ppid/storage/app/public')),
            'url' => env('MEDIA_URL', 'http://localhost:8000/storage'),
            'visibility' => 'public',
            'throw' => false,
        ],

        /*
         * Berkas dokumen yang unduhannya terbatas (langkah 83).
         *
         * Sengaja **di luar** `storage/app/public` milik fe-ppid. Folder itu
         * ditautkan ke `public/storage` lewat `php artisan storage:link`,
         * sehingga isinya dilayani web server secara langsung — tidak ada rute
         * PHP yang sempat berjalan, jadi tidak ada tempat memasang pemeriksaan
         * hak. Selama berkasnya di sana, pemeriksaan apa pun bisa dilewati
         * cukup dengan menyalin alamat berkasnya.
         *
         * Berkas di sini hanya keluar lewat `DokumenInformasiController` milik
         * fe-ppid, yang memeriksa dulu apakah permohonan orangnya sudah
         * disetujui petugas.
         */
        'dokumen_terbatas' => [
            'driver' => 'local',
            'root' => env('DOKUMEN_TERBATAS_ROOT', base_path('../fe-ppid/storage/app/dokumen-terbatas')),
            'visibility' => 'private',
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
