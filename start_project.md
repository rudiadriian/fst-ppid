Saya ingin membuat aplikasi PPID untuk perusahaan BUMD Jakarta PT Food Station Tjipinang Jaya (Perseroda), yang mana website ini dibutuhkan oleh perusahaan untuk menampung arus keluar dokumen resmi berdasarkan permintaan pengunjung website. 
Dan saya sudah membuatkan folder Front-end @\ppid\fe-ppid dan Back end @\ppid\be-ppid, saya ingin konsep ini terintegrasi dari informasi yang ditampilkan agar lebih dinamis, responsif, dan jikapun ada ganguan disisi Back End nantinya Front End tidak terkena dampaknya dan masih dapat diakses.

Untuk Front-end @\ppid\fe-ppid sudah selesai saya kerjakan mockup desainnya, sekarang tolong bantu saya membuat Back end @\ppid\be-ppid agar dapat berfungsi maksimal, memiliki keamanan yang tinggi dan ringan ketika diakses. 
Adapun langkah-langkah yang perlu dijalankan, jika sudah selesai dijalankan tolong beri tanda checklist. berikut langkahnya dibawah ini :
1. ✅ [SELESAI] tolong jalankan query SQL sesuai dengan schema database (nama database) ppiddb dibawah ini :
   - PostgreSQL 18, DB `ppiddb` dibuat, 37 tabel + 4 partisi + trigger `search_vector` (config `indonesian` jalan).
   - Arsitektur: `fe-ppid` = frontend publik (Laravel/Blade), `be-ppid` = backend/web admin (React fuse + Vite).
    -- =========================================================
    -- Rancangan Database PPID - PT Food Station Tjipinang Jaya
    -- PostgreSQL DDL
    -- =========================================================

    CREATE EXTENSION IF NOT EXISTS pgcrypto; -- untuk gen_random_uuid()

    -- =========================================================
    -- A. MANAJEMEN PENGGUNA & AKSES (INTERNAL)
    -- =========================================================

    CREATE TABLE roles (
        id BIGSERIAL PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        slug VARCHAR(100) UNIQUE NOT NULL,
        description TEXT,
        created_at TIMESTAMPTZ DEFAULT now(),
        updated_at TIMESTAMPTZ DEFAULT now()
    );

    CREATE TABLE users (
        id BIGSERIAL PRIMARY KEY,
        role_id BIGINT REFERENCES roles(id) ON DELETE RESTRICT,
        name VARCHAR(150) NOT NULL,
        email VARCHAR(150) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        phone VARCHAR(30),
        is_active BOOLEAN DEFAULT TRUE,
        last_login_at TIMESTAMPTZ,
        email_verified_at TIMESTAMPTZ,
        remember_token VARCHAR(100),
        created_at TIMESTAMPTZ DEFAULT now(),
        updated_at TIMESTAMPTZ DEFAULT now(),
        deleted_at TIMESTAMPTZ
    );
    CREATE INDEX idx_users_role ON users(role_id);

    CREATE TABLE permissions (
        id BIGSERIAL PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        slug VARCHAR(100) UNIQUE NOT NULL
    );

    CREATE TABLE role_has_permissions (
        role_id BIGINT REFERENCES roles(id) ON DELETE CASCADE,
        permission_id BIGINT REFERENCES permissions(id) ON DELETE CASCADE,
        PRIMARY KEY (role_id, permission_id)
    );

    -- Master menu/modul backend (dashboard, informasi publik, permohonan, dst)
    CREATE TABLE modul_sistem (
        id BIGSERIAL PRIMARY KEY,
        parent_id BIGINT REFERENCES modul_sistem(id) ON DELETE CASCADE,
        nama VARCHAR(100) NOT NULL,
        slug VARCHAR(100) UNIQUE NOT NULL,
        icon VARCHAR(50),
        route VARCHAR(150),
        urutan INT DEFAULT 0,
        is_active BOOLEAN DEFAULT TRUE
    );

    -- Matrix hak akses: role X modul -> aksi apa saja yang boleh
    CREATE TABLE role_modul_akses (
        id BIGSERIAL PRIMARY KEY,
        role_id BIGINT REFERENCES roles(id) ON DELETE CASCADE,
        modul_id BIGINT REFERENCES modul_sistem(id) ON DELETE CASCADE,
        can_view BOOLEAN DEFAULT FALSE,
        can_create BOOLEAN DEFAULT FALSE,
        can_edit BOOLEAN DEFAULT FALSE,
        can_delete BOOLEAN DEFAULT FALSE,
        can_approve BOOLEAN DEFAULT FALSE,
        can_export BOOLEAN DEFAULT FALSE,
        UNIQUE(role_id, modul_id)
    );

    CREATE TABLE audit_log (
        id BIGSERIAL PRIMARY KEY,
        user_id BIGINT REFERENCES users(id) ON DELETE SET NULL,
        action VARCHAR(50) NOT NULL,          -- create/update/delete/login
        model_type VARCHAR(100),
        model_id BIGINT,
        old_values JSONB,
        new_values JSONB,
        ip_address VARCHAR(45),
        user_agent VARCHAR(255),
        created_at TIMESTAMPTZ DEFAULT now()
    );
    CREATE INDEX idx_audit_model ON audit_log(model_type, model_id);
    CREATE INDEX idx_audit_user ON audit_log(user_id);

    -- =========================================================
    -- B. INFORMASI PUBLIK (INTI PPID)
    -- =========================================================

    CREATE TABLE kategori_informasi (
        id BIGSERIAL PRIMARY KEY,
        parent_id BIGINT REFERENCES kategori_informasi(id) ON DELETE SET NULL,
        nama VARCHAR(150) NOT NULL,
        slug VARCHAR(150) UNIQUE NOT NULL,
        deskripsi TEXT,
        urutan INT DEFAULT 0,
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMPTZ DEFAULT now(),
        updated_at TIMESTAMPTZ DEFAULT now()
    );

    CREATE TABLE informasi_publik (
        id BIGSERIAL PRIMARY KEY,
        kategori_id BIGINT REFERENCES kategori_informasi(id) ON DELETE RESTRICT,
        judul VARCHAR(255) NOT NULL,
        slug VARCHAR(255) UNIQUE NOT NULL,
        ringkasan TEXT,
        konten TEXT,
        nomor_klasifikasi VARCHAR(50),
        tanggal_publikasi DATE,
        status VARCHAR(20) DEFAULT 'draft' CHECK (status IN ('draft','menunggu_review','published','archived')),
        published_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
        reviewed_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
        reviewed_at TIMESTAMPTZ,
        views_count INT DEFAULT 0,
        search_vector TSVECTOR,
        created_at TIMESTAMPTZ DEFAULT now(),
        updated_at TIMESTAMPTZ DEFAULT now(),
        deleted_at TIMESTAMPTZ
    );
    CREATE INDEX idx_infopublik_status ON informasi_publik(status);
    CREATE INDEX idx_infopublik_kategori ON informasi_publik(kategori_id);
    CREATE INDEX idx_infopublik_search ON informasi_publik USING GIN(search_vector);

    -- Daftar Informasi Dikecualikan (kanal B - berbeda struktur dari informasi_publik biasa)
    CREATE TABLE informasi_dikecualikan (
        id BIGSERIAL PRIMARY KEY,
        judul VARCHAR(255) NOT NULL,
        slug VARCHAR(255) UNIQUE NOT NULL,
        ringkasan TEXT,
        alasan_pengecualian TEXT NOT NULL,
        dasar_hukum_pengecualian VARCHAR(255),
        jangka_waktu_pengecualian VARCHAR(100),
        tanggal_penetapan DATE,
        pejabat_penetap BIGINT REFERENCES users(id) ON DELETE SET NULL,
        file_surat_penetapan VARCHAR(500),
        status VARCHAR(20) DEFAULT 'draft' CHECK (status IN ('draft','published','archived')),
        created_at TIMESTAMPTZ DEFAULT now(),
        updated_at TIMESTAMPTZ DEFAULT now(),
        deleted_at TIMESTAMPTZ
    );
    CREATE INDEX idx_infodikecualikan_status ON informasi_dikecualikan(status);

    CREATE TABLE informasi_publik_files (
        id BIGSERIAL PRIMARY KEY,
        informasi_publik_id BIGINT REFERENCES informasi_publik(id) ON DELETE CASCADE,
        nama_file VARCHAR(255),
        path_file VARCHAR(500) NOT NULL,
        ukuran_file BIGINT,
        tipe_file VARCHAR(100),
        urutan INT DEFAULT 0,
        created_at TIMESTAMPTZ DEFAULT now()
    );
    CREATE INDEX idx_infofiles_parent ON informasi_publik_files(informasi_publik_id);

    -- =========================================================
    -- C. LAYANAN PERMOHONAN INFORMASI
    -- =========================================================

    CREATE TABLE pemohon (
        id BIGSERIAL PRIMARY KEY,
        nik VARCHAR(20),
        nama VARCHAR(150) NOT NULL,
        email VARCHAR(150) NOT NULL,
        no_hp VARCHAR(30),
        alamat TEXT,
        pekerjaan VARCHAR(100),
        jenis_pemohon VARCHAR(20) DEFAULT 'pribadi' CHECK (jenis_pemohon IN ('pribadi','instansi','kelompok')),
        nama_lembaga VARCHAR(200),
        password VARCHAR(255),
        email_verified_at TIMESTAMPTZ,
        created_at TIMESTAMPTZ DEFAULT now(),
        updated_at TIMESTAMPTZ DEFAULT now(),
        deleted_at TIMESTAMPTZ
    );
    CREATE INDEX idx_pemohon_email ON pemohon(email);

    CREATE TABLE permohonan_informasi (
        id BIGSERIAL PRIMARY KEY,
        kode_permohonan VARCHAR(30) UNIQUE NOT NULL DEFAULT ('PPID-' || to_char(now(),'YYYY') || '-' || lpad(nextval('permohonan_informasi_id_seq')::text,6,'0')),
        pemohon_id BIGINT REFERENCES pemohon(id) ON DELETE RESTRICT,
        kategori_id BIGINT REFERENCES kategori_informasi(id) ON DELETE SET NULL,
        rincian_informasi TEXT NOT NULL,
        tujuan_penggunaan TEXT,
        format_informasi VARCHAR(20) CHECK (format_informasi IN ('softcopy','hardcopy')),
        cara_pengiriman VARCHAR(20) CHECK (cara_pengiriman IN ('email','ambil_langsung','pos')),
        status VARCHAR(20) DEFAULT 'diajukan' CHECK (status IN ('diajukan','diverifikasi','diproses','menunggu_approval','disetujui','ditolak','ditolak_sebagian','selesai','kedaluwarsa')),
        alasan_penolakan TEXT,
        tanggal_permohonan TIMESTAMPTZ DEFAULT now(),
        batas_waktu_tanggapan TIMESTAMPTZ,
        tanggal_tanggapan TIMESTAMPTZ,
        ditangani_oleh BIGINT REFERENCES users(id) ON DELETE SET NULL,
        tampil_di_register_publik BOOLEAN DEFAULT FALSE,  -- consent pemohon utk ditampilkan di "Register Permohonan Informasi"
        created_at TIMESTAMPTZ DEFAULT now(),
        updated_at TIMESTAMPTZ DEFAULT now(),
        deleted_at TIMESTAMPTZ
    );
    CREATE INDEX idx_permohonan_status ON permohonan_informasi(status);
    CREATE INDEX idx_permohonan_pemohon ON permohonan_informasi(pemohon_id);
    CREATE INDEX idx_permohonan_sla ON permohonan_informasi(batas_waktu_tanggapan);

    CREATE TABLE permohonan_files (
        id BIGSERIAL PRIMARY KEY,
        permohonan_id BIGINT REFERENCES permohonan_informasi(id) ON DELETE CASCADE,
        nama_file VARCHAR(255),
        path_file VARCHAR(500) NOT NULL,
        tipe_file VARCHAR(100),
        created_at TIMESTAMPTZ DEFAULT now()
    );

    CREATE TABLE permohonan_tanggapan_files (
        id BIGSERIAL PRIMARY KEY,
        permohonan_id BIGINT REFERENCES permohonan_informasi(id) ON DELETE CASCADE,
        nama_file VARCHAR(255),
        path_file VARCHAR(500) NOT NULL,
        uploaded_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
        created_at TIMESTAMPTZ DEFAULT now()
    );

    CREATE TABLE permohonan_log_status (
        id BIGSERIAL PRIMARY KEY,
        permohonan_id BIGINT REFERENCES permohonan_informasi(id) ON DELETE CASCADE,
        status_sebelumnya VARCHAR(20),
        status_baru VARCHAR(20) NOT NULL,
        catatan TEXT,
        changed_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
        created_at TIMESTAMPTZ DEFAULT now()
    );
    CREATE INDEX idx_logstatus_permohonan ON permohonan_log_status(permohonan_id);

    -- Alur persetujuan berjenjang: PPID Pelaksana siapkan -> PPID Utama/Atasan PPID setujui
    CREATE TABLE approval_permohonan (
        id BIGSERIAL PRIMARY KEY,
        permohonan_id BIGINT REFERENCES permohonan_informasi(id) ON DELETE CASCADE,
        disiapkan_oleh BIGINT REFERENCES users(id) ON DELETE SET NULL,
        tanggal_diajukan TIMESTAMPTZ DEFAULT now(),
        disetujui_oleh BIGINT REFERENCES users(id) ON DELETE SET NULL,
        status_approval VARCHAR(20) DEFAULT 'pending' CHECK (status_approval IN ('pending','disetujui','ditolak','revisi')),
        catatan_approval TEXT,
        tanggal_approval TIMESTAMPTZ,
        created_at TIMESTAMPTZ DEFAULT now()
    );
    CREATE INDEX idx_approval_permohonan ON approval_permohonan(permohonan_id);

    CREATE TABLE keberatan_informasi (
        id BIGSERIAL PRIMARY KEY,
        permohonan_id BIGINT REFERENCES permohonan_informasi(id) ON DELETE CASCADE,
        pemohon_id BIGINT REFERENCES pemohon(id) ON DELETE RESTRICT,
        jenis_keberatan VARCHAR(50) CHECK (jenis_keberatan IN (
            'permohonan_ditolak','informasi_tidak_disediakan','permintaan_tidak_ditanggapi',
            'informasi_tidak_sesuai','biaya_tidak_wajar','melebihi_jangka_waktu'
        )),
        alasan_keberatan TEXT NOT NULL,
        status VARCHAR(20) DEFAULT 'diajukan' CHECK (status IN ('diajukan','diproses','selesai')),
        tanggapan_atasan_ppid TEXT,
        ditangani_oleh BIGINT REFERENCES users(id) ON DELETE SET NULL,
        tanggal_keberatan TIMESTAMPTZ DEFAULT now(),
        tanggal_tanggapan TIMESTAMPTZ,
        created_at TIMESTAMPTZ DEFAULT now(),
        updated_at TIMESTAMPTZ DEFAULT now()
    );
    CREATE INDEX idx_keberatan_permohonan ON keberatan_informasi(permohonan_id);

    CREATE TABLE keberatan_files (
        id BIGSERIAL PRIMARY KEY,
        keberatan_id BIGINT REFERENCES keberatan_informasi(id) ON DELETE CASCADE,
        nama_file VARCHAR(255),
        path_file VARCHAR(500) NOT NULL,
        created_at TIMESTAMPTZ DEFAULT now()
    );

    -- =========================================================
    -- D. KONTEN DINAMIS WEBSITE
    -- =========================================================

    -- Survei kepuasan pemohon setelah permohonan selesai (indikator "98% Kepuasan" di beranda)
    CREATE TABLE survey_kepuasan (
        id BIGSERIAL PRIMARY KEY,
        permohonan_id BIGINT REFERENCES permohonan_informasi(id) ON DELETE CASCADE,
        rating SMALLINT CHECK (rating BETWEEN 1 AND 5),
        komentar TEXT,
        created_at TIMESTAMPTZ DEFAULT now()
    );

    -- Lead-capture form "Unduh Dokumen" di beranda (nama/email/telepon -> link dikirim ke email)
    CREATE TABLE permintaan_unduhan (
        id BIGSERIAL PRIMARY KEY,
        informasi_publik_file_id BIGINT REFERENCES informasi_publik_files(id) ON DELETE SET NULL,
        nama VARCHAR(150) NOT NULL,
        email VARCHAR(150) NOT NULL,
        telepon VARCHAR(30),
        token_unduhan VARCHAR(100) UNIQUE DEFAULT encode(gen_random_bytes(24),'hex'),
        token_expired_at TIMESTAMPTZ,
        downloaded_at TIMESTAMPTZ,
        created_at TIMESTAMPTZ DEFAULT now()
    );
    CREATE INDEX idx_unduhan_token ON permintaan_unduhan(token_unduhan);

    -- Laporan Statistik Informasi Publik & Laporan Pelayanan Informasi (kanal D)
    CREATE TABLE laporan_layanan (
        id BIGSERIAL PRIMARY KEY,
        tipe_laporan VARCHAR(30) CHECK (tipe_laporan IN ('statistik_informasi','pelayanan_informasi')),
        judul VARCHAR(255) NOT NULL,
        tahun INT NOT NULL,
        periode VARCHAR(30),                 -- mis. 'Triwulan I', 'Semester II', 'Tahunan'
        jumlah_permohonan_masuk INT DEFAULT 0,
        jumlah_dikabulkan INT DEFAULT 0,
        jumlah_ditolak INT DEFAULT 0,
        jumlah_ditolak_sebagian INT DEFAULT 0,
        jumlah_keberatan INT DEFAULT 0,
        rata_rata_hari_respon NUMERIC(5,2),
        ringkasan TEXT,
        file_laporan VARCHAR(500),
        status VARCHAR(20) DEFAULT 'draft' CHECK (status IN ('draft','published','archived')),
        published_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
        created_at TIMESTAMPTZ DEFAULT now(),
        updated_at TIMESTAMPTZ DEFAULT now()
    );
    CREATE INDEX idx_laporan_tipe_tahun ON laporan_layanan(tipe_laporan, tahun);

    CREATE TABLE kategori_berita (
        id BIGSERIAL PRIMARY KEY,
        nama VARCHAR(100) NOT NULL,
        slug VARCHAR(100) UNIQUE NOT NULL
    );

    CREATE TABLE berita (
        id BIGSERIAL PRIMARY KEY,
        kategori_berita_id BIGINT REFERENCES kategori_berita(id) ON DELETE SET NULL,
        judul VARCHAR(255) NOT NULL,
        slug VARCHAR(255) UNIQUE NOT NULL,
        thumbnail VARCHAR(500),
        ringkasan TEXT,
        konten TEXT,
        tanggal_publikasi DATE,
        status VARCHAR(20) DEFAULT 'draft' CHECK (status IN ('draft','published','archived')),
        penulis BIGINT REFERENCES users(id) ON DELETE SET NULL,
        views_count INT DEFAULT 0,
        created_at TIMESTAMPTZ DEFAULT now(),
        updated_at TIMESTAMPTZ DEFAULT now(),
        deleted_at TIMESTAMPTZ
    );
    CREATE INDEX idx_berita_status_tgl ON berita(status, tanggal_publikasi);

    CREATE TABLE galeri (
        id BIGSERIAL PRIMARY KEY,
        judul VARCHAR(255),
        tipe VARCHAR(10) CHECK (tipe IN ('foto','video')),
        path_file VARCHAR(500) NOT NULL,
        deskripsi TEXT,
        tanggal DATE,
        created_at TIMESTAMPTZ DEFAULT now()
    );

    CREATE TABLE faq (
        id BIGSERIAL PRIMARY KEY,
        pertanyaan TEXT NOT NULL,
        jawaban TEXT NOT NULL,
        kategori VARCHAR(100),
        urutan INT DEFAULT 0,
        is_active BOOLEAN DEFAULT TRUE
    );

    CREATE TABLE banner_slider (
        id BIGSERIAL PRIMARY KEY,
        judul VARCHAR(255),
        gambar VARCHAR(500) NOT NULL,
        link VARCHAR(500),
        urutan INT DEFAULT 0,
        is_active BOOLEAN DEFAULT TRUE,
        tanggal_mulai DATE,
        tanggal_selesai DATE
    );

    CREATE TABLE struktur_organisasi (
        id BIGSERIAL PRIMARY KEY,
        nama VARCHAR(150) NOT NULL,
        jabatan VARCHAR(150) NOT NULL,
        foto VARCHAR(500),
        urutan INT DEFAULT 0,
        deskripsi TEXT,
        is_active BOOLEAN DEFAULT TRUE
    );

    CREATE TABLE halaman_statis (
        id BIGSERIAL PRIMARY KEY,
        judul VARCHAR(255) NOT NULL,
        slug VARCHAR(255) UNIQUE NOT NULL,
        konten TEXT,
        is_active BOOLEAN DEFAULT TRUE,
        updated_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
        updated_at TIMESTAMPTZ DEFAULT now()
    );

    CREATE TABLE regulasi (
        id BIGSERIAL PRIMARY KEY,
        kategori VARCHAR(30) DEFAULT 'regulasi' CHECK (kategori IN ('dasar_hukum_ppid','regulasi','pedoman')), -- bedakan kanal Profil (Dasar Hukum) vs kanal Layanan (Regulasi)
        judul VARCHAR(255) NOT NULL,
        nomor_peraturan VARCHAR(100),
        jenis_peraturan VARCHAR(100),
        tahun INT,
        file_path VARCHAR(500),
        tanggal_berlaku DATE,
        created_at TIMESTAMPTZ DEFAULT now()
    );

    CREATE TABLE tautan_terkait (
        id BIGSERIAL PRIMARY KEY,
        nama VARCHAR(150) NOT NULL,
        url VARCHAR(500) NOT NULL,
        logo VARCHAR(500),
        urutan INT DEFAULT 0,
        is_active BOOLEAN DEFAULT TRUE
    );

    CREATE TABLE menu_navigasi (
        id BIGSERIAL PRIMARY KEY,
        parent_id BIGINT REFERENCES menu_navigasi(id) ON DELETE CASCADE,
        label VARCHAR(100) NOT NULL,
        url VARCHAR(255),
        urutan INT DEFAULT 0,
        target VARCHAR(10) DEFAULT '_self',
        is_active BOOLEAN DEFAULT TRUE
    );

    -- =========================================================
    -- E. OPERASIONAL & MONITORING
    -- =========================================================

    CREATE TABLE notifikasi (
        id BIGSERIAL PRIMARY KEY,
        user_id BIGINT REFERENCES users(id) ON DELETE CASCADE,
        type VARCHAR(50),
        message TEXT,
        is_read BOOLEAN DEFAULT FALSE,
        data JSONB,
        created_at TIMESTAMPTZ DEFAULT now()
    );
    CREATE INDEX idx_notifikasi_user ON notifikasi(user_id, is_read);

    CREATE TABLE statistik_kunjungan (
        id BIGSERIAL PRIMARY KEY,
        halaman VARCHAR(255),
        ip_address VARCHAR(45),
        user_agent VARCHAR(255),
        referrer VARCHAR(255),
        visited_at TIMESTAMPTZ DEFAULT now()
    ) PARTITION BY RANGE (visited_at);
    -- Contoh partisi bulanan (buat via migration terjadwal):
    -- CREATE TABLE statistik_kunjungan_2026_07 PARTITION OF statistik_kunjungan
    --   FOR VALUES FROM ('2026-07-01') TO ('2026-08-01');

    CREATE TABLE pengaturan_situs (
        id BIGSERIAL PRIMARY KEY,
        key VARCHAR(100) UNIQUE NOT NULL,
        value TEXT,
        group_name VARCHAR(50)
    );

    -- =========================================================
    -- TRIGGER: auto-update search_vector pada informasi_publik
    -- =========================================================
    CREATE OR REPLACE FUNCTION informasi_publik_search_update() RETURNS trigger AS $$
    BEGIN
        NEW.search_vector := to_tsvector('indonesian', coalesce(NEW.judul,'') || ' ' || coalesce(NEW.ringkasan,''));
        RETURN NEW;
    END
    $$ LANGUAGE plpgsql;

    CREATE TRIGGER trg_infopublik_search
    BEFORE INSERT OR UPDATE ON informasi_publik
    FOR EACH ROW EXECUTE FUNCTION informasi_publik_search_update();

2. ✅ [SELESAI] sekarang tolong jalankan backend foldernya agar bisa login dan mengakses web adminnya.
    - ✅ `npm install` di `be-ppid` selesai (1091 paket). Sebelumnya `node_modules` kosong.
    - ✅ Dev server jalan: `npm run dev` → http://localhost:3000 (Vite 6.3.5, Fuse React 16).
    - ✅ Login admin bisa diakses di `/sign-in`. Kredensial mock bawaan: `admin@fusetheme.com` (password apa saja asal tidak kosong).
    - ✅ DB `ppiddb` terverifikasi: 37 tabel (PostgreSQL 18).
    - 🔧 Perbaikan: `@mui/styles` dihapus dari `optimizeDeps.include` di `vite.config.mts` (paket tidak ada di dependencies → warning saat start).
    - ⚠️ Catatan Node: Node terpasang v20.10.0, `package.json` engines minta >= 22.12.0. Jalan normal karena `engine-strict=false`, tapi disarankan upgrade Node 22 LTS.
    - ✅ Integrasi API selesai (rincian di 3b). Seluruh modul CMS membaca/menulis `ppiddb` lewat `api-ppid`; menu demo Fuse sudah diganti menu PPID. Halaman awal panel: `/ppid/dashboard`.

3. ✅ [SELESAI] Bangun API layer + integrasi CMS. Keputusan arsitektur (disetujui 2026-07-27):
    - **Lokasi API**: project Laravel terpisah `api-ppid` (bukan di dalam `fe-ppid`). Alasan: endpoint admin tidak menempel di domain publik, dan kalau API mati `fe-ppid` tetap hidup karena baca `ppiddb` langsung.
    - **Auth admin**: JWT yang diterbitkan API dari tabel `users` + `roles`, RBAC dari `role_modul_akses`. Di sisi `be-ppid` pakai `JwtAuthProvider` bawaan Fuse, tinggal ganti target dari MSW mock ke API asli.
    - Rincian yang perlu dikerjakan: scaffold `api-ppid`, Eloquent model untuk 37 tabel, endpoint CRUD per modul CMS, hardening (rate limit, CORS whitelist, validasi, audit_log), lalu ganti hook data di `be-ppid` per modul.

    Progres 3a — fondasi API + login terintegrasi (SELESAI):
    - ✅ `api-ppid` dibuat (Laravel 10, PHP 8.1) + `tymon/jwt-auth`. Jalankan: `php artisan serve --port=8001`.
    - ✅ `.env` diarahkan ke `ppiddb` (kredensial sama dengan `fe-ppid`), guard `api` pakai driver `jwt`.
    - ✅ Migration `add_admin_ui_columns_to_users_table`: menambah kolom `photo_url`, `shortcuts`, `settings` di tabel `users` untuk kebutuhan panel admin. Tabel inti tetap dari DDL langkah 1.
    - ✅ Endpoint: `GET /api/v1/health`, `POST /api/v1/auth/sign-in`, `GET /api/v1/auth/sign-in-with-token`, `POST /api/v1/auth/refresh`, `POST /api/v1/auth/sign-out`, `PUT /api/v1/auth/user/{id}`.
    - ✅ `ModulSistemSeeder`: 19 modul CMS + role `super-admin`, `ppid-pelaksana`, `ppid-utama` beserta matrix `role_modul_akses`.
    - ✅ Middleware `akses:{modul},{aksi}` — hak akses dibaca dari DB tiap request, bukan dari klaim token.
    - ✅ Hardening: rate limit login 5/menit per email+IP (20/menit per IP), CORS dibatasi `ADMIN_ORIGINS`, pesan login gagal tidak membedakan email/password (anti enumerasi), semua login sukses/gagal masuk `audit_log`, token JWT TTL 60 menit + blacklist saat logout.
    - ✅ `be-ppid` disambungkan: `authApi.ts` menunjuk `/api/v1/auth/*`, proxy `/api/v1` → `127.0.0.1:8001` di `vite.config.mts`, provider AWS Cognito & Firebase dimatikan (sisa JWT saja), prefill kredensial demo Fuse dihapus dari form login.
    - ✅ Login admin nyata memakai akun `admin@foodstation.co.id`. Kata sandi di-set lewat `php artisan ppid:set-password admin@foodstation.co.id` (jangan simpan kata sandi di file yang masuk git). Terverifikasi via API langsung dan lewat proxy `localhost:3000`. `npx tsc --noEmit` di `be-ppid` bersih.
    - ⚠️ Wajib sebelum production: PHP 8.1 dan Laravel 10 sudah lewat masa dukungan keamanan (mis. CVE-2026-48019 baru ditambal di Laravel ≥12.60). Upgrade PHP ke 8.3/8.4 lalu `composer require laravel/framework:^12`.

    Progres 3b — modul CMS (SELESAI 2026-07-28). Sasaran: `be-ppid` menjadi pusat kontrol konten situs `fe-ppid`, keduanya terhubung lewat API yang aman dan mudah dipelihara.

    Sisi API (`api-ppid`):
    - ✅ 26 Eloquent model baru menutup seluruh tabel CMS di `ppiddb` (kategori & informasi publik, informasi dikecualikan, pemohon, permohonan + lampiran + log status + approval, keberatan, survei, permintaan unduhan, laporan, berita, galeri, FAQ, banner, struktur organisasi, halaman statis, regulasi, tautan, menu, notifikasi, pengaturan, statistik kunjungan).
    - ✅ `CrudController` generik: paginasi, pencarian (ILIKE), filter, pengurutan, slug otomatis unik, hapus massal, dan penulisan `audit_log` untuk setiap create/update/delete. Nama kolom dari query string selalu dicocokkan ke daftar putih di kelas turunan, jadi `sort`/`filter` bukan jalur injeksi.
    - ✅ 20 controller modul tipis di `app/Http/Controllers/Api/Cms/` — hanya berisi konfigurasi + aturan validasi, tanpa mengulang logika CRUD.
    - ✅ `CrudRoute::register()` mendaftarkan route sekaligus memasang `akses:{modul},{aksi}`, sehingga tidak ada endpoint CMS yang lolos tanpa cek hak akses. Total 169 route di bawah `/api/v1`.
    - ✅ Alur permohonan: `POST /permohonan/{id}/status` hanya menerima transisi yang sah (tabel `PermohonanInformasi::TRANSISI`), wajib alasan saat menolak, dan setiap perpindahan tercatat di `permohonan_log_status`. Plus endpoint approval berjenjang dan lampiran berkas tanggapan.
    - ✅ `POST /uploads`: nama berkas di disk diacak, ekstensi/mime dibatasi daftar putih per jenis (ekstensi yang bisa dieksekusi tidak ada di daftar mana pun), batas ukuran 5/20/100 MB, rate limit 30/menit.
    - ✅ Endpoint pendukung: `GET /me/navigation` (menu + hak akses per modul dari DB), `GET /dashboard/ringkasan`, `GET /laporan-layanan/rekap`, `GET|PUT /role/{id}/akses`, `POST /pengaturan-situs/massal`.
    - ✅ Audit log bersifat baca-saja dari API (store/update/destroy menjawab 405) supaya jejaknya tetap sah sebagai bukti.
    - ✅ Handler error API selalu JSON; pelanggaran constraint DB dijawab 409 tanpa membocorkan SQL. Pesan validasi berbahasa Indonesia (`lang/id/validation.php`, `APP_LOCALE=id`).
    - ✅ Rate limit disesuaikan: 300/menit untuk sesi login, 60/menit untuk anonim.

    Berkas media (jembatan CMS → situs publik):
    - ✅ Disk `media` di `api-ppid` menulis ke `fe-ppid/storage/app/public` (diatur `MEDIA_ROOT`/`MEDIA_URL`), jadi berkas yang diunggah dari CMS langsung tersedia untuk situs publik tanpa sinkronisasi.
    - ✅ `fe-ppid` menambah route `/storage/{path}` yang menyajikan berkas dari storage (di luar document root, `X-Content-Type-Options: nosniff`). Route ini menganggur bila `php artisan storage:link` dijalankan.
    - DB menyimpan path relatif (`uploads/{modul}/{tahun}/{bulan}/{acak}.{ext}`) agar tiap sisi menyusun URL-nya sendiri.

    Sisi panel admin (`be-ppid`):
    - ✅ Engine CRUD generik di `src/app/(control-panel)/ppid/`: klien API tunggal, hook react-query per resource, halaman daftar berbasis `DataTable` (paginasi/urut/cari dikerjakan server), dan formulir dialog yang dirakit dari konfigurasi field. Pesan validasi API ditempelkan ke input terkait.
    - ✅ Registry 20 modul di `lib/resources.ts` — menambah modul cukup menambah satu objek konfigurasi, tanpa komponen atau route baru.
    - ✅ Tipe field yang didukung: teks, textarea, rich text (TipTap), angka, pilihan, relasi (dropdown dari resource lain), boolean, tanggal, unggah berkas tunggal, unggah gambar, dan lampiran ganda.
    - ✅ Menu samping dibangun dari hak akses role (`PpidNavigationSync`); modul yang tidak boleh dilihat tidak muncul, dan tombol tambah/ubah/hapus mengikuti hak `create`/`edit`/`delete`. Penegakan sebenarnya tetap di API.
    - ✅ Dashboard PPID: beban permohonan, jumlah lewat batas waktu, keberatan belum selesai, kondisi konten, indeks kepuasan, tren 6 bulan.
    - ✅ Route `/ppid/:resourceSlug`; root `/` diarahkan ke `/ppid/dashboard`. `npx tsc --noEmit` bersih dan `vite build` sukses.

    Verifikasi yang sudah dijalankan (via API, 2026-07-28):
    - GET semua modul 200; create kategori + informasi publik berhasil; unggah dokumen sah berhasil sementara berkas `.php` ditolak 422; transisi status ilegal ditolak dengan pesan jelas; endpoint tanpa token 401; tulis ke audit log 405; rekap laporan dan simpan pengaturan massal berjalan.
    - Data uji sudah dibersihkan kembali dari `ppiddb`.

    Progres 3c — perbaikan login + integrasi situs publik (SELESAI 2026-07-28):

    Perbaikan login "masih loading":
    - ✅ Penyebab: `settingsConfig.defaultAuth` masih `['admin']` bawaan Fuse, sedangkan role PPID berasal dari tabel `roles` (`super-admin`, `ppid-utama`, `ppid-pelaksana`). Setelah login, `FuseAuthorization` menilai role tidak cocok → mengalihkan ke `/401` → halaman itu ikut ditolak → berputar terus dan yang tampil hanya `FuseLoading`.
    - ✅ Perbaikan: `defaultAuth` disetel `null` (daftar role statis di frontend tidak dipakai lagi karena role bisa ditambah dari CMS). Halaman panel dijaga komponen baru `PpidAuthGuard` — cukup "harus sudah login"; hak per modul tetap ditegakkan middleware `akses:` di API.
    - ✅ `loginRedirectUrl` diarahkan ke `/ppid/dashboard`.
    - ✅ `useNavigasi` tidak lagi dipanggil sebelum login. Sebelumnya request tanpa token dijawab 401 dan interceptor auth memperlakukan 401 apa pun sebagai perintah sign out.
    - ✅ 35 berkas route demo Fuse dihapus (`apps/*`, `dashboards/*`, `pages/*`, `auth-role-examples`, `documentation`) sehingga halaman demo tidak lagi bisa dibuka lewat URL langsung. Komponennya dibiarkan karena sebagian dipakai panel (messenger, notifikasi). Menu profil/inbox di `UserMenu` diganti tautan Dashboard.
    - ✅ Terverifikasi lewat proxy `localhost:3000` memakai akun uji berrole `ppid-pelaksana`: login mengembalikan token + user, `/me/navigation` mengembalikan modul sesuai role, dan modul `pengguna` yang tidak diizinkan dijawab 403. Akun uji sudah dihapus kembali.

    Situs publik `fe-ppid` kini membaca konten dari CMS:
    - ✅ 13 model baru di `fe-ppid` (berita, kategori berita, galeri, FAQ, banner, menu, struktur organisasi, tautan, pengaturan situs, halaman statis, kategori & informasi publik beserta lampiran).
    - ✅ Helper `App\Support\Cms`: setiap pembacaan dibungkus penanganan galat dengan data cadangan, ditambah pembentuk URL media, pembaca pengaturan situs, dan format tanggal Indonesia. Kalau DB bermasalah, halaman tetap terbuka dan menampilkan pemberitahuan.
    - ✅ `HomeController` baru: slider hero (Banner Slider), kartu klasifikasi + jumlah dokumen (Kategori Informasi), empat angka ringkas (permohonan, dokumen, regulasi, indeks kepuasan dari survei), berita terbaru, slider arsip (Galeri), laporan terbaru, FAQ, dan blok kontak (Pengaturan Situs). Blok data statis di `home.blade.php` dihapus; sisanya hanya ikon dan tautan menu.
    - ✅ `KontenController` + halaman baru: `/berita`, `/berita/{slug}`, `/galeri`, `/faq`, `/struktur-ppid`. Semuanya masuk menu header, menu mobile, dan footer.
    - ✅ `showPublicInformation` membaca kategori dan dokumen dari CMS (dengan tautan unduh berkas). Tiga klasifikasi wajib UU No. 14/2008 selalu punya halaman walau kategorinya belum dibuat — tabelnya tampil kosong, bukan 404.
    - ✅ `showProfilePage` memakai isi dari modul Halaman Statis bila slug-nya sudah dibuat (menerima `{slug}` atau `profil-{slug}`); kalau belum, tata letak profil bawaan tetap tampil. Halaman Struktur PPID menampilkan pejabat dari modul Struktur Organisasi.
    - ✅ `CmsLayoutComposer` mengisi header dan footer di semua halaman: daftar kategori informasi, tautan terkait, dan kontak. Tautan profil di footer yang sebelumnya menunjuk slug tidak dikenal (404) sudah dibetulkan.
    - ✅ Konten berbasis HTML dari editor CMS dirender hanya dengan tag aman (tanpa `<script>`), baik di berita maupun halaman statis.
    - ✅ `KontenAwalSeeder` di `api-ppid`: 3 kategori informasi, 4 kategori berita, 5 FAQ, dan 5 pengaturan kontak. Sengaja tidak membuat berita/laporan/pejabat karangan — itu harus diisi perusahaan lewat CMS.

    Verifikasi 3c (2026-07-28):
    - 20 halaman publik dijawab 200: beranda, berita, galeri, FAQ, struktur, 3 kanal informasi + dikecualikan, 4 halaman profil, regulasi, permohonan, keberatan, cek status, register, laporan, standar layanan.
    - Rantai penuh CMS → situs publik diuji: berita dibuat lewat `POST /api/v1/berita` langsung muncul di `/berita`, di beranda, dan halaman detailnya 200. Berkas yang diunggah lewat `POST /api/v1/uploads` tersaji di `http://localhost:8000/storage/uploads/...` dengan `Content-Type: image/png`, sedangkan path di luar folder `uploads` dijawab 404. Data uji sudah dihapus.
    - `npx tsc --noEmit` di `be-ppid` bersih; `php -l` bersih pada seluruh berkas PHP yang disentuh.

    Yang belum / catatan lanjutan:
    - ⚠️ Belum diuji lewat antarmuka browser sungguhan (tidak ada akses browser di sesi ini). Uji manual: `http://localhost:3000` untuk panel dan `http://localhost:8000` untuk situs publik.
    - ⚠️ Isi situs masih menunggu data asli: berita, galeri, banner, laporan, regulasi, struktur pejabat, dan halaman profil. Semua sudah bisa diisi dari CMS.
    - ⚠️ Menu header masih memakai struktur kanal tetap; modul Menu Navigasi tersedia di CMS tapi belum dipakai untuk menyusun ulang menu utama.
    - ✅ Sebelum production: jalankan `php artisan storage:link` di `fe-ppid`, set `APP_DEBUG=false` di `api-ppid`, dan lakukan upgrade PHP/Laravel yang disebut di 3a.
4. ✅ [SELESAI] percobaan login masih belum berhasil, tampilan cms dashboard (be-ppid) tidak tampil

    Ada empat penyebab berbeda yang menumpuk. Semuanya sudah ditambal dan diverifikasi lewat peramban sungguhan (Chrome headless dikendalikan protokol CDP), bukan hanya lewat curl.

    Penyebab 1 — sesi hilang tiap kali halaman dimuat ulang (paling menentukan):
    - `JwtAuthProvider` melakukan auto-login memakai token tersimpan, tapi tidak pernah memasang kembali header `Authorization` pada klien HTTP. Akibatnya setiap permintaan setelah reload dikirim tanpa token → API menjawab 401 → interceptor auth menganggapnya perintah sign out → pengguna terlempar ke `/sign-in` dan panel tampak "tidak muncul".
    - Perbaikan: header dipasang ulang tepat setelah auto-login berhasil, termasuk memakai token baru bila server mengirim `New-Access-Token`.

    Penyebab 2 — token dibatalkan sendiri oleh server:
    - `GET /auth/sign-in-with-token` memanggil `Auth::refresh()` yang langsung memasukkan token lama ke blacklist. Panel menembakkan beberapa permintaan bersamaan saat halaman dimuat, sehingga permintaan yang masih membawa token lama ditolak 401.
    - Perbaikan: endpoint itu tidak lagi memutar token; pembaruan token tetap tersedia lewat `POST /auth/refresh`.

    Penyebab 3 — service worker mock (MSW) mencegat API asli:
    - Template Fuse mendaftarkan service worker MSW di `src/index.tsx`. Service worker yang terlanjur terpasang di peramban ikut mencegat `/api/v1/*`; terpantau `GET /api/v1/me/navigation` dijawab 500 oleh service worker, dan pernah membuat React gagal dipasang sama sekali (halaman berhenti di splash screen).
    - Perbaikan: MSW tidak lagi dijalankan (semua data sudah dari API asli), dan saat aplikasi mulai, seluruh registrasi service worker lama dicabut supaya peramban yang pernah membuka versi lama pulih sendiri.

    Penyebab 4 — panel notifikasi menembak API sebelum login:
    - Panel notifikasi terpasang di layout sejak halaman pertama dan memanggil endpoint mock `/api/mock/notifications` (404 setelah MSW dilepas) lalu API asli tanpa token (401) — 401 itu memicu sign out otomatis tepat setelah pengguna berhasil masuk.
    - Perbaikan: dibuat endpoint asli `GET|DELETE /api/v1/notifikasi` yang membaca tabel `notifikasi` milik pengguna login, service di frontend diarahkan ke sana, dan query-nya baru berjalan setelah status login pasti.

    Perbaikan pendukung:
    - `FuseAuthorization` mengabaikan nilai `fuseRedirectUrl` berisi `401`/`404` yang tersimpan dari percobaan gagal sebelumnya, supaya sesi peramban lama tidak selalu mendarat di halaman "tidak berwenang".
    - `Authenticate` middleware di API mengembalikan 401 JSON, bukan mencoba redirect ke route `login` yang tidak ada (sebelumnya menghasilkan 500 "Route [login] not defined").
    - Panel Messenger demo dilepas dari layout kanan (layout1/2/3) karena endpoint mock-nya sudah tidak ada dan hanya menghasilkan 404 beruntun.

    Verifikasi lewat peramban (Chrome headless, 2026-07-28):
    - Login dengan akun uji berrole `ppid-pelaksana`: berhasil dan mendarat di `/ppid/dashboard`, judul "Dashboard PPID" tampil dengan angka nyata dari database, 18 tautan menu PPID terbentuk sesuai hak akses role.
    - Muat ulang langsung ke `/ppid/faq` (bukan navigasi dalam aplikasi): sesi bertahan, `GET /api/v1/me/navigation` menjawab 200, tabel FAQ menampilkan 5 baris dari CMS.
    - Seluruh 18 modul CMS ditelusuri satu per satu: semuanya membuka halaman yang benar dengan judul modul masing-masing, tidak ada "Akses ditolak" maupun "modul tidak dikenal", dan **tidak ada satu pun respons HTTP ≥ 400**.
    - Formulir diuji: tombol "Tambah FAQ" membuka dialog, data tersimpan (notifikasi "FAQ ditambahkan"), dan baris baru langsung muncul di tabel. Data uji beserta akun uji sudah dihapus kembali.
    - `npx tsc --noEmit` bersih; situs publik (`/`, `/berita`, `/faq`, `/galeri`), API (`/api/v1/health`), dan panel (`localhost:3000`) semuanya menjawab 200.

    Catatan pemakaian:
    - Jalankan tiga proses: `php artisan serve --port=8001` di `api-ppid`, `php artisan serve --port=8000` di `fe-ppid`, dan `npm run dev` di `be-ppid` (port 3000).
    - Bila panel pernah dibuka sebelum perbaikan ini, muat ulang sekali; pencabutan service worker lama berjalan otomatis saat aplikasi dimulai.