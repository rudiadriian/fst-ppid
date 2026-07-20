<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PPID FSTJ — Transparansi Informasi Publik</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Noto+Sans:wght@400;500;600&family=Roboto+Mono:wght@500;600&display=swap" rel="stylesheet">
<style>
  :root{
    --green-deep:#0A3B2A;    /* hijau tua utama - identitas korporat */
    --green-mid:#0F5C3F;     /* hijau pertengahan untuk gradasi */
    --green-bright:#1B8354;  /* hijau terang aksen/hover */
    --black:#14170F;         /* hitam kehijauan, dipakai footer & teks berat */
    --gold:#C6A15B;          /* emas resmi - lambang, garis, badge */
    --gold-deep:#9C7C3A;     /* emas gelap untuk hover/kontras teks */
    --mist:#F6F7F2;          /* latar terang, hijau sangat muda */
    --line:#E1E5DC;          /* garis pembatas */
    --ink:#2B3026;           /* teks isi */
  }
  body{font-family:'Noto Sans',sans-serif; color:var(--ink); background:#fff;}
  .font-display{font-family:'Plus Jakarta Sans',sans-serif;}
  .font-mono{font-family:'Roboto Mono',monospace;}
  .grad-hero{
    background: linear-gradient(155deg, var(--black) 0%, var(--green-deep) 42%, var(--green-mid) 78%, var(--green-bright) 100%);
  }
  .grad-line{
    height:4px;
    background: linear-gradient(90deg, var(--gold) 0%, var(--green-bright) 35%, var(--green-deep) 70%, var(--black) 100%);
  }
  .badge{
    font-family:'Roboto Mono',monospace;
    font-size:11px; letter-spacing:.05em;
    color:var(--green-deep);
    background:var(--mist);
    border:1px solid var(--line);
    padding:5px 12px; border-radius:2px;
    display:inline-block;
  }
  .badge-dark{
    font-family:'Roboto Mono',monospace;
    font-size:11px; letter-spacing:.05em;
    color:var(--gold);
    background:rgba(255,255,255,.06);
    border:1px solid rgba(198,161,91,.35);
    padding:5px 12px; border-radius:2px;
    display:inline-block;
  }
  .menu-panel{display:none;}
  .menu-item:hover .menu-panel{display:block;}
  input:focus, button:focus, a:focus{outline: 2px solid var(--gold); outline-offset: 2px;}
  .book-cover{aspect-ratio: 3/4;}
  .emblem-ring{
    background: conic-gradient(from 180deg, var(--gold), #E8D7A8, var(--gold));
  }
  .gold-rule::after{
    content:"";
    display:block;
    width:56px;
    height:3px;
    background:var(--gold);
    margin-top:14px;
  }
</style>
</head>
<body class="bg-white">

<!-- ============ TOP BAR RESMI ============ -->
<div class="bg-[var(--black)] text-white/70 text-[11px]">
  <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-8">
    <span>Pemerintah Provinsi DKI Jakarta — Badan Usaha Milik Daerah</span>
    <div class="flex items-center gap-4">
      <a href="#" class="hover:text-[var(--gold)] transition">Peta Situs</a>
      <span class="w-px h-3 bg-white/15"></span>
      <a href="#" class="hover:text-[var(--gold)] transition">Bahasa Indonesia</a>
    </div>
  </div>
</div>

<!-- ============ HEADER + MEGA MENU ============ -->
<header class="bg-white sticky top-0 z-40 shadow-sm">
  <div class="grad-line"></div>
  <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-24">
      <div class="flex items-center gap-4">
        <div class="w-14 h-14 rounded emblem-ring p-[2px]">
          <div class="w-full h-full rounded bg-[var(--green-deep)] flex items-center justify-center">
            <span class="text-white font-display font-black text-lg">FS</span>
          </div>
        </div>
        <div>
          <p class="font-display font-extrabold text-[var(--green-deep)] leading-tight text-lg">PPID FSTJ</p>
          <p class="text-xs text-[var(--ink)]/60 leading-tight">PT Food Station Tjipinang Jaya (Perseroda)</p>
        </div>
      </div>

      <nav class="hidden lg:flex items-stretch h-24 font-display font-semibold text-sm text-[var(--green-deep)]">
        <div class="menu-item relative flex items-center px-5 hover:bg-[var(--mist)] cursor-pointer transition">
          <span>Tentang</span>
          <div class="menu-panel absolute top-full left-0 bg-white border border-[var(--line)] shadow-lg w-64 py-2 z-50">
            <a href="#" class="block px-5 py-2.5 text-sm font-medium hover:bg-[var(--mist)] hover:text-[var(--green-deep)]">Dasar Hukum</a>
            <a href="#" class="block px-5 py-2.5 text-sm font-medium hover:bg-[var(--mist)] hover:text-[var(--green-deep)]">Profil Singkat</a>
            <a href="#" class="block px-5 py-2.5 text-sm font-medium hover:bg-[var(--mist)] hover:text-[var(--green-deep)]">Visi &amp; Misi</a>
            <a href="#" class="block px-5 py-2.5 text-sm font-medium hover:bg-[var(--mist)] hover:text-[var(--green-deep)]">Struktur Organisasi PPID</a>
            <a href="#" class="block px-5 py-2.5 text-sm font-medium hover:bg-[var(--mist)] hover:text-[var(--green-deep)]">Maklumat Pelayanan Informasi</a>
          </div>
        </div>
        <div class="menu-item relative flex items-center px-5 hover:bg-[var(--mist)] cursor-pointer transition">
          <span>Informasi Publik</span>
          <div class="menu-panel absolute top-full left-0 bg-white border border-[var(--line)] shadow-lg w-72 py-2 z-50">
            <a href="#" class="block px-5 py-2.5 text-sm font-medium hover:bg-[var(--mist)] hover:text-[var(--green-deep)]">Daftar Informasi Publik</a>
            <a href="#" class="block px-5 py-2.5 text-sm font-medium hover:bg-[var(--mist)] hover:text-[var(--green-deep)]">Informasi Berkala</a>
            <a href="#" class="block px-5 py-2.5 text-sm font-medium hover:bg-[var(--mist)] hover:text-[var(--green-deep)]">Informasi Serta Merta</a>
            <a href="#" class="block px-5 py-2.5 text-sm font-medium hover:bg-[var(--mist)] hover:text-[var(--green-deep)]">Informasi Setiap Saat</a>
            <a href="#" class="block px-5 py-2.5 text-sm font-medium hover:bg-[var(--mist)] hover:text-[var(--green-deep)]">Laporan Tahunan</a>
          </div>
        </div>
        <div class="menu-item relative flex items-center px-5 hover:bg-[var(--mist)] cursor-pointer transition">
          <span>Layanan Informasi</span>
          <div class="menu-panel absolute top-full left-0 bg-white border border-[var(--line)] shadow-lg w-80 py-2 z-50">
            <a href="#" class="block px-5 py-2.5 text-sm font-medium hover:bg-[var(--mist)] hover:text-[var(--green-deep)]">Permohonan Informasi Publik</a>
            <a href="#" class="block px-5 py-2.5 text-sm font-medium hover:bg-[var(--mist)] hover:text-[var(--green-deep)]">Pengajuan Keberatan Informasi</a>
            <a href="#" class="block px-5 py-2.5 text-sm font-medium hover:bg-[var(--mist)] hover:text-[var(--green-deep)]">Status Permohonan</a>
            <a href="#" class="block px-5 py-2.5 text-sm font-medium hover:bg-[var(--mist)] hover:text-[var(--green-deep)]">Alur Mekanisme Pelayanan</a>
          </div>
        </div>
        <a href="#laporan" class="flex items-center px-5 hover:bg-[var(--mist)] transition">Laporan</a>
        <a href="#kontak" class="flex items-center px-5 hover:bg-[var(--mist)] transition">Kontak</a>
      </nav>

      <a href="#layanan" class="hidden md:inline-flex items-center bg-[var(--green-deep)] hover:bg-[var(--black)] text-white font-display font-bold text-sm px-5 py-2.5 rounded transition">
        Ajukan Permohonan
      </a>
      <button class="lg:hidden text-[var(--green-deep)]" aria-label="Buka menu">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
      </button>
    </div>
  </div>
</header>

<main>

<!-- ============ HERO — GRADASI HIJAU-HITAM-EMAS ============ -->
<section class="relative grad-hero overflow-hidden">
  <div class="absolute inset-0 opacity-[0.05]" style="background-image: repeating-linear-gradient(45deg, #fff 0 1px, transparent 1px 30px);"></div>

  <div class="relative max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-10 text-center">
    <span class="badge-dark">UU No. 14 Tahun 2008 tentang Keterbukaan Informasi Publik</span>
    <h1 class="font-display font-extrabold text-white text-3xl sm:text-4xl lg:text-5xl leading-tight mt-6 mb-4 max-w-3xl mx-auto">
      Layanan Informasi Publik <span class="text-[var(--gold)]">PT Food Station Tjipinang Jaya</span>
    </h1>
    <p class="text-white/70 max-w-2xl mx-auto mb-10">
      Akses permohonan, keberatan, dan dokumen informasi publik secara resmi, terbuka, dan akuntabel.
    </p>

    <!-- Ikon Layanan Utama -->
    <div class="grid grid-cols-2 gap-4 max-w-2xl mx-auto mb-4">
      <a href="{{ route('ppid.request') ?? '#' }}" class="bg-white rounded p-6 flex flex-col items-center gap-3 hover:-translate-y-1 hover:shadow-2xl transition duration-200 border-b-4 border-[var(--gold)]">
        <div class="w-14 h-14 rounded bg-[var(--green-deep)] flex items-center justify-center">
          <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        </div>
        <span class="font-display font-bold text-[var(--green-deep)] text-sm text-center leading-snug">Permohonan<br>Informasi Publik</span>
      </a>
      <a href="{{ route('ppid.objection') ?? '#' }}" class="bg-white rounded p-6 flex flex-col items-center gap-3 hover:-translate-y-1 hover:shadow-2xl transition duration-200 border-b-4 border-[var(--gold)]">
        <div class="w-14 h-14 rounded bg-[var(--black)] flex items-center justify-center">
          <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.398 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <span class="font-display font-bold text-[var(--green-deep)] text-sm text-center leading-snug">Pengajuan<br>Keberatan Informasi</span>
      </a>
    </div>
    <div class="grid grid-cols-3 gap-4 max-w-2xl mx-auto">
      <a href="#" class="bg-white/95 rounded py-4 px-2 flex flex-col items-center gap-2 hover:bg-white transition duration-200">
        <svg class="w-6 h-6 text-[var(--green-deep)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        <span class="font-display font-semibold text-[var(--green-deep)] text-xs text-center">Informasi Berkala</span>
      </a>
      <a href="#" class="bg-white/95 rounded py-4 px-2 flex flex-col items-center gap-2 hover:bg-white transition duration-200">
        <svg class="w-6 h-6 text-[var(--green-deep)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        <span class="font-display font-semibold text-[var(--green-deep)] text-xs text-center">Informasi Serta Merta</span>
      </a>
      <a href="#" class="bg-white/95 rounded py-4 px-2 flex flex-col items-center gap-2 hover:bg-white transition duration-200">
        <svg class="w-6 h-6 text-[var(--green-deep)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span class="font-display font-semibold text-[var(--green-deep)] text-xs text-center">Informasi Setiap Saat</span>
      </a>
    </div>
  </div>
</section>

<!-- ============ PENCARIAN + STATISTIK RINGKAS ============ -->
<section class="bg-[var(--mist)] border-b border-[var(--line)]">
  <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid lg:grid-cols-12 gap-6 items-center">
      <div class="lg:col-span-5">
        <form class="flex items-stretch bg-white rounded border border-[var(--line)] overflow-hidden">
          <input type="text" placeholder="Cari dokumen, regulasi, atau laporan..." class="w-full px-4 py-3 text-sm border-0 focus:ring-0">
          <button type="submit" class="bg-[var(--green-deep)] text-white font-display font-bold text-sm px-6 hover:bg-[var(--black)] transition">Cari</button>
        </form>
      </div>
      <div class="lg:col-span-7 grid grid-cols-4 gap-3 text-center">
        <div class="bg-white border border-[var(--line)] rounded py-3">
          <p class="font-mono font-semibold text-xl text-[var(--green-deep)]">318</p>
          <p class="text-[10px] text-[var(--ink)]/60 mt-1 leading-tight">Permohonan<br>Diterima</p>
        </div>
        <div class="bg-white border border-[var(--line)] rounded py-3">
          <p class="font-mono font-semibold text-xl text-[var(--green-bright)]">95%</p>
          <p class="text-[10px] text-[var(--ink)]/60 mt-1 leading-tight">Selesai<br>Tepat Waktu</p>
        </div>
        <div class="bg-white border border-[var(--line)] rounded py-3">
          <p class="font-mono font-semibold text-xl text-[var(--black)]">2.4</p>
          <p class="text-[10px] text-[var(--ink)]/60 mt-1 leading-tight">Hari<br>Rata-rata</p>
        </div>
        <div class="bg-white border border-[var(--line)] rounded py-3">
          <p class="font-mono font-semibold text-xl text-[var(--gold-deep)]">92.4</p>
          <p class="text-[10px] text-[var(--ink)]/60 mt-1 leading-tight">Indeks<br>Keterbukaan</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============ SAMBUTAN PPID ============ -->
<section class="py-16 bg-white">
  <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid lg:grid-cols-12 gap-10 items-center">
      <div class="lg:col-span-4">
        <div class="w-full aspect-[4/5] bg-[var(--mist)] border border-[var(--line)] rounded flex items-center justify-center">
          <svg class="w-20 h-20 text-[var(--line)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </div>
      </div>
      <div class="lg:col-span-8">
        <span class="badge">Sambutan</span>
        <h2 class="font-display font-extrabold text-2xl sm:text-3xl text-[var(--green-deep)] mt-4 gold-rule">Komitmen Keterbukaan Informasi Publik</h2>
        <p class="text-[var(--ink)]/80 leading-relaxed mt-5 max-w-2xl">
          Sebagai Badan Usaha Milik Daerah Provinsi DKI Jakarta, PT Food Station Tjipinang Jaya (Perseroda) berkomitmen menjalankan amanat Undang-Undang Keterbukaan Informasi Publik secara konsisten. Kami memastikan setiap permohonan informasi dilayani secara profesional, tepat waktu, dan dapat dipertanggungjawabkan kepada masyarakat.
        </p>
        <p class="font-display font-bold text-[var(--green-deep)] mt-6">Pejabat Pengelola Informasi dan Dokumentasi</p>
        <p class="text-sm text-[var(--ink)]/60">PT Food Station Tjipinang Jaya (Perseroda)</p>
      </div>
    </div>
  </div>
</section>

<!-- ============ BERITA TERBARU ============ -->
<section id="berita" class="py-16 bg-[var(--mist)] border-y border-[var(--line)]">
  <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-8 pb-4" style="border-bottom: 3px solid var(--green-deep);">
      <h2 class="font-display font-extrabold text-2xl text-[var(--green-deep)]">Berita Terbaru</h2>
      <a href="#" class="text-sm font-display font-bold text-[var(--green-bright)] hover:text-[var(--gold-deep)] transition whitespace-nowrap">Lihat Semua Berita →</a>
    </div>

    <div class="bg-white divide-y divide-[var(--line)] border border-[var(--line)]">
      <article class="p-5 flex gap-5 items-start">
        <img src="https://images.unsplash.com/photo-1586201375761-83865001e31c?q=80&w=400&auto=format&fit=crop" alt="" class="w-32 h-24 sm:w-44 sm:h-28 object-cover rounded shrink-0">
        <div class="min-w-0">
          <p class="font-mono text-xs text-[var(--ink)]/50 mb-1.5">10 DESEMBER 2025 · OPERASIONAL</p>
          <h3 class="font-display font-bold text-[var(--green-deep)] mb-1.5 leading-snug">
            <a href="#" class="hover:text-[var(--gold-deep)] transition">Food Station Jaga Stabilitas Pasokan Beras Jelang Hari Raya</a>
          </h3>
          <p class="text-sm text-[var(--ink)]/60 line-clamp-2 hidden sm:block">PT Food Station Tjipinang Jaya terus berkomitmen dalam menjaga ketahanan pangan dan transparansi publik di wilayah DKI Jakarta.</p>
        </div>
      </article>
      <article class="p-5 flex gap-5 items-start">
        <img src="https://images.unsplash.com/photo-1531403009284-440f080d1e12?q=80&w=400&auto=format&fit=crop" alt="" class="w-32 h-24 sm:w-44 sm:h-28 object-cover rounded shrink-0">
        <div class="min-w-0">
          <p class="font-mono text-xs text-[var(--ink)]/50 mb-1.5">05 DESEMBER 2025 · PRESTASI</p>
          <h3 class="font-display font-bold text-[var(--green-deep)] mb-1.5 leading-snug">
            <a href="#" class="hover:text-[var(--gold-deep)] transition">Raih Penghargaan Top BUMD 2025 Kategori Transparansi Publik</a>
          </h3>
          <p class="text-sm text-[var(--ink)]/60 line-clamp-2 hidden sm:block">Penghargaan ini menjadi bukti komitmen perusahaan terhadap keterbukaan informasi kepada masyarakat.</p>
        </div>
      </article>
      <article class="p-5 flex gap-5 items-start">
        <img src="https://images.unsplash.com/photo-1488459716781-31db52582fe9?q=80&w=400&auto=format&fit=crop" alt="" class="w-32 h-24 sm:w-44 sm:h-28 object-cover rounded shrink-0">
        <div class="min-w-0">
          <p class="font-mono text-xs text-[var(--ink)]/50 mb-1.5">01 DESEMBER 2025 · CSR</p>
          <h3 class="font-display font-bold text-[var(--green-deep)] mb-1.5 leading-snug">
            <a href="#" class="hover:text-[var(--gold-deep)] transition">Program Pangan Murah Bersubsidi Kembali Digelar di 5 Wilayah</a>
          </h3>
          <p class="text-sm text-[var(--ink)]/60 line-clamp-2 hidden sm:block">Program ini menyasar wilayah dengan kebutuhan pangan tinggi sebagai bentuk tanggung jawab sosial perusahaan.</p>
        </div>
      </article>
    </div>
  </div>
</section>

<!-- ============ LAPORAN TAHUNAN — RAK BUKU ============ -->
<section id="laporan" class="py-16 bg-white">
  <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-8 pb-4" style="border-bottom: 3px solid var(--green-deep);">
      <h2 class="font-display font-extrabold text-2xl text-[var(--green-deep)]">Laporan Tahunan Perusahaan</h2>
      <a href="#" class="text-sm font-display font-bold text-[var(--green-bright)] hover:text-[var(--gold-deep)] transition whitespace-nowrap">Lihat Arsip Lengkap →</a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-6">
      <div class="group cursor-pointer" onclick="openModal()">
        <div class="book-cover bg-[var(--green-deep)] rounded-sm shadow-md group-hover:shadow-2xl group-hover:-translate-y-1 transition duration-200 flex flex-col justify-end p-4 border-t-4 border-[var(--gold)]">
          <p class="font-mono text-white/50 text-[11px]">FSTJ</p>
          <p class="font-display font-bold text-white text-sm leading-snug mt-1">Laporan Keuangan &amp; Kinerja</p>
          <p class="font-display font-black text-[var(--gold)] text-2xl mt-2">2024</p>
        </div>
        <p class="text-xs text-[var(--ink)]/60 mt-2 text-center">Format PDF</p>
      </div>
      <div class="group cursor-pointer" onclick="openModal()">
        <div class="book-cover bg-[var(--black)] rounded-sm shadow-md group-hover:shadow-2xl group-hover:-translate-y-1 transition duration-200 flex flex-col justify-end p-4 border-t-4 border-[var(--gold)]">
          <p class="font-mono text-white/50 text-[11px]">FSTJ</p>
          <p class="font-display font-bold text-white text-sm leading-snug mt-1">Laporan Tahunan Perusahaan</p>
          <p class="font-display font-black text-[var(--gold)] text-2xl mt-2">2023</p>
        </div>
        <p class="text-xs text-[var(--ink)]/60 mt-2 text-center">Format PDF</p>
      </div>
      <div class="group cursor-pointer" onclick="openModal()">
        <div class="book-cover bg-[var(--green-bright)] rounded-sm shadow-md group-hover:shadow-2xl group-hover:-translate-y-1 transition duration-200 flex flex-col justify-end p-4 border-t-4 border-[var(--gold)]">
          <p class="font-mono text-white/60 text-[11px]">FSTJ</p>
          <p class="font-display font-bold text-white text-sm leading-snug mt-1">Laporan Keberlanjutan</p>
          <p class="font-display font-black text-white text-2xl mt-2">2021</p>
        </div>
        <p class="text-xs text-[var(--ink)]/60 mt-2 text-center">Format PDF</p>
      </div>
    </div>
  </div>
</section>

<!-- ============ KLASIFIKASI INFORMASI ============ -->
<section class="py-16 bg-[var(--mist)] border-y border-[var(--line)]">
  <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8 pb-4" style="border-bottom: 3px solid var(--green-deep);">
      <h2 class="font-display font-extrabold text-2xl text-[var(--green-deep)]">Klasifikasi Informasi Publik</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-px bg-[var(--line)] border border-[var(--line)]">
      <div class="bg-white p-7">
        <span class="badge">Pasal 9 UU KIP</span>
        <h3 class="font-display font-bold text-lg text-[var(--green-deep)] mt-4 mb-2">Informasi Berkala</h3>
        <p class="text-sm text-[var(--ink)]/70 leading-relaxed">Diumumkan secara rutin — laporan tahunan, kinerja, dan keuangan perusahaan.</p>
      </div>
      <div class="bg-white p-7">
        <span class="badge">Pasal 10 UU KIP</span>
        <h3 class="font-display font-bold text-lg text-[var(--green-deep)] mt-4 mb-2">Informasi Serta Merta</h3>
        <p class="text-sm text-[var(--ink)]/70 leading-relaxed">Menyangkut hajat hidup orang banyak, keselamatan, dan ketertiban umum.</p>
      </div>
      <div class="bg-white p-7">
        <span class="badge">Pasal 11 UU KIP</span>
        <h3 class="font-display font-bold text-lg text-[var(--green-deep)] mt-4 mb-2">Informasi Setiap Saat</h3>
        <p class="text-sm text-[var(--ink)]/70 leading-relaxed">Tersedia kapan pun diminta — regulasi, prosedur, dan profil layanan.</p>
      </div>
    </div>
  </div>
</section>

<!-- ============ KONTAK ============ -->
<section id="kontak" class="py-16 bg-white">
  <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white border border-[var(--line)] flex flex-col lg:flex-row">
      <div class="lg:w-2/5 p-8 lg:p-10">
        <h2 class="font-display font-extrabold text-xl text-[var(--green-deep)] mb-6 pb-3" style="border-bottom:3px solid var(--green-deep);">PPID Food Station Tjipinang Jaya</h2>
        <div class="space-y-5 text-sm">
          <div>
            <p class="font-display font-bold text-[var(--green-deep)] mb-1">Alamat</p>
            <p class="text-[var(--ink)]/70 leading-relaxed">Komplek Pasar Induk Beras Cipinang, Jl. Pisangan Lama Selatan No. 1, Jakarta Timur 13230</p>
          </div>
          <div>
            <p class="font-display font-bold text-[var(--green-deep)] mb-1">Telepon &amp; Email</p>
            <p class="text-[var(--ink)]/70">021-471 8011 (Ext. PPID)<br><a href="mailto:ppid@foodstation.co.id" class="text-[var(--green-bright)]">ppid@foodstation.co.id</a></p>
          </div>
          <div>
            <p class="font-display font-bold text-[var(--green-deep)] mb-1">Jadwal Pelayanan Informasi Publik</p>
            <p class="text-[var(--ink)]/70">Senin s.d. Jumat, 08.00–17.00 WIB<br>(Istirahat 12.00–13.00 WIB)</p>
          </div>
        </div>
      </div>
      <div class="lg:w-3/5 min-h-[320px] bg-slate-200"></div>
    </div>
  </div>
</section>

</main>

<!-- ============ FOOTER ============ -->
<footer class="bg-[var(--black)] text-white/70 pt-14 pb-6">
  <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-10 pb-10 border-b border-white/10">
      <div class="col-span-2">
        <p class="font-display font-extrabold text-white text-base mb-2">PPID FSTJ</p>
        <p class="text-sm text-white/50 leading-relaxed max-w-xs">PT Food Station Tjipinang Jaya (Perseroda) — Badan Usaha Milik Daerah Provinsi DKI Jakarta di bidang ketahanan pangan.</p>
      </div>
      <div>
        <p class="font-display font-bold text-white text-sm mb-3">Layanan</p>
        <ul class="space-y-2 text-sm text-white/50">
          <li><a href="#" class="hover:text-[var(--gold)] transition">Permohonan Informasi</a></li>
          <li><a href="#" class="hover:text-[var(--gold)] transition">Pengajuan Keberatan</a></li>
          <li><a href="#" class="hover:text-[var(--gold)] transition">Status Permohonan</a></li>
        </ul>
      </div>
      <div>
        <p class="font-display font-bold text-white text-sm mb-3">Tautan</p>
        <ul class="space-y-2 text-sm text-white/50">
          <li><a href="#" class="hover:text-[var(--gold)] transition">Profil PPID</a></li>
          <li><a href="#" class="hover:text-[var(--gold)] transition">Dasar Hukum</a></li>
          <li><a href="#" class="hover:text-[var(--gold)] transition">Laporan Tahunan</a></li>
        </ul>
      </div>
    </div>
    <p class="text-xs text-white/40 text-center mb-4">PPID FSTJ telah dikunjungi sebanyak 1.204 kali bulan ini dan 18.760 kali sejak diluncurkan.</p>
    <div class="text-xs text-white/40 flex flex-col sm:flex-row justify-between gap-2">
      <p>© 2025 PT Food Station Tjipinang Jaya (Perseroda). Seluruh hak cipta dilindungi.</p>
      <p>Situs resmi Badan Usaha Milik Daerah Provinsi DKI Jakarta.</p>
    </div>
  </div>
</footer>

<!-- ============ MODAL UNDUH ============ -->
<div id="downloadModal" class="fixed inset-0 z-[60] hidden items-center justify-center p-4">
  <div id="modalBackdrop" class="fixed inset-0 bg-[var(--black)]/70"></div>
  <div class="relative bg-white rounded shadow-2xl max-w-md w-full p-6 border-t-4 border-[var(--gold)]">
    <div class="flex justify-between items-center mb-5 pb-3 border-b border-[var(--line)]">
      <h3 class="font-display font-bold text-lg text-[var(--green-deep)]">Unduh Dokumen</h3>
      <button id="closeModal" class="text-[var(--ink)]/40 hover:text-[var(--ink)]">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <p class="text-sm text-[var(--ink)]/60 mb-4">Lengkapi data diri untuk mengunduh dokumen.</p>
    <div class="space-y-4">
      <div><label class="block text-xs font-display font-bold text-[var(--green-deep)] uppercase tracking-wide mb-1">Nama Lengkap</label><input type="text" class="w-full rounded border-[var(--line)] focus:border-[var(--green-bright)]"></div>
      <div><label class="block text-xs font-display font-bold text-[var(--green-deep)] uppercase tracking-wide mb-1">Email</label><input type="email" class="w-full rounded border-[var(--line)] focus:border-[var(--green-bright)]"></div>
      <button class="w-full bg-[var(--green-deep)] hover:bg-[var(--black)] text-white font-display font-bold py-3 rounded transition">Kirim Permintaan</button>
    </div>
  </div>
</div>

<script>
  function openModal(){
    const m = document.getElementById('downloadModal');
    m.classList.remove('hidden'); m.classList.add('flex');
  }
  document.getElementById('closeModal').addEventListener('click', ()=>{
    const m = document.getElementById('downloadModal');
    m.classList.add('hidden'); m.classList.remove('flex');
  });
  document.getElementById('modalBackdrop').addEventListener('click', ()=>{
    const m = document.getElementById('downloadModal');
    m.classList.add('hidden'); m.classList.remove('flex');
  });
</script>

</body>
</html>
