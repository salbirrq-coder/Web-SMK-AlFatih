-- ============================================================
-- SMK Tahfizh Al-Fatih - Database Schema
-- Database: smk_tahfizh
-- ============================================================

CREATE DATABASE IF NOT EXISTS smk_tahfizh
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE smk_tahfizh;

-- Admin
CREATE TABLE IF NOT EXISTS admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(150) NOT NULL UNIQUE,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  name VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Program Keahlian
CREATE TABLE IF NOT EXISTS program_keahlian (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(150) NOT NULL,
  deskripsi TEXT,
  icon VARCHAR(10) DEFAULT '🎓',
  gambar VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Pendaftar
CREATE TABLE IF NOT EXISTS pendaftar (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nomor_pendaftaran VARCHAR(30) NOT NULL UNIQUE,
  nama_lengkap VARCHAR(150) NOT NULL,
  nisn VARCHAR(16) NOT NULL,
  nik VARCHAR(16) NOT NULL,
  tempat_lahir VARCHAR(100) NOT NULL,
  tanggal_lahir DATE NOT NULL,
  jenis_kelamin VARCHAR(20) NOT NULL,
  agama VARCHAR(20) NOT NULL,
  whatsapp VARCHAR(20) NOT NULL,
  email VARCHAR(150),
  alamat TEXT NOT NULL,
  asal_sekolah VARCHAR(150) NOT NULL,
  tahun_lulus VARCHAR(10),
  nama_ayah VARCHAR(150) NOT NULL,
  nama_ibu VARCHAR(150) NOT NULL,
  wa_orang_tua VARCHAR(20),
  pekerjaan_orang_tua VARCHAR(100),
  program_keahlian_id INT,
  file_foto VARCHAR(255),
  file_kk VARCHAR(255),
  file_dokumen VARCHAR(255),
  status ENUM('MENUNGGU','DIVERIFIKASI','DITERIMA','TIDAK DITERIMA') DEFAULT 'MENUNGGU',
  catatan TEXT,
  tanggal_daftar TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (program_keahlian_id) REFERENCES program_keahlian(id) ON DELETE SET NULL
);

-- Berita
CREATE TABLE IF NOT EXISTS berita (
  id INT AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(255) NOT NULL,
  kategori VARCHAR(50) NOT NULL,
  deskripsi TEXT,
  gambar VARCHAR(255),
  tanggal DATE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Fasilitas
CREATE TABLE IF NOT EXISTS fasilitas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(150) NOT NULL,
  deskripsi TEXT,
  gambar VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Pengumuman
CREATE TABLE IF NOT EXISTS pengumuman (
  id INT AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(255) NOT NULL,
  isi TEXT,
  tanggal DATE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- Seed Data
-- ============================================================

-- Admin default: username="admin", password="admin123"
INSERT INTO admin (email, username, password, name) VALUES
('admin@smkta.sch.id', 'admin', '$2y$10$Y1qBYHQsFfbuQGEZ/aiYf..NvU6LbrmbBKr2TinMiKxPugJYEGLKy', 'Administrator')
ON DUPLICATE KEY UPDATE id = id;

-- Program Keahlian
INSERT INTO program_keahlian (nama, deskripsi, icon) VALUES
('Rekayasa Perangkat Lunak', 'Mempelajari pengembangan perangkat lunak, aplikasi mobile, dan web development dengan standar industri.', '💻'),
('Teknik Jaringan Komputer dan Telekomunikasi', 'Mempelajari jaringan komputer, telekomunikasi, server, dan keamanan sistem.', '🌐'),
('Multimedia', 'Mempelajari desain grafis, animasi, videografi, dan produksi konten digital.', '🎨'),
('Akuntansi dan Keuangan Lembaga', 'Mempelajari akuntansi, pengelolaan keuangan, dan aplikasi keuangan digital.', '📊')
ON DUPLICATE KEY UPDATE nama = VALUES(nama);

-- Fasilitas
INSERT INTO fasilitas (nama, deskripsi) VALUES
('Ruang Kelas', 'Ruang kelas nyaman dan modern dilengkapi dengan AC dan proyektor.'),
('Laboratorium Komputer', 'Lab komputer dengan spesifikasi tinggi untuk pembelajaran coding dan jaringan.'),
('Perpustakaan', 'Perpustakaan dengan koleksi buku lengkap dan area baca yang nyaman.'),
('Masjid', 'Masjid untuk kegiatan ibadah dan pembelajaran tahfizh Al-Qur''an.'),
('Lapangan', 'Lapangan olahraga multifungsi untuk berbagai kegiatan siswa.'),
('Ruang Tahfizh', 'Ruang khusus untuk kegiatan menghafal dan pembelajaran Al-Qur''an.');

-- Berita
INSERT INTO berita (judul, kategori, deskripsi, tanggal) VALUES
('Pendaftaran Peserta Didik Baru Tahun Ajaran 2026/2027', 'Pendaftaran', 'SMK Tahfizh Al-Fatih membuka pendaftaran peserta didik baru untuk tahun ajaran 2026/2027. Kuota terbatas. Segera daftarkan diri Anda!', CURDATE()),
('Kegiatan Tahfizh Al-Qur''an Siswa SMK Tahfizh Al-Fatih', 'Kegiatan', 'Program tahfizh Al-Qur''an berjalan lancar. Siswa secara rutin menghafal Al-Qur''an dengan bimbingan ustadz dan ustadzah profesional.', CURDATE()),
('Prestasi Siswa SMK Tahfizh Al-Fatih', 'Prestasi', 'Selamat kepada siswa-siswi SMK Tahfizh Al-Fatih yang meraih prestasi dalam berbagai kompetisi. Kami bangga dengan pencapaian Anda!', CURDATE());

-- Pengumuman
INSERT INTO pengumuman (judul, isi, tanggal) VALUES
('Pengumuman Hasil Seleksi PPDB', 'Hasil seleksi PPDB akan diumumkan melalui halaman pengumuman. Siswa dapat mengecek hasil seleksi dengan memasukkan nomor pendaftaran dan tanggal lahir.', CURDATE());
