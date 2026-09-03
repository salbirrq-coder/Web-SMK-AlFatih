<?php
require_once __DIR__ . '/config.php';

/**
 * Koneksi database (PDO) dengan koneksi singleton.
 */
function db() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            die('Koneksi database gagal. Pastikan MySQL berjalan dan database telah dibuat. Error: ' . htmlspecialchars($e->getMessage()));
        }
    }
    return $pdo;
}

/**
 * Membuat database dan tabel jika belum ada (otomatis saat pertama kali).
 */
function ensure_database() {
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';charset=utf8mb4', DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE " . DB_NAME);

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS admin (
              id INT AUTO_INCREMENT PRIMARY KEY,
              email VARCHAR(150) NOT NULL UNIQUE,
              username VARCHAR(50) NOT NULL UNIQUE,
              password VARCHAR(255) NOT NULL,
              name VARCHAR(100) NOT NULL,
              created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        // Check & seed admin
        $stmt = $pdo->query("SELECT COUNT(*) AS c FROM admin");
        if ((int)$stmt->fetch()['c'] === 0) {
            $hash = password_hash('admin123', PASSWORD_BCRYPT);
            $pdo->prepare("INSERT INTO admin (email, username, password, name) VALUES (?,?,?,?)")
                ->execute(['admin@smkta.sch.id', 'admin', $hash, 'Administrator']);
        }

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS program_keahlian (
              id INT AUTO_INCREMENT PRIMARY KEY,
              nama VARCHAR(150) NOT NULL,
              deskripsi TEXT,
              icon VARCHAR(10) DEFAULT '🎓',
              gambar VARCHAR(255),
              created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        $c = $pdo->query("SELECT COUNT(*) AS c FROM program_keahlian")->fetch()['c'];
        if ((int)$c === 0) {
            $ins = $pdo->prepare("INSERT INTO program_keahlian (nama, deskripsi, icon) VALUES (?,?,?)");
            $ins->execute(['Rekayasa Perangkat Lunak', 'Mempelajari pengembangan perangkat lunak, aplikasi mobile, dan web development dengan standar industri.', '💻']);
            $ins->execute(['Teknik Jaringan Komputer dan Telekomunikasi', 'Mempelajari jaringan komputer, telekomunikasi, server, dan keamanan sistem.', '🌐']);
            $ins->execute(['Multimedia', 'Mempelajari desain grafis, animasi, videografi, dan produksi konten digital.', '🎨']);
            $ins->execute(['Akuntansi dan Keuangan Lembaga', 'Mempelajari akuntansi, pengelolaan keuangan, dan aplikasi keuangan digital.', '📊']);
        }

        $pdo->exec("
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
              tanggal_daftar TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        // Migrasi: pastikan kolom catatan ada (untuk DB lama)
        $cols = $pdo->query("SHOW COLUMNS FROM pendaftar LIKE 'catatan'")->fetchAll();
        if (empty($cols)) {
            $pdo->exec("ALTER TABLE pendaftar ADD COLUMN catatan TEXT NULL AFTER status");
        }

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS berita (
              id INT AUTO_INCREMENT PRIMARY KEY,
              judul VARCHAR(255) NOT NULL,
              kategori VARCHAR(50) NOT NULL,
              deskripsi TEXT,
              gambar VARCHAR(255),
              tanggal DATE,
              created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        $c = $pdo->query("SELECT COUNT(*) AS c FROM berita")->fetch()['c'];
        if ((int)$c === 0) {
            $ins = $pdo->prepare("INSERT INTO berita (judul, kategori, deskripsi, tanggal) VALUES (?,?,?,CURDATE())");
            $ins->execute(['Pendaftaran Peserta Didik Baru Tahun Ajaran 2026/2027', 'Pendaftaran', 'SMK Tahfizh Al-Fatih membuka pendaftaran peserta didik baru untuk tahun ajaran 2026/2027. Kuota terbatas. Segera daftarkan diri Anda!']);
            $ins->execute(["Kegiatan Tahfizh Al-Qur'an Siswa SMK Tahfizh Al-Fatih", 'Kegiatan', "Program tahfizh Al-Qur'an berjalan lancar. Siswa secara rutin menghafal Al-Qur'an dengan bimbingan ustadz dan ustadzah profesional."]);
            $ins->execute(['Prestasi Siswa SMK Tahfizh Al-Fatih', 'Prestasi', 'Selamat kepada siswa-siswi SMK Tahfizh Al-Fatih yang meraih prestasi dalam berbagai kompetisi. Kami bangga dengan pencapaian Anda!']);
        }

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS fasilitas (
              id INT AUTO_INCREMENT PRIMARY KEY,
              nama VARCHAR(150) NOT NULL,
              deskripsi TEXT,
              gambar VARCHAR(255),
              created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        $c = $pdo->query("SELECT COUNT(*) AS c FROM fasilitas")->fetch()['c'];
        if ((int)$c === 0) {
            $ins = $pdo->prepare("INSERT INTO fasilitas (nama, deskripsi) VALUES (?,?)");
            $fs = [
                ['Ruang Kelas', 'Ruang kelas nyaman dan modern dilengkapi dengan AC dan proyektor.'],
                ['Laboratorium Komputer', 'Lab komputer dengan spesifikasi tinggi untuk pembelajaran coding dan jaringan.'],
                ['Perpustakaan', 'Perpustakaan dengan koleksi buku lengkap dan area baca yang nyaman.'],
                ['Masjid', 'Masjid untuk kegiatan ibadah dan pembelajaran tahfizh Al-Qur\'an.'],
                ['Lapangan', 'Lapangan olahraga multifungsi untuk berbagai kegiatan siswa.'],
                ['Ruang Tahfizh', 'Ruang khusus untuk kegiatan menghafal dan pembelajaran Al-Qur\'an.'],
            ];
            foreach ($fs as $f) $ins->execute($f);
        }

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS pengumuman (
              id INT AUTO_INCREMENT PRIMARY KEY,
              judul VARCHAR(255) NOT NULL,
              isi TEXT,
              tanggal DATE,
              created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        $c = $pdo->query("SELECT COUNT(*) AS c FROM pengumuman")->fetch()['c'];
        if ((int)$c === 0) {
            $pdo->prepare("INSERT INTO pengumuman (judul, isi, tanggal) VALUES (?,?,CURDATE())")
                ->execute(['Pengumuman Hasil Seleksi PPDB', 'Hasil seleksi PPDB akan diumumkan melalui halaman pengumuman. Siswa dapat mengecek hasil seleksi dengan memasukkan nomor pendaftaran dan tanggal lahir.']);
        }
    } catch (PDOException $e) {
        // silent - db() akan menampilkan error
    }
}
