<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Alur persetujuan berjenjang yang bisa diubah super admin.
 *
 * Sebelum ini persetujuan permohonan hanya satu baris bebas di
 * `approval_permohonan`: siapa pun yang punya hak `approve` bisa memutuskan,
 * tidak ada urutan, dan keberatan tidak punya jalur persetujuan sama sekali.
 * Susunan jenjangnya pun tertanam di kode, jadi perubahan struktur organisasi
 * menuntut penulisan ulang.
 *
 * Tiga tabel memisahkan **definisi** dari **jalannya**:
 *
 *  1. `alur_approval`       — satu definisi alur per jenis pengajuan.
 *  2. `alur_approval_tahap` — jenjangnya: urutan, role yang memutuskan, dan
 *                             kotak struktur organisasi yang diwakilinya.
 *  3. `approval_pengajuan`  — langkah nyata milik satu pengajuan, dibuat saat
 *                             pengajuan masuk ke tahap persetujuan.
 *
 * Tabel ketiga **menyalin** nama tahap dan role-nya saat langkah dibuat.
 * Tanpa salinan itu, super admin yang menyusun ulang alur ikut menulis ulang
 * riwayat persetujuan yang sudah terjadi — persetujuan tahun lalu akan
 * terbaca seolah diputus oleh jabatan yang baru dibuat kemarin.
 *
 * `approval_permohonan` yang lama tidak dihapus: isinya riwayat yang sudah
 * terjadi. Ia berhenti ditulis dan hanya dibaca sebagai arsip.
 */
return new class extends Migration
{
    /** CHECK constraint dengan daftar nilai yang sah. */
    private function check(string $tabel, string $nama, string $kolom, array $nilai): void
    {
        $daftar = collect($nilai)->map(fn ($v) => "'".$v."'")->implode(', ');

        DB::statement("ALTER TABLE {$tabel} DROP CONSTRAINT IF EXISTS {$nama}");
        DB::statement("ALTER TABLE {$tabel} ADD CONSTRAINT {$nama} CHECK ({$kolom} IS NULL OR {$kolom}::text = ANY (ARRAY[{$daftar}]::text[]))");
    }

    public function up(): void
    {
        if (!Schema::hasTable('alur_approval')) {
            Schema::create('alur_approval', function (Blueprint $table) {
                $table->id();
                // 'permohonan' | 'keberatan'
                $table->string('jenis', 20);
                $table->string('nama', 150);
                $table->text('keterangan')->nullable();
                $table->boolean('is_active')->default(true);

                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->softDeletes();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

                $table->index(['jenis', 'is_active']);
            });

            $this->check('alur_approval', 'alur_approval_jenis_check', 'jenis', ['permohonan', 'keberatan']);
        }

        if (!Schema::hasTable('alur_approval_tahap')) {
            Schema::create('alur_approval_tahap', function (Blueprint $table) {
                $table->id();
                $table->foreignId('alur_id')->constrained('alur_approval')->cascadeOnDelete();
                $table->unsignedInteger('urutan')->default(1);
                $table->string('nama', 150);
                /*
                 * Role yang berhak memutuskan tahap ini. `nullOnDelete` supaya
                 * menghapus role tidak ikut menghapus jenjangnya — tahap yang
                 * kehilangan role-nya berhenti bisa diputus siapa pun kecuali
                 * super admin, dan itu memang keadaan yang harus terlihat,
                 * bukan tahap yang diam-diam lenyap dari alur.
                 */
                $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();
                /*
                 * Kotak struktur organisasi yang diwakili tahap ini. Inilah
                 * pengikat "jenjang persetujuan mengikuti struktur organisasi":
                 * yang tampil di panel adalah jabatan pada bagan, bukan sekadar
                 * nama role teknis.
                 */
                $table->foreignId('struktur_id')->nullable()->constrained('struktur_organisasi')->nullOnDelete();
                /** Batas waktu tahap ini (hari kalender); null = tanpa batas. */
                $table->unsignedSmallInteger('sla_hari')->nullable();
                /** Tahap boleh menolak — bila false, pilihannya hanya setuju/revisi. */
                $table->boolean('boleh_tolak')->default(true);
                $table->text('keterangan')->nullable();
                $table->boolean('is_active')->default(true);

                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->softDeletes();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

                $table->index(['alur_id', 'urutan']);
            });
        }

        if (!Schema::hasTable('approval_pengajuan')) {
            Schema::create('approval_pengajuan', function (Blueprint $table) {
                $table->id();
                // 'permohonan' | 'keberatan'
                $table->string('jenis', 20);
                /*
                 * Id baris pada `permohonan_informasi` atau `keberatan_informasi`.
                 * Tidak dipasangi foreign key karena menunjuk dua tabel; yang
                 * menjaga keutuhannya adalah penghapus di aplikasi. Sebagai
                 * gantinya baris langkah ikut terhapus lewat kode, bukan cascade.
                 */
                $table->unsignedBigInteger('pengajuan_id');
                $table->foreignId('alur_id')->nullable()->constrained('alur_approval')->nullOnDelete();
                $table->foreignId('tahap_id')->nullable()->constrained('alur_approval_tahap')->nullOnDelete();
                $table->unsignedInteger('urutan')->default(1);
                /** Salinan definisi tahap saat langkah dibuat (lihat catatan kelas). */
                $table->string('nama_tahap', 150);
                $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();
                $table->string('nama_jabatan', 150)->nullable();
                // 'menunggu' | 'disetujui' | 'ditolak' | 'revisi' | 'dilewati'
                $table->string('status', 20)->default('menunggu');
                $table->text('catatan')->nullable();
                $table->foreignId('diputus_oleh')->nullable()->constrained('users')->nullOnDelete();
                $table->timestampTz('tanggal_masuk')->nullable();
                $table->timestampTz('batas_waktu')->nullable();
                $table->timestampTz('tanggal_putusan')->nullable();
                $table->timestamp('created_at')->nullable();

                $table->index(['jenis', 'pengajuan_id', 'urutan']);
                $table->index(['status', 'role_id']);
            });

            $this->check('approval_pengajuan', 'approval_pengajuan_jenis_check', 'jenis', ['permohonan', 'keberatan']);
            $this->check('approval_pengajuan', 'approval_pengajuan_status_check', 'status', [
                'menunggu', 'disetujui', 'ditolak', 'revisi', 'dilewati',
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_pengajuan');
        Schema::dropIfExists('alur_approval_tahap');
        Schema::dropIfExists('alur_approval');
    }
};
