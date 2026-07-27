Saya ingin membuat aplikasi PPID untuk perusahaan BUMD Jakarta PT Food Station Tjipinang Jaya (Perseroda), yang mana website ini dibutuhkan oleh perusahaan untuk menampung arus keluar dokumen resmi berdasarkan permintaan pengunjung website. 
Dan saya sudah membuatkan folder Front-end @\ppid\fe-ppid dan Back end @\ppid\be-ppid, saya ingin konsep ini terintegrasi dari informasi yang ditampilkan agar lebih dinamis, responsif, dan jikapun ada ganguan disisi Back End nantinya Front End tidak terkena dampaknya dan masih dapat diakses.

Untuk Front-end @\ppid\fe-ppid sudah selesai saya kerjakan mockup desainnya, sekarang tolong bantu saya membuat Back end @\ppid\be-ppid agar dapat berfungsi maksimal, memiliki keamanan yang tinggi dan ringan ketika diakses. 
Adapun langkah-langkah yang perlu dijalankan, jika sudah selesai dijalankan tolong beri tanda checklist. berikut langkahnya dibawah ini :
1. tolong jalankan query SQL sesuai dengan schema database (nama database) ppiddb dibawah ini :
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