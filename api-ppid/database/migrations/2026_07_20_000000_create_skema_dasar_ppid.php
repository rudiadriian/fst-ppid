<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Skema dasar seluruh modul PPID.
 *
 * Tabel-tabel ini semula dibuat lewat DDL manual di luar Git, sehingga proyek
 * tidak pernah bisa dipasang ulang dari nol — migrasi yang ada hanya menambal
 * (`add_*`, `make_*`). Berkas ini menutup lubang itu: isinya keadaan tabel
 * **sebelum** semua migrasi tambalan, direkonstruksi dari model Eloquent,
 * aturan validasi controller, seeder, dan pemakaian kolom di kedua aplikasi
 * (api-ppid & fe-ppid).
 *
 * Karena itu kolom yang ditambahkan migrasi setelah tanggal ini sengaja TIDAK
 * ada di sini — biar migrasi tambalannya yang memasang, persis seperti urutan
 * aslinya:
 *   - `users.photo_url|shortcuts|settings`      → 2026_07_27_000001
 *   - `informasi_publik.tautan`                 → 2026_08_07_000001
 *   - `pemohon.password|email_verified_at`      → 2026_08_10_000001
 *   - `pemohon.foto|file_ktp|status_verifikasi` → 2026_08_10_000002
 *   - `struktur_organisasi.parent_id|tipe_node` → 2026_08_10_000003
 *   - `regulasi.ringkasan|uploaded_by`          → 2026_08_11_000001
 *   - kolom `*_en` seluruh modul konten         → 2026_08_12_000001
 *   - `banner_slider.ringkasan`                 → 2026_08_13_000002
 *   - jejak `created_by|updated_by|deleted_at`  → 2026_08_14_000001
 *   - `pemohon.jumlah_ditolak|catatan_…`        → 2026_08_15_000002
 *
 * Seluruh blok dijaga `hasTable`/`hasColumn` supaya aman dijalankan di basis
 * data yang tabelnya sudah terlanjur ada sebagian — termasuk `users` bawaan
 * Laravel yang biasanya sudah terpasang lebih dulu.
 */
return new class extends Migration
{
    /**
     * CHECK constraint daftar nilai, ditulis manual.
     *
     * Laravel tidak punya `enum` portabel untuk PostgreSQL, dan migrasi
     * tambalan berikutnya memang memperluas daftar ini lewat
     * `DROP CONSTRAINT IF EXISTS … ADD CONSTRAINT` dengan nama yang sama.
     */
    private function check(string $tabel, string $nama, string $kolom, array $nilai): void
    {
        $daftar = collect($nilai)->map(fn ($v) => "'".$v."'")->implode(', ');

        DB::statement("ALTER TABLE {$tabel} DROP CONSTRAINT IF EXISTS {$nama}");
        DB::statement("ALTER TABLE {$tabel} ADD CONSTRAINT {$nama} CHECK ({$kolom} IS NULL OR {$kolom}::text = ANY (ARRAY[{$daftar}]::text[]))");
    }

    public function up(): void
    {
        $this->tabelAkses();
        $this->tabelInformasi();
        $this->tabelLayanan();
        $this->tabelKonten();
        $this->tabelSitus();
        $this->tabelSistem();
        $this->pemicu();
    }

    /* ---------------------------------------------------------------- akses */

    private function tabelAkses(): void
    {
        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150);
                $table->string('slug', 150)->unique();
            });
        }

        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('slug', 100)->unique();
                $table->text('description')->nullable();
            });
        }

        /*
         * `users` umumnya sudah dibuat migrasi bawaan Laravel dengan kolom
         * seadanya (name/email/password). Kolom khas panel PPID ditambahkan
         * di sini supaya basis data lama maupun baru berakhir sama.
         */
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150);
                $table->string('email', 150)->unique();
                $table->timestampTz('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role_id')) {
                $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();
            }

            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 30)->nullable();
            }

            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }

            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestampTz('last_login_at')->nullable();
            }
        });

        /* Daftar modul panel; di-seed ModulSistemSeeder, bukan diisi operator. */
        if (!Schema::hasTable('modul_sistem')) {
            Schema::create('modul_sistem', function (Blueprint $table) {
                $table->id();
                $table->foreignId('parent_id')->nullable()->constrained('modul_sistem')->nullOnDelete();
                $table->string('nama', 100);
                $table->string('slug', 100)->unique();
                $table->string('icon', 100)->nullable();
                $table->string('route', 150)->nullable();
                $table->integer('urutan')->default(0);
                $table->boolean('is_active')->default(true);
            });
        }

        /* Hak akses per role per modul — sumber kebenaran menu & tombol panel. */
        if (!Schema::hasTable('role_modul_akses')) {
            Schema::create('role_modul_akses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
                $table->foreignId('modul_id')->constrained('modul_sistem')->cascadeOnDelete();
                $table->boolean('can_view')->default(false);
                $table->boolean('can_create')->default(false);
                $table->boolean('can_edit')->default(false);
                $table->boolean('can_delete')->default(false);
                $table->boolean('can_approve')->default(false);
                $table->boolean('can_export')->default(false);

                $table->unique(['role_id', 'modul_id']);
            });
        }
    }

    /* ----------------------------------------------------------- informasi */

    private function tabelInformasi(): void
    {
        if (!Schema::hasTable('kategori_informasi')) {
            Schema::create('kategori_informasi', function (Blueprint $table) {
                $table->id();
                $table->foreignId('parent_id')->nullable()->constrained('kategori_informasi')->nullOnDelete();
                $table->string('nama', 150);
                $table->string('slug', 150)->unique();
                $table->text('deskripsi')->nullable();
                $table->integer('urutan')->default(0);
                $table->boolean('is_active')->default(true);
            });
        }

        if (!Schema::hasTable('informasi_publik')) {
            Schema::create('informasi_publik', function (Blueprint $table) {
                $table->id();
                $table->foreignId('kategori_id')->nullable()->constrained('kategori_informasi')->nullOnDelete();
                $table->string('judul', 255);
                $table->string('slug', 255)->unique();
                $table->text('ringkasan')->nullable();
                $table->text('konten')->nullable();
                $table->string('nomor_klasifikasi', 100)->nullable();
                $table->date('tanggal_publikasi')->nullable();
                $table->string('status', 20)->default('draft');
                $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestampTz('reviewed_at')->nullable();
                $table->integer('views_count')->default(0);

                $table->index(['status', 'tanggal_publikasi']);
            });

            // Kolom pencarian penuh; diisi trigger, tidak pernah oleh aplikasi.
            DB::statement('ALTER TABLE informasi_publik ADD COLUMN IF NOT EXISTS search_vector tsvector');
            DB::statement('CREATE INDEX IF NOT EXISTS informasi_publik_search_idx ON informasi_publik USING gin (search_vector)');

            $this->check('informasi_publik', 'informasi_publik_status_check', 'status', ['draft', 'published', 'archived']);
        }

        if (!Schema::hasTable('informasi_publik_files')) {
            Schema::create('informasi_publik_files', function (Blueprint $table) {
                $table->id();
                $table->foreignId('informasi_publik_id')->constrained('informasi_publik')->cascadeOnDelete();
                $table->string('nama_file', 255);
                $table->string('path_file', 500);
                $table->bigInteger('ukuran_file')->nullable();
                $table->string('tipe_file', 100)->nullable();
                $table->integer('urutan')->default(0);
                $table->timestampTz('created_at')->nullable();
            });
        }

        /*
         * Permintaan tautan unduh berkas informasi publik. `token_unduhan`
         * adalah kredensial akses berkasnya, karena itu unik dan berumur.
         */
        if (!Schema::hasTable('permintaan_unduhan')) {
            Schema::create('permintaan_unduhan', function (Blueprint $table) {
                $table->id();
                $table->foreignId('informasi_publik_file_id')->constrained('informasi_publik_files')->cascadeOnDelete();
                $table->string('nama', 150);
                $table->string('email', 150);
                $table->string('telepon', 30)->nullable();
                $table->string('token_unduhan', 100)->nullable()->unique();
                $table->timestampTz('token_expired_at')->nullable();
                $table->timestampTz('downloaded_at')->nullable();
                $table->timestampTz('created_at')->nullable();
            });
        }

        if (!Schema::hasTable('informasi_dikecualikan')) {
            Schema::create('informasi_dikecualikan', function (Blueprint $table) {
                $table->id();
                $table->string('judul', 255);
                $table->string('slug', 255)->unique();
                $table->text('ringkasan')->nullable();
                // Dilonggarkan menjadi nullable oleh 2026_08_11_000002.
                $table->text('alasan_pengecualian');
                $table->text('dasar_hukum_pengecualian')->nullable();
                $table->string('jangka_waktu_pengecualian', 150)->nullable();
                $table->date('tanggal_penetapan')->nullable();
                $table->foreignId('pejabat_penetap')->nullable()->constrained('users')->nullOnDelete();
                $table->string('file_surat_penetapan', 500)->nullable();
                $table->string('status', 20)->default('draft');
            });

            $this->check('informasi_dikecualikan', 'informasi_dikecualikan_status_check', 'status', ['draft', 'published', 'archived']);
        }
    }

    /* -------------------------------------------------------------- layanan */

    private function tabelLayanan(): void
    {
        /*
         * Satu baris `pemohon` = satu orang. Baris yang sama dipakai panel
         * (data pemohon) dan situs (akun Portal Pengguna), sehingga riwayat
         * permohonan lama tetap menempel setelah orangnya mendaftar akun.
         */
        if (!Schema::hasTable('pemohon')) {
            Schema::create('pemohon', function (Blueprint $table) {
                $table->id();
                $table->string('nik', 32)->nullable();
                $table->string('nama', 150);
                $table->string('email', 150)->nullable()->unique();
                $table->string('no_hp', 30)->nullable();
                $table->text('alamat')->nullable();
                $table->string('pekerjaan', 100)->nullable();
                $table->string('jenis_pemohon', 20)->nullable();
                $table->string('nama_lembaga', 150)->nullable();
            });

            // Daftar nilainya diperluas 2026_08_10_000002 untuk Portal Pengguna.
            $this->check('pemohon', 'pemohon_jenis_pemohon_check', 'jenis_pemohon', ['pribadi', 'instansi', 'kelompok']);
        }

        if (!Schema::hasTable('permohonan_informasi')) {
            Schema::create('permohonan_informasi', function (Blueprint $table) {
                $table->id();
                // Diisi trigger bila pemanggilnya tidak menyertakan nomor.
                $table->string('kode_permohonan', 50)->nullable()->unique();
                $table->foreignId('pemohon_id')->constrained('pemohon')->cascadeOnDelete();
                $table->foreignId('kategori_id')->nullable()->constrained('kategori_informasi')->nullOnDelete();
                $table->text('rincian_informasi');
                $table->text('tujuan_penggunaan')->nullable();
                $table->string('format_informasi', 20)->nullable();
                $table->string('cara_pengiriman', 20)->nullable();
                $table->string('status', 20)->default('diajukan');
                $table->text('alasan_penolakan')->nullable();
                $table->timestampTz('tanggal_permohonan')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->timestampTz('batas_waktu_tanggapan')->nullable();
                $table->timestampTz('tanggal_tanggapan')->nullable();
                $table->foreignId('ditangani_oleh')->nullable()->constrained('users')->nullOnDelete();
                $table->boolean('tampil_di_register_publik')->default(false);

                $table->index(['status', 'tanggal_permohonan']);
            });

            $this->check('permohonan_informasi', 'permohonan_informasi_format_informasi_check', 'format_informasi', ['softcopy', 'hardcopy']);
            $this->check('permohonan_informasi', 'permohonan_informasi_cara_pengiriman_check', 'cara_pengiriman', ['email', 'ambil_langsung', 'pos']);
            // `revisi` ditambahkan 2026_08_10_000002.
            $this->check('permohonan_informasi', 'permohonan_informasi_status_check', 'status', [
                'diajukan', 'diverifikasi', 'diproses', 'menunggu_approval',
                'disetujui', 'ditolak', 'ditolak_sebagian', 'selesai', 'kedaluwarsa',
            ]);
        }

        if (!Schema::hasTable('permohonan_files')) {
            Schema::create('permohonan_files', function (Blueprint $table) {
                $table->id();
                $table->foreignId('permohonan_id')->constrained('permohonan_informasi')->cascadeOnDelete();
                $table->string('nama_file', 255);
                $table->string('path_file', 500);
                $table->string('tipe_file', 100)->nullable();
                $table->timestampTz('created_at')->nullable();
            });
        }

        if (!Schema::hasTable('permohonan_tanggapan_files')) {
            Schema::create('permohonan_tanggapan_files', function (Blueprint $table) {
                $table->id();
                $table->foreignId('permohonan_id')->constrained('permohonan_informasi')->cascadeOnDelete();
                $table->string('nama_file', 255);
                $table->string('path_file', 500);
                $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestampTz('created_at')->nullable();
            });
        }

        /* Jejak perpindahan status; sumber linimasa pada rincian permohonan. */
        if (!Schema::hasTable('permohonan_log_status')) {
            Schema::create('permohonan_log_status', function (Blueprint $table) {
                $table->id();
                $table->foreignId('permohonan_id')->constrained('permohonan_informasi')->cascadeOnDelete();
                $table->string('status_sebelumnya', 20)->nullable();
                $table->string('status_baru', 20);
                $table->text('catatan')->nullable();
                $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestampTz('created_at')->nullable();
            });
        }

        if (!Schema::hasTable('approval_permohonan')) {
            Schema::create('approval_permohonan', function (Blueprint $table) {
                $table->id();
                $table->foreignId('permohonan_id')->constrained('permohonan_informasi')->cascadeOnDelete();
                $table->foreignId('disiapkan_oleh')->nullable()->constrained('users')->nullOnDelete();
                $table->timestampTz('tanggal_diajukan')->nullable();
                $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
                $table->string('status_approval', 20)->default('pending');
                $table->text('catatan_approval')->nullable();
                $table->timestampTz('tanggal_approval')->nullable();
                $table->timestampTz('created_at')->nullable();
            });

            $this->check('approval_permohonan', 'approval_permohonan_status_approval_check', 'status_approval', ['pending', 'disetujui', 'ditolak', 'revisi']);
        }

        if (!Schema::hasTable('keberatan_informasi')) {
            Schema::create('keberatan_informasi', function (Blueprint $table) {
                $table->id();
                $table->foreignId('permohonan_id')->constrained('permohonan_informasi')->cascadeOnDelete();
                $table->foreignId('pemohon_id')->constrained('pemohon')->cascadeOnDelete();
                $table->string('jenis_keberatan', 50);
                $table->text('alasan_keberatan');
                $table->string('status', 20)->default('diajukan');
                $table->text('tanggapan_atasan_ppid')->nullable();
                $table->foreignId('ditangani_oleh')->nullable()->constrained('users')->nullOnDelete();
                $table->timestampTz('tanggal_keberatan')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->timestampTz('tanggal_tanggapan')->nullable();
            });

            $this->check('keberatan_informasi', 'keberatan_informasi_jenis_keberatan_check', 'jenis_keberatan', [
                'permohonan_ditolak', 'informasi_tidak_disediakan', 'permintaan_tidak_ditanggapi',
                'informasi_tidak_sesuai', 'biaya_tidak_wajar', 'melebihi_jangka_waktu',
            ]);

            // `revisi` & `menunggu_approval` ditambahkan 2026_08_10_000002.
            $this->check('keberatan_informasi', 'keberatan_informasi_status_check', 'status', ['diajukan', 'diproses', 'ditolak', 'selesai']);
        }

        if (!Schema::hasTable('keberatan_files')) {
            Schema::create('keberatan_files', function (Blueprint $table) {
                $table->id();
                $table->foreignId('keberatan_id')->constrained('keberatan_informasi')->cascadeOnDelete();
                $table->string('nama_file', 255);
                $table->string('path_file', 500);
                $table->timestampTz('created_at')->nullable();
            });
        }

        /* Satu permohonan hanya boleh dinilai sekali. */
        if (!Schema::hasTable('survey_kepuasan')) {
            Schema::create('survey_kepuasan', function (Blueprint $table) {
                $table->id();
                $table->foreignId('permohonan_id')->constrained('permohonan_informasi')->cascadeOnDelete();
                $table->smallInteger('rating');
                $table->text('komentar')->nullable();

                $table->unique('permohonan_id');
            });

            DB::statement('ALTER TABLE survey_kepuasan DROP CONSTRAINT IF EXISTS survey_kepuasan_rating_check');
            DB::statement('ALTER TABLE survey_kepuasan ADD CONSTRAINT survey_kepuasan_rating_check CHECK (rating BETWEEN 1 AND 5)');
        }

        if (!Schema::hasTable('laporan_layanan')) {
            Schema::create('laporan_layanan', function (Blueprint $table) {
                $table->id();
                $table->string('tipe_laporan', 30)->nullable();
                $table->string('judul', 255);
                $table->integer('tahun')->nullable();
                $table->string('periode', 30)->nullable();
                $table->integer('jumlah_permohonan_masuk')->default(0);
                $table->integer('jumlah_dikabulkan')->default(0);
                $table->integer('jumlah_ditolak')->default(0);
                $table->integer('jumlah_ditolak_sebagian')->default(0);
                $table->integer('jumlah_keberatan')->default(0);
                $table->decimal('rata_rata_hari_respon', 6, 2)->nullable();
                $table->text('ringkasan')->nullable();
                $table->string('file_laporan', 500)->nullable();
                $table->string('status', 20)->default('draft');
                $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            });

            $this->check('laporan_layanan', 'laporan_layanan_status_check', 'status', ['draft', 'published', 'archived']);
        }
    }

    /* --------------------------------------------------------------- konten */

    private function tabelKonten(): void
    {
        if (!Schema::hasTable('kategori_berita')) {
            Schema::create('kategori_berita', function (Blueprint $table) {
                $table->id();
                $table->string('nama', 150);
                $table->string('slug', 150)->unique();
            });
        }

        if (!Schema::hasTable('berita')) {
            Schema::create('berita', function (Blueprint $table) {
                $table->id();
                $table->foreignId('kategori_berita_id')->nullable()->constrained('kategori_berita')->nullOnDelete();
                $table->string('judul', 255);
                $table->string('slug', 255)->unique();
                $table->string('thumbnail', 500)->nullable();
                $table->text('ringkasan')->nullable();
                $table->text('konten')->nullable();
                $table->date('tanggal_publikasi')->nullable();
                $table->string('status', 20)->default('draft');
                $table->foreignId('penulis')->nullable()->constrained('users')->nullOnDelete();
                $table->integer('views_count')->default(0);

                $table->index(['status', 'tanggal_publikasi']);
            });

            $this->check('berita', 'berita_status_check', 'status', ['draft', 'published', 'archived']);
        }

        if (!Schema::hasTable('galeri')) {
            Schema::create('galeri', function (Blueprint $table) {
                $table->id();
                $table->string('judul', 255);
                $table->string('tipe', 20)->default('foto');
                $table->string('path_file', 500);
                $table->text('deskripsi')->nullable();
                $table->date('tanggal')->nullable();
            });

            $this->check('galeri', 'galeri_tipe_check', 'tipe', ['foto', 'video']);
        }

        if (!Schema::hasTable('faq')) {
            Schema::create('faq', function (Blueprint $table) {
                $table->id();
                $table->text('pertanyaan');
                $table->text('jawaban');
                $table->string('kategori', 100)->nullable();
                $table->integer('urutan')->default(0);
                $table->boolean('is_active')->default(true);
            });
        }

        if (!Schema::hasTable('banner_slider')) {
            Schema::create('banner_slider', function (Blueprint $table) {
                $table->id();
                $table->string('judul', 255);
                $table->string('gambar', 500);
                $table->string('link', 500)->nullable();
                $table->integer('urutan')->default(0);
                $table->boolean('is_active')->default(true);
                $table->date('tanggal_mulai')->nullable();
                $table->date('tanggal_selesai')->nullable();
            });
        }

        if (!Schema::hasTable('struktur_organisasi')) {
            Schema::create('struktur_organisasi', function (Blueprint $table) {
                $table->id();
                $table->string('nama', 150);
                $table->string('jabatan', 150);
                $table->string('foto', 500)->nullable();
                $table->integer('urutan')->default(0);
                $table->text('deskripsi')->nullable();
                $table->boolean('is_active')->default(true);
            });
        }

        /*
         * `updated_by` sudah ada di tabel ini sejak awal — migrasi jejak
         * 2026_08_14_000001 secara eksplisit tidak melepasnya saat rollback.
         */
        if (!Schema::hasTable('halaman_statis')) {
            Schema::create('halaman_statis', function (Blueprint $table) {
                $table->id();
                $table->string('judul', 255);
                $table->string('slug', 150)->unique();
                $table->text('konten')->nullable();
                $table->boolean('is_active')->default(true);
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            });
        }

        if (!Schema::hasTable('regulasi')) {
            Schema::create('regulasi', function (Blueprint $table) {
                $table->id();
                $table->string('kategori', 100)->nullable();
                $table->string('judul', 255);
                $table->string('nomor_peraturan', 150)->nullable();
                $table->string('jenis_peraturan', 100)->nullable();
                $table->integer('tahun')->nullable();
                $table->string('file_path', 500)->nullable();
                $table->date('tanggal_berlaku')->nullable();
            });
        }
    }

    /* ----------------------------------------------------------------- situs */

    private function tabelSitus(): void
    {
        if (!Schema::hasTable('menu_navigasi')) {
            Schema::create('menu_navigasi', function (Blueprint $table) {
                $table->id();
                $table->foreignId('parent_id')->nullable()->constrained('menu_navigasi')->nullOnDelete();
                $table->string('label', 150);
                $table->string('url', 500)->nullable();
                $table->integer('urutan')->default(0);
                $table->string('target', 20)->default('_self');
                $table->boolean('is_active')->default(true);
            });

            $this->check('menu_navigasi', 'menu_navigasi_target_check', 'target', ['_self', '_blank']);
        }

        if (!Schema::hasTable('tautan_terkait')) {
            Schema::create('tautan_terkait', function (Blueprint $table) {
                $table->id();
                $table->string('nama', 150);
                $table->string('url', 500);
                $table->string('logo', 500)->nullable();
                $table->integer('urutan')->default(0);
                $table->boolean('is_active')->default(true);
            });
        }

        /* Pengaturan situs berbentuk key–value; dibaca situs lewat Cms::pengaturan(). */
        if (!Schema::hasTable('pengaturan_situs')) {
            Schema::create('pengaturan_situs', function (Blueprint $table) {
                $table->id();
                $table->string('key', 150)->unique();
                $table->text('value')->nullable();
                $table->string('group_name', 100)->nullable();
            });
        }
    }

    /* --------------------------------------------------------------- sistem */

    private function tabelSistem(): void
    {
        if (!Schema::hasTable('notifikasi')) {
            Schema::create('notifikasi', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('type', 50)->nullable();
                $table->text('message');
                $table->boolean('is_read')->default(false);
                $table->jsonb('data')->nullable();
                $table->timestampTz('created_at')->nullable();

                $table->index(['user_id', 'is_read']);
            });
        }

        /*
         * Jejak audit tidak ikut terhapus bersama penggunanya: `user_id`
         * dikosongkan, barisnya tetap tinggal sebagai bukti.
         */
        if (!Schema::hasTable('audit_log')) {
            Schema::create('audit_log', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action', 50);
                $table->string('model_type', 150)->nullable();
                $table->unsignedBigInteger('model_id')->nullable();
                $table->jsonb('old_values')->nullable();
                $table->jsonb('new_values')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestampTz('created_at')->nullable();

                $table->index(['model_type', 'model_id']);
                $table->index('created_at');
            });
        }

        if (!Schema::hasTable('statistik_kunjungan')) {
            Schema::create('statistik_kunjungan', function (Blueprint $table) {
                $table->id();
                $table->string('halaman', 500)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->string('referrer', 500)->nullable();
                $table->timestampTz('visited_at')->default(DB::raw('CURRENT_TIMESTAMP'));

                $table->index('visited_at');
            });
        }
    }

    /* -------------------------------------------------------------- pemicu */

    private function pemicu(): void
    {
        /*
         * Kolom pencarian informasi publik. Bobot A untuk judul supaya
         * kecocokan judul selalu menang atas kecocokan isi.
         */
        DB::statement(<<<'SQL'
            CREATE OR REPLACE FUNCTION fn_infopublik_search() RETURNS trigger AS $$
            BEGIN
                NEW.search_vector :=
                    setweight(to_tsvector('simple', coalesce(NEW.judul, '')), 'A') ||
                    setweight(to_tsvector('simple', coalesce(NEW.ringkasan, '')), 'B') ||
                    setweight(to_tsvector('simple', coalesce(NEW.konten, '')), 'C');
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        SQL);

        DB::statement('DROP TRIGGER IF EXISTS trg_infopublik_search ON informasi_publik');
        DB::statement(<<<'SQL'
            CREATE TRIGGER trg_infopublik_search
            BEFORE INSERT OR UPDATE OF judul, ringkasan, konten ON informasi_publik
            FOR EACH ROW EXECUTE FUNCTION fn_infopublik_search();
        SQL);

        /*
         * Nomor registrasi permohonan: PPID-FSTJ/<tanggal>/<urutan harian>.
         *
         * Panel admin menyimpan permohonan tanpa mengisi nomor, jadi nomornya
         * harus lahir di sisi basis data. Bentuknya dibuat sama persis dengan
         * yang dihasilkan Portal Pengguna supaya keduanya berbagi satu deret
         * nomor per hari. `pg_advisory_xact_lock` menahan dua permohonan
         * bersamaan agar tidak memperebutkan urutan yang sama.
         */
        DB::statement(<<<'SQL'
            CREATE OR REPLACE FUNCTION fn_permohonan_kode() RETURNS trigger AS $$
            DECLARE
                awalan text;
                urutan integer;
            BEGIN
                IF NEW.kode_permohonan IS NOT NULL THEN
                    RETURN NEW;
                END IF;

                awalan := 'PPID-FSTJ/' || to_char(now(), 'YYYYMMDD') || '/';

                PERFORM pg_advisory_xact_lock(hashtext(awalan));

                SELECT coalesce(max(substring(kode_permohonan from length(awalan) + 1)::integer), 0) + 1
                  INTO urutan
                  FROM permohonan_informasi
                 WHERE kode_permohonan LIKE awalan || '%';

                NEW.kode_permohonan := awalan || lpad(urutan::text, 4, '0');

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        SQL);

        DB::statement('DROP TRIGGER IF EXISTS trg_permohonan_kode ON permohonan_informasi');
        DB::statement(<<<'SQL'
            CREATE TRIGGER trg_permohonan_kode
            BEFORE INSERT ON permohonan_informasi
            FOR EACH ROW EXECUTE FUNCTION fn_permohonan_kode();
        SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS trg_permohonan_kode ON permohonan_informasi');
        DB::statement('DROP TRIGGER IF EXISTS trg_infopublik_search ON informasi_publik');
        DB::statement('DROP FUNCTION IF EXISTS fn_permohonan_kode()');
        DB::statement('DROP FUNCTION IF EXISTS fn_infopublik_search()');

        // Urutan terbalik dari pembuatannya supaya foreign key tidak menahan.
        foreach ([
            'statistik_kunjungan', 'audit_log', 'notifikasi',
            'pengaturan_situs', 'tautan_terkait', 'menu_navigasi',
            'regulasi', 'halaman_statis', 'struktur_organisasi', 'banner_slider',
            'faq', 'galeri', 'berita', 'kategori_berita',
            'laporan_layanan', 'survey_kepuasan', 'keberatan_files', 'keberatan_informasi',
            'approval_permohonan', 'permohonan_log_status', 'permohonan_tanggapan_files',
            'permohonan_files', 'permohonan_informasi', 'pemohon',
            'informasi_dikecualikan', 'permintaan_unduhan', 'informasi_publik_files',
            'informasi_publik', 'kategori_informasi',
            'role_modul_akses', 'modul_sistem',
        ] as $tabel) {
            Schema::dropIfExists($tabel);
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role_id')) {
                $table->dropConstrainedForeignId('role_id');
            }

            $lepas = array_values(array_filter(
                ['phone', 'is_active', 'last_login_at'],
                fn (string $kolom) => Schema::hasColumn('users', $kolom)
            ));

            if ($lepas !== []) {
                $table->dropColumn($lepas);
            }
        });

        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
    }
};
