<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

ensure_database();
$pdo = db();
$programs = $pdo->query("SELECT * FROM program_keahlian ORDER BY id")->fetchAll();
$errors = [];
$nomor = null;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $d = $_POST;

    // Validate required
    $required = ['nama_lengkap','nisn','nik','tempat_lahir','tanggal_lahir','jenis_kelamin','agama','whatsapp','alamat','asal_sekolah','nama_ayah','nama_ibu','program_keahlian_id'];
    foreach ($required as $field) {
        if (empty($d[$field])) {
            $errors[] = 'Data belum lengkap. Mohon isi semua field wajib.';
            break;
        }
    }

    if (empty($errors)) {
        if (!empty($d['email']) && !filter_var($d['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid.';
        }
        if (!preg_match('/^[0-9]{10,16}$/', preg_replace('/\s+/', '', $d['nisn']))) {
            $errors[] = 'NISN harus berupa 10-16 digit angka.';
        }
        // Validate program exists
        $progCheck = $pdo->prepare("SELECT id FROM program_keahlian WHERE id = ?");
        $progCheck->execute([$d['program_keahlian_id']]);
        if (!$progCheck->fetch()) {
            $errors[] = 'Program keahlian tidak valid.';
        }
        if (empty($d['nama_ibu']) || empty($d['nama_ayah'])) {
            $errors[] = 'Nama orang tua wajib diisi.';
        }
    }

    // Upload files
    $foto = null; $kk = null; $dokumen = null;
    if (empty($errors)) {
        $fotoRes = handle_upload('file_foto', 'pendaftar', true);
        if (isset($fotoRes['error'])) $errors[] = 'Foto: ' . $fotoRes['error'];
        else $foto = $fotoRes['filename'];
    }
    if (empty($errors)) {
        $kkRes = handle_upload('file_kk', 'pendaftar', true);
        if (isset($kkRes['error'])) $errors[] = 'Kartu Keluarga: ' . $kkRes['error'];
        else $kk = $kkRes['filename'];
    }
    if (empty($errors)) {
        $dokRes = handle_upload('file_dokumen', 'pendaftar', false);
        if (isset($dokRes['error'])) $errors[] = 'Dokumen: ' . $dokRes['error'];
        else $dokumen = $dokRes['filename'];
    }

    // Insert
    if (empty($errors)) {
        $nomor = generate_nomor_pendaftaran();
        try {
            $stmt = $pdo->prepare("
                INSERT INTO pendaftar (
                    nomor_pendaftaran, nama_lengkap, nisn, nik, tempat_lahir, tanggal_lahir,
                    jenis_kelamin, agama, whatsapp, email, alamat, asal_sekolah, tahun_lulus,
                    nama_ayah, nama_ibu, wa_orang_tua, pekerjaan_orang_tua, program_keahlian_id,
                    file_foto, file_kk, file_dokumen, status
                ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,'MENUNGGU')
            ");
            $stmt->execute([
                $nomor, $d['nama_lengkap'], $d['nisn'], $d['nik'], $d['tempat_lahir'], $d['tanggal_lahir'],
                $d['jenis_kelamin'], $d['agama'], $d['whatsapp'], $d['email'] ?? '', $d['alamat'], $d['asal_sekolah'], $d['tahun_lulus'] ?? '',
                $d['nama_ayah'], $d['nama_ibu'], $d['wa_orang_tua'] ?? '', $d['pekerjaan_orang_tua'] ?? '', $d['program_keahlian_id'],
                $foto, $kk, $dokumen
            ]);
        } catch (PDOException $e) {
            $errors[] = 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage();
        }
    }

    if (empty($errors)) {
        // Show success - don't redirect, show inline success
    } else {
        // Delete any uploaded files if validation failed
        if ($foto && file_exists(__DIR__ . '/' . $foto)) unlink(__DIR__ . '/' . $foto);
        if ($kk && file_exists(__DIR__ . '/' . $kk)) unlink(__DIR__ . '/' . $kk);
        if ($dokumen && file_exists(__DIR__ . '/' . $dokumen)) unlink(__DIR__ . '/' . $dokumen);
    }
}

$pageTitle = 'Pendaftaran Siswa Baru';
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="page-hero-shape" style="top:20%;left:10%;width:60px;height:60px;border-radius:50%;background:rgba(212,175,55,0.15);animation:floatGentle 6s ease-in-out infinite;"></div>
    <div class="page-hero-shape" style="bottom:25%;right:15%;width:40px;height:40px;border-radius:12px;border:2px solid rgba(255,255,255,0.1);animation:spinSlow 20s linear infinite;"></div>
    <div class="container">
        <div class="hero-badge"><span class="dot"></span> PPDB 2026/2027</div>
        <h1>Formulir <span class="gold-text">Pendaftaran</span> Siswa Baru</h1>
        <p>Lengkapi seluruh data dengan benar. Pastikan dokumen yang diupload sesuai dengan persyaratan yang ditentukan.</p>
    </div>
</section>

<div class="container" style="padding-top:40px;padding-bottom:80px;max-width:960px;">
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <div><?php echo implode('<br>', array_map('e', $errors)); ?></div></div>
    <?php endif; ?>

    <?php if ($nomor): ?>
        <!-- SUCCESS -->
        <div class="result-card reveal-scale">
            <div class="result-icon"><i class="fas fa-check-circle"></i></div>
            <h1 style="font-size:28px;color:var(--forest-900);margin-bottom:12px;">Data Pendaftaran Berhasil Dikirim!</h1>
            <p style="color:var(--text-gray);margin-bottom:8px;">Simpan nomor pendaftaran Anda untuk mengecek status pendaftaran.</p>
            <div class="nomor-card">
                <div class="label">Nomor Pendaftaran Anda</div>
                <div class="value"><?php echo e($nomor); ?></div>
            </div>
            <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;">
                <a href="<?php echo SITE_URL; ?>cek-status.php" class="btn btn-green">CEK STATUS PENDAFTARAN</a>
                <a href="<?php echo SITE_URL; ?>" class="btn btn-outline-green"><i class="fas fa-arrow-left"></i> KEMBALI KE BERANDA</a>
            </div>
        </div>
    <?php else: ?>
        <!-- FORM -->
        <form action="" method="POST" enctype="multipart/form-data" class="form-card reveal">
            <!-- Data Siswa -->
            <div class="form-section">
                <div class="form-section-header">
                    <div class="form-section-icon green"><i class="fas fa-user-graduate"></i></div>
                    <div><h2>Data Siswa</h2><p>Identitas calon peserta didik</p></div>
                </div>
                <div class="form-grid">
                    <div class="form-group full"><label class="form-label">Nama Lengkap <span class="req">*</span></label><input class="form-control" type="text" name="nama_lengkap" value="<?php echo e($_POST['nama_lengkap'] ?? ''); ?>" placeholder="Nama lengkap sesuai akta kelahiran"></div>
                    <div class="form-group"><label class="form-label">NISN <span class="req">*</span></label><input class="form-control" type="text" name="nisn" maxlength="16" value="<?php echo e($_POST['nisn'] ?? ''); ?>" placeholder="Nomor Induk Siswa Nasional"></div>
                    <div class="form-group"><label class="form-label">NIK <span class="req">*</span></label><input class="form-control" type="text" name="nik" maxlength="16" value="<?php echo e($_POST['nik'] ?? ''); ?>" placeholder="Nomor Induk Kependudukan"></div>
                    <div class="form-group"><label class="form-label">Tempat Lahir <span class="req">*</span></label><input class="form-control" type="text" name="tempat_lahir" value="<?php echo e($_POST['tempat_lahir'] ?? ''); ?>"></div>
                    <div class="form-group"><label class="form-label">Tanggal Lahir <span class="req">*</span></label><input class="form-control" type="date" name="tanggal_lahir" value="<?php echo e($_POST['tanggal_lahir'] ?? ''); ?>"></div>
                    <div class="form-group"><label class="form-label">Jenis Kelamin <span class="req">*</span></label>
                        <select class="form-control" name="jenis_kelamin">
                            <option value="">-- Pilih --</option>
                            <option value="Laki-laki" <?php echo ($_POST['jenis_kelamin'] ?? '') === 'Laki-laki' ? 'selected' : ''; ?>>Laki-laki</option>
                            <option value="Perempuan" <?php echo ($_POST['jenis_kelamin'] ?? '') === 'Perempuan' ? 'selected' : ''; ?>>Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group"><label class="form-label">Agama <span class="req">*</span></label>
                        <select class="form-control" name="agama"><option value="Islam">Islam</option></select>
                    </div>
                    <div class="form-group"><label class="form-label">Nomor WhatsApp <span class="req">*</span></label><input class="form-control" type="tel" name="whatsapp" value="<?php echo e($_POST['whatsapp'] ?? ''); ?>" placeholder="08xxxxxxxxxx"></div>
                    <div class="form-group"><label class="form-label">Email <span class="req">*</span></label><input class="form-control" type="email" name="email" value="<?php echo e($_POST['email'] ?? ''); ?>" placeholder="email@contoh.com"></div>
                    <div class="form-group full"><label class="form-label">Alamat Lengkap <span class="req">*</span></label><textarea class="form-control" name="alamat" rows="3"><?php echo e($_POST['alamat'] ?? ''); ?></textarea></div>
                    <div class="form-group"><label class="form-label">Asal Sekolah <span class="req">*</span></label><input class="form-control" type="text" name="asal_sekolah" value="<?php echo e($_POST['asal_sekolah'] ?? ''); ?>"></div>
                    <div class="form-group"><label class="form-label">Tahun Lulus</label><input class="form-control" type="number" name="tahun_lulus" min="2000" max="2030" value="<?php echo e($_POST['tahun_lulus'] ?? ''); ?>"></div>
                </div>
            </div>

            <!-- Data Orang Tua -->
            <div class="form-section">
                <div class="form-section-header">
                    <div class="form-section-icon gold"><i class="fas fa-user"></i></div>
                    <div><h2>Data Orang Tua</h2><p>Informasi orang tua/wali calon peserta didik</p></div>
                </div>
                <div class="form-grid">
                    <div class="form-group"><label class="form-label">Nama Ayah <span class="req">*</span></label><input class="form-control" type="text" name="nama_ayah" value="<?php echo e($_POST['nama_ayah'] ?? ''); ?>"></div>
                    <div class="form-group"><label class="form-label">Nama Ibu <span class="req">*</span></label><input class="form-control" type="text" name="nama_ibu" value="<?php echo e($_POST['nama_ibu'] ?? ''); ?>"></div>
                    <div class="form-group"><label class="form-label">Nomor WhatsApp Orang Tua</label><input class="form-control" type="tel" name="wa_orang_tua" value="<?php echo e($_POST['wa_orang_tua'] ?? ''); ?>"></div>
                    <div class="form-group"><label class="form-label">Pekerjaan Orang Tua</label><input class="form-control" type="text" name="pekerjaan_orang_tua" value="<?php echo e($_POST['pekerjaan_orang_tua'] ?? ''); ?>"></div>
                </div>
            </div>

            <!-- Program -->
            <div class="form-section">
                <div class="form-section-header">
                    <div class="form-section-icon teal"><i class="fas fa-school"></i></div>
                    <div><h2>Pilihan Program Keahlian</h2><p>Pilih salah satu program keahlian yang diminati</p></div>
                </div>
                <div class="form-group">
                    <label class="form-label">Program Keahlian <span class="req">*</span></label>
                    <select class="form-control" name="program_keahlian_id">
                        <option value="">-- Pilih Program Keahlian --</option>
                        <?php foreach ($programs as $p): ?>
                            <option value="<?php echo $p['id']; ?>" <?php echo ($_POST['program_keahlian_id'] ?? '') == $p['id'] ? 'selected' : ''; ?>><?php echo e($p['nama']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Upload -->
            <div class="form-section">
                <div class="form-section-header">
                    <div class="form-section-icon indigo"><i class="fas fa-file-upload"></i></div>
                    <div><h2>Upload Dokumen</h2><p>Format: JPG, PNG, WEBP, atau PDF. Maksimal 2MB per file.</p></div>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Foto <span class="req">*</span></label>
                        <label class="upload-box">
                            <i class="fas fa-upload"></i>
                            <div class="upload-text" data-default="Pilih file foto">Pilih file foto</div>
                            <input type="file" name="file_foto" accept=".jpg,.jpeg,.png,.webp">
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kartu Keluarga <span class="req">*</span></label>
                        <label class="upload-box">
                            <i class="fas fa-upload"></i>
                            <div class="upload-text" data-default="Pilih file KK">Pilih file KK</div>
                            <input type="file" name="file_kk" accept=".jpg,.jpeg,.png,.webp,.pdf">
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Dokumen Pendukung (Opsional)</label>
                        <label class="upload-box">
                            <i class="fas fa-upload"></i>
                            <div class="upload-text" data-default="Pilih file tambahan">Pilih file tambahan</div>
                            <input type="file" name="file_dokumen" accept=".jpg,.jpeg,.png,.webp,.pdf">
                        </label>
                    </div>
                </div>
            </div>

            <!-- Agreement & Submit -->
            <div class="checkbox-group" style="margin-bottom:24px;">
                <input type="checkbox" name="agree" id="agree" <?php echo !empty($_POST['agree']) ? 'checked' : ''; ?> required>
                <label for="agree">Saya menyatakan bahwa data yang saya masukkan adalah benar. Apabila di kemudian hari ditemukan ketidakbenaran data, saya bersedia menerima sanksi sesuai ketentuan yang berlaku.</label>
            </div>

            <button type="submit" class="btn btn-green btn-lg" style="width:100%;"><i class="fas fa-check-circle"></i> KIRIM PENDAFTARAN</button>
        </form>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
