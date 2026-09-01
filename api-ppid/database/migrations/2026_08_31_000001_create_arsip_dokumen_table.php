<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Arsip dokumen petugas (langkah 95).
 *
 * Berkas tanggapan selama ini hanya hidup di dalam satu permohonan: petugas
 * mengunggahnya dari dialog rincian, dan begitu permohonan lain meminta dokumen
 * yang sama — SK, laporan tahunan, daftar informasi publik — berkasnya harus
 * diunggah ulang. Tidak ada tempat untuk melihat "dokumen apa saja yang biasa
 * kami kirimkan", dan tiap unggahan ulang menambah satu salinan baru di disk.
 *
 * Tabel ini menjadi tempat itu. Barisnya menunjuk berkas yang sudah tersimpan
 * di disk `media`; melampirkannya ke sebuah permohonan cukup menyalin
 * `path_file`-nya ke `permohonan_tanggapan_files`, tanpa unggahan kedua.
 *
 * `path_file` unik: satu berkas fisik hanya boleh punya satu baris arsip,
 * sehingga pencatatan otomatis dari dialog tanggapan tidak menumpuk duplikat
 * untuk berkas yang sama.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('arsip_dokumen')) {
            return;
        }

        Schema::create('arsip_dokumen', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 255);
            $table->text('keterangan')->nullable();
            // Pengelompokan bebas isi petugas (mis. "SK", "Laporan Tahunan").
            $table->string('kategori', 100)->nullable();
            // Path relatif di disk `media`, sama bentuknya dengan kolom berkas
            // modul lain (mis. uploads/permohonan/2026/08/xxx.pdf).
            $table->string('path_file', 500);
            $table->string('nama_file', 255)->nullable();
            $table->unsignedBigInteger('ukuran_file')->nullable();
            $table->string('tipe_file', 150)->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            // Satu berkas fisik = satu baris arsip.
            $table->unique('path_file');
            // Cara daftarnya dibaca panel: yang aktif, terbaru di atas.
            $table->index(['is_active', 'id']);
            $table->index('kategori');

            if (Schema::hasTable('users')) {
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
                $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
                $table->foreign('deleted_by')->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arsip_dokumen');
    }
};
