# Web SMK AlFatih

Website resmi **SMK Tahfizh Al-Fatih** — sistem informasi sekolah dengan PPDB (Pendaftaran Peserta Didik Baru) dan panel admin.

Dibangun dengan **PHP + MySQL (XAMPP)** tanpa framework.

## Fitur

- Beranda (hero, program keahlian, fasilitas, berita)
- Pendaftaran Peserta Didik Baru (PPDB) dengan nomor pendaftaran otomatis
- Cek Status Pendaftaran
- Cek Kelulusan
- Pengumuman
- Halaman Kontak + QR code lokasi
- **Panel Admin**:
  - Dashboard ringkasan
  - Kelola pendaftar (verifikasi, terima/tolak dengan catatan)
  - Kelola pengumuman, program keahlian, berita, fasilitas
  - Login admin

## Teknologi

- PHP 8+
- MySQL / MariaDB (via XAMPP)
- HTML, CSS, JavaScript
- Font Awesome, Google Fonts

## Cara Menjalankan (XAMPP)

1. Salin folder `smkTahfizh` ke `C:\xampp\htdocs\` (jalankan sebagai `http://localhost/smkTahfizh/`).
2. Pastikan Apache dan MySQL aktif di XAMPP.
3. Database dan tabel akan dibuat otomatis oleh `includes/db.php` saat halaman pertama dibuka.

## Akun Admin Default

- **Username:** `admin`
- **Password:** `admin123`

> **Penting:** Ganti password admin default setelah login pertama.

## Struktur

- `index.php` — beranda publik
- `pendaftaran.php` — form PPDB
- `cek-status.php` — cek status pendaftaran
- `cek-kelulusan.php` — cek kelulusan
- `pengumuman.php` — daftar pengumuman
- `kontak.php` — kontak & QR lokasi
- `admin/` — panel admin
- `includes/` — konfigurasi, koneksi DB, fungsi bantu, header/footer
- `database/` — schema database
- `assets/` — CSS, JS, gambar
