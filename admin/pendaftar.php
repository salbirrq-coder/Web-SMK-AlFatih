<?php
$activeMenu = 'pendaftar';
$pageTitle = 'Data Pendaftar';
require_once __DIR__ . '/includes/admin_header.php';

$pdo = db();

// Filters
$status = $_GET['status'] ?? '';
$program = (int)($_GET['program'] ?? 0);
$q = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page - 1) * $perPage;

$where = [];
$params = [];
if ($status) {
    $where[] = "p.status = ?";
    $params[] = $status;
}
if ($program) {
    $where[] = "p.program_keahlian_id = ?";
    $params[] = $program;
}
if ($q !== '') {
    $where[] = "(p.nama_lengkap LIKE ? OR p.nomor_pendaftaran LIKE ?)";
    $params[] = "%$q%";
    $params[] = "%$q%";
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Count
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM pendaftar p $whereSql");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$totalPages = max(1, ceil($total / $perPage));

// Data
$sql = "SELECT p.*, pk.nama AS program
        FROM pendaftar p
        LEFT JOIN program_keahlian pk ON p.program_keahlian_id = pk.id
        $whereSql
        ORDER BY p.tanggal_daftar DESC
        LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$pendaftar = $stmt->fetchAll();

$programs = $pdo->query("SELECT * FROM program_keahlian ORDER BY nama")->fetchAll();

// Counts by status for tabs
$counts = ['all' => (int)$pdo->query("SELECT COUNT(*) FROM pendaftar")->fetchColumn()];
foreach (['MENUNGGU','DIVERIFIKASI','DITERIMA','TIDAK DITERIMA'] as $s) {
    $st = $pdo->prepare("SELECT COUNT(*) FROM pendaftar WHERE status = ?");
    $st->execute([$s]);
    $counts[$s] = (int)$st->fetchColumn();
}

$statusTabs = [
    ['key' => 'all', 'label' => 'Semua', 'sclass' => 'status-gray'],
    ['key' => 'MENUNGGU', 'label' => 'Menunggu', 'sclass' => 'status-yellow'],
    ['key' => 'DIVERIFIKASI', 'label' => 'Diverifikasi', 'sclass' => 'status-blue'],
    ['key' => 'DITERIMA', 'label' => 'Diterima', 'sclass' => 'status-green'],
    ['key' => 'TIDAK DITERIMA', 'label' => 'Ditolak', 'sclass' => 'status-red'],
];
?>

<div class="page-heading">
    <div>
        <h1>Data Pendaftar</h1>
        <div class="sub">Kelola, verifikasi, terima, atau tolak pendaftaran siswa.</div>
    </div>
</div>

<!-- Status tabs -->
<div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:18px;">
    <?php foreach ($statusTabs as $tab): ?>
        <?php
        $active = ($status === $tab['key']) || ($status === '' && $tab['key'] === 'all');
        $href = SITE_URL . 'admin/pendaftar.php?status=' . $tab['key'];
        ?>
        <a href="<?php echo $href; ?>" style="display:inline-flex;align-items:center;gap:8px;padding:8px 16px;border-radius:50px;font-size:13px;font-weight:600;border:1px solid var(--border);transition:all .2s ease;<?php echo $active ? 'background:var(--forest-600);color:#fff;border-color:var(--forest-600);' : 'color:var(--forest-700);background:#fff;'; ?>">
            <span class="status-badge <?php echo $tab['sclass']; ?>" style="font-size:11px;"><?php echo $tab['label']; ?></span>
            <span style="opacity:.8;"><?php echo $counts[$tab['key']]; ?></span>
        </a>
    <?php endforeach; ?>
</div>

<!-- Filters -->
<div class="table-card">
    <div class="table-header">
        <form method="GET" class="filter-bar" style="flex:1;">
            <?php if ($status): ?><input type="hidden" name="status" value="<?php echo e($status); ?>"><?php endif; ?>
            <input type="text" name="q" class="form-control" placeholder="Cari nama / nomor pendaftaran..." value="<?php echo e($q); ?>" style="max-width:300px;">
            <select name="program" class="form-control" style="max-width:240px;">
                <option value="">Semua Program</option>
                <?php foreach ($programs as $pr): ?>
                    <option value="<?php echo $pr['id']; ?>" <?php echo $program == $pr['id'] ? 'selected' : ''; ?>><?php echo e($pr['nama']); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-green btn-sm"><i class="fas fa-filter"></i> Filter</button>
            <a href="<?php echo SITE_URL; ?>admin/pendaftar.php" class="btn btn-outline-green btn-sm"><i class="fas fa-rotate-left"></i> Reset</a>
        </form>
    </div>

    <?php if (empty($pendaftar)): ?>
        <div class="empty-state"><i class="fas fa-users-slash"></i><h3>Tidak Ada Data</h3><p>Tidak ditemukan pendaftar dengan kriteria tersebut.</p></div>
    <?php else: ?>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama</th>
                        <th>No. Pendaftaran</th>
                        <th>Program</th>
                        <th>Tanggal Daftar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = $offset + 1; foreach ($pendaftar as $p): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td class="nama-cell">
                                <div><?php echo e($p['nama_lengkap']); ?></div>
                                <div style="font-size:12px;color:var(--text-gray);"><?php echo e($p['asal_sekolah']); ?></div>
                            </td>
                            <td style="font-weight:600;color:var(--forest-600);"><?php echo e($p['nomor_pendaftaran']); ?></td>
                            <td><?php echo e($p['program'] ?? '-'); ?></td>
                            <td><?php echo format_date($p['tanggal_daftar']); ?></td>
                            <td><span class="status-badge <?php echo status_class($p['status']); ?>"><?php echo status_label($p['status']); ?></span></td>
                            <td>
                                <div style="display:flex;gap:6px;">
                                    <button class="action-btn action-green" onclick="openDetail(<?php echo $p['id']; ?>)"><i class="fas fa-eye"></i></button>
                                    <?php if ($p['status'] === 'MENUNGGU'): ?>
                                        <button class="action-btn action-green" onclick="openAccept(<?php echo $p['id']; ?>)" title="Terima"><i class="fas fa-check"></i></button>
                                        <button class="action-btn action-red" onclick="openReject(<?php echo $p['id']; ?>)" title="Tolak"><i class="fas fa-xmark"></i></button>
                                    <?php elseif ($p['status'] === 'DIVERIFIKASI'): ?>
                                        <button class="action-btn action-green" onclick="openAccept(<?php echo $p['id']; ?>)" title="Terima"><i class="fas fa-check"></i></button>
                                        <button class="action-btn action-red" onclick="openReject(<?php echo $p['id']; ?>)" title="Tolak"><i class="fas fa-xmark"></i></button>
                                    <?php elseif (in_array($p['status'], ['DITERIMA','TIDAK DITERIMA'])): ?>
                                        <button class="action-btn action-gold" onclick="openRevert(<?php echo $p['id']; ?>)" title="Kembalikan ke Menunggu"><i class="fas fa-rotate-left"></i></button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <div class="page-info">Menampilkan <?php echo $offset + 1; ?> - <?php echo min($offset + $perPage, $total); ?> dari <?php echo $total; ?> pendaftar</div>
            <div class="page-btns">
                <a class="page-btn" href="<?php echo SITE_URL; ?>admin/pendaftar.php?page=<?php echo max(1, $page-1); ?>&status=<?php echo $status; ?>&program=<?php echo $program; ?>&q=<?php echo urlencode($q); ?>" <?php echo $page <= 1 ? 'disabled' : ''; ?>><i class="fas fa-chevron-left"></i></a>
                <span style="padding:0 10px;font-size:13px;color:var(--text-gray);"><?php echo $page; ?> / <?php echo $totalPages; ?></span>
                <a class="page-btn" href="<?php echo SITE_URL; ?>admin/pendaftar.php?page=<?php echo min($totalPages, $page+1); ?>&status=<?php echo $status; ?>&program=<?php echo $program; ?>&q=<?php echo urlencode($q); ?>" <?php echo $page >= $totalPages ? 'disabled' : ''; ?>><i class="fas fa-chevron-right"></i></a>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- ===== Detail Modal ===== -->
<div class="modal-overlay" id="detailModal">
    <div class="modal">
        <div class="modal-header">
            <div>
                <h3 style="font-size:18px;color:var(--forest-900);">Detail Pendaftar</h3>
                <span id="dStatusWrap" style="margin-top:6px;display:inline-block;"></span>
            </div>
            <button class="modal-close" onclick="closeModal('detailModal')">&times;</button>
        </div>
        <div class="modal-body" id="detailBody">
            <div style="display:grid;grid-template-columns:120px 1fr;gap:20px;align-items:start;">
                <div id="dFoto" style="width:120px;height:120px;border-radius:16px;overflow:hidden;background:var(--forest-50);"></div>
                <div>
                    <div style="font-size:20px;font-weight:800;color:var(--forest-900);" id="dNama"></div>
                    <div style="color:var(--forest-600);font-weight:600;font-size:13px;margin:4px 0;" id="dNomor"></div>
                    <div style="font-size:13px;color:var(--text-gray);" id="dProgram"></div>
                </div>
            </div>

            <div style="margin-top:24px;">
                <div style="font-weight:700;color:var(--forest-700);font-size:14px;margin-bottom:10px;">Data Pribadi</div>
                <div class="detail-grid">
                    <div class="detail-item"><div class="d-label">NISN</div><div class="d-value" id="dNisn"></div></div>
                    <div class="detail-item"><div class="d-label">NIK</div><div class="d-value" id="dNik"></div></div>
                    <div class="detail-item"><div class="d-label">Tempat, Tgl Lahir</div><div class="d-value" id="dLahir"></div></div>
                    <div class="detail-item"><div class="d-label">Jenis Kelamin</div><div class="d-value" id="dJk"></div></div>
                    <div class="detail-item"><div class="d-label">Agama</div><div class="d-value" id="dAgama"></div></div>
                    <div class="detail-item"><div class="d-label">Pekerjaan Orang Tua</div><div class="d-value" id="dPekerjaan"></div></div>
                    <div class="detail-item" style="grid-column:1/-1;"><div class="d-label">Alamat</div><div class="d-value" id="dAlamat"></div></div>
                    <div class="detail-item"><div class="d-label">Asal Sekolah</div><div class="d-value" id="dAsal"></div></div>
                    <div class="detail-item"><div class="d-label">Tahun Lulus</div><div class="d-value" id="dTahun"></div></div>
                    <div class="detail-item"><div class="d-label">WhatsApp</div><div class="d-value" id="dWa"></div></div>
                    <div class="detail-item"><div class="d-label">Email</div><div class="d-value" id="dEmail"></div></div>
                </div>
            </div>

            <div style="margin-top:24px;">
                <div style="font-weight:700;color:var(--forest-700);font-size:14px;margin-bottom:10px;">Orang Tua / Wali</div>
                <div class="detail-grid">
                    <div class="detail-item"><div class="d-label">Nama Ayah</div><div class="d-value" id="dAyah"></div></div>
                    <div class="detail-item"><div class="d-label">Nama Ibu</div><div class="d-value" id="dIbu"></div></div>
                    <div class="detail-item"><div class="d-label">WhatsApp Ortu</div><div class="d-value" id="dWaOrtu"></div></div>
                </div>
            </div>

            <div style="margin-top:24px;">
                <div style="font-weight:700;color:var(--forest-700);font-size:14px;margin-bottom:10px;">Berkas</div>
                <div style="display:flex;gap:10px;flex-wrap:wrap;" id="dBerkas"></div>
                <div id="dCatatan" style="display:none;margin-top:16px;padding:16px;background:var(--forest-50);border-radius:12px;font-size:14px;"></div>
            </div>

            <div style="margin-top:24px;display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap;" id="dActions"></div>
        </div>
    </div>
</div>

<!-- ===== Accept Modal ===== -->
<div class="modal-overlay" id="acceptModal">
    <div class="modal">
        <div class="modal-header">
            <h3 style="font-size:18px;color:var(--forest-900);">Terima Pendaftaran</h3>
            <button class="modal-close" onclick="closeModal('acceptModal')">&times;</button>
        </div>
        <div class="modal-body">
            <p style="color:var(--text-gray);font-size:14px;margin-bottom:6px;">Anda akan menerima pendaftaran:</p>
            <p style="font-weight:700;color:var(--forest-900);font-size:16px;margin-bottom:16px;" id="acceptName"></p>
            <form method="POST" action="<?php echo SITE_URL; ?>admin/pendaftar-action.php">
                <input type="hidden" name="id" id="acceptId">
                <input type="hidden" name="action" value="terima">
                <?php echo sec_csrf_field(); ?>
                <label class="form-label">Ucapan Selamat (opsional)</label>
                <textarea class="form-control" name="catatan" rows="3" placeholder="Pesan selamat yang tampil di hasil cek status..."></textarea>
                <div style="display:flex;gap:10px;margin-top:18px;justify-content:flex-end;">
                    <button type="button" class="btn btn-outline-green" onclick="closeModal('acceptModal')">Batal</button>
                    <button type="submit" class="btn btn-green"><i class="fas fa-check"></i> Ya, Terima</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== Reject Modal ===== -->
<div class="modal-overlay" id="rejectModal">
    <div class="modal">
        <div class="modal-header">
            <h3 style="font-size:18px;color:#dc2626;">Tolak Pendaftaran</h3>
            <button class="modal-close" onclick="closeModal('rejectModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="alert alert-error" style="margin-bottom:14px;"><i class="fas fa-exclamation-triangle"></i> <div>Anda akan menolak pendaftaran siswa ini. Alasan penolakan wajib diisi agar siswa tahu alasannya.</div></div>
            <p style="font-weight:700;color:var(--forest-900);font-size:15px;margin-bottom:14px;" id="rejectName"></p>
            <form method="POST" action="<?php echo SITE_URL; ?>admin/pendaftar-action.php">
                <input type="hidden" name="id" id="rejectId">
                <input type="hidden" name="action" value="tolak">
                <?php echo sec_csrf_field(); ?>
                <label class="form-label">Alasan Penolakan <span class="req">*</span></label>
                <textarea class="form-control" name="catatan" rows="4" id="rejectCatatan" placeholder="Tuliskan alasan mengapa siswa ditolak, misalnya: berkas tidak lengkap, data tidak sesuai, kuota penuh, dll."></textarea>
                <div style="display:flex;gap:10px;margin-top:18px;justify-content:flex-end;">
                    <button type="button" class="btn btn-outline-green" onclick="closeModal('rejectModal')">Batal</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-xmark"></i> Ya, Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== Revert Modal ===== -->
<div class="modal-overlay" id="revertModal">
    <div class="modal">
        <div class="modal-header">
            <h3 style="font-size:18px;color:var(--forest-900);">Kembalikan ke Menunggu</h3>
            <button class="modal-close" onclick="closeModal('revertModal')">&times;</button>
        </div>
        <div class="modal-body">
            <p style="color:var(--text-gray);font-size:14px;margin-bottom:16px;">Kembalikan status pendaftaran ini menjadi <strong>Menunggu Verifikasi</strong>?</p>
            <form method="POST" action="<?php echo SITE_URL; ?>admin/pendaftar-action.php">
                <input type="hidden" name="id" id="revertId">
                <input type="hidden" name="action" value="kembalikan">
                <?php echo sec_csrf_field(); ?>
                <div style="display:flex;gap:10px;justify-content:flex-end;">
                    <button type="button" class="btn btn-outline-green" onclick="closeModal('revertModal')">Batal</button>
                    <button type="submit" class="btn btn-green"><i class="fas fa-rotate-left"></i> Ya, Kembalikan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Store pendaftar data for detail
var pendaftarData = <?php echo json_encode($pendaftar, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

function esc(s) {
    if (s === null || s === undefined) return '-';
    var d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
}

function statusBadge(status) {
    var map = {
        'MENUNGGU': ['status-yellow', 'MENUNGGU VERIFIKASI'],
        'DIVERIFIKASI': ['status-blue', 'SEDANG DIVERIFIKASI'],
        'DITERIMA': ['status-green', 'DITERIMA'],
        'TIDAK DITERIMA': ['status-red', 'TIDAK DITERIMA']
    };
    var m = map[status] || ['status-gray', status];
    return '<span class="status-badge ' + m[0] + '">' + m[1] + '</span>';
}

function openModal(id) { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }

function openDetail(id) {
    var p = pendaftarData.find(function(x){ return x.id == id; });
    if (!p) return;
    document.getElementById('dStatusWrap').innerHTML = statusBadge(p.status);

    document.getElementById('dNama').textContent = p.nama_lengkap;
    document.getElementById('dNomor').textContent = p.nomor_pendaftaran;
    document.getElementById('dProgram').textContent = 'Program: ' + (p.program || '-');

    // Foto
    var fotoEl = document.getElementById('dFoto');
    if (p.file_foto) {
        fotoEl.innerHTML = '<img src="' + esc('<?php echo SITE_URL; ?>' + '') + p.file_foto + '" style="width:100%;height:100%;object-fit:cover;" alt="Foto">';
    } else {
        fotoEl.innerHTML = '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:36px;color:var(--forest-300);"><i class="fas fa-user"></i></div>';
    }

    document.getElementById('dNisn').textContent = p.nisn;
    document.getElementById('dNik').textContent = p.nik;
    document.getElementById('dLahir').textContent = (p.tempat_lahir || '') + ', ' + p.tanggal_lahir;
    document.getElementById('dJk').textContent = p.jenis_kelamin;
    document.getElementById('dAgama').textContent = p.agama;
    document.getElementById('dPekerjaan').textContent = p.pekerjaan_orang_tua || '-';
    document.getElementById('dAlamat').textContent = p.alamat;
    document.getElementById('dAsal').textContent = p.asal_sekolah;
    document.getElementById('dTahun').textContent = p.tahun_lulus || '-';
    document.getElementById('dWa').textContent = p.whatsapp;
    document.getElementById('dEmail').textContent = p.email || '-';

    document.getElementById('dAyah').textContent = p.nama_ayah;
    document.getElementById('dIbu').textContent = p.nama_ibu;
    document.getElementById('dWaOrtu').textContent = p.wa_orang_tua || '-';

    // Berkas
    var berkasHtml = '';
    if (p.file_kk) berkasHtml += berkasLink('Kartu Keluarga', p.file_kk);
    if (p.file_dokumen) berkasHtml += berkasLink('Dokumen', p.file_dokumen);
    if (!berkasHtml) berkasHtml = '<span style="color:var(--text-gray);font-size:13px;">Tidak ada berkas tambahan</span>';
    document.getElementById('dBerkas').innerHTML = berkasHtml;

    // Catatan
    var catatan = document.getElementById('dCatatan');
    if (p.catatan) {
        catatan.style.display = 'block';
        catatan.innerHTML = '<strong style="color:var(--forest-700);">Catatan / Alasan:</strong><br>' + esc(p.catatan);
    } else {
        catatan.style.display = 'none';
    }

    // Actions
    var actions = '';
    if (p.status === 'MENUNGGU' || p.status === 'DIVERIFIKASI') {
        actions += '<button class="btn btn-green btn-sm" onclick="closeModal(\'detailModal\');setTimeout(function(){openAccept(' + p.id + ')},100);"><i class="fas fa-check"></i> Terima</button>';
        actions += '<button class="btn btn-danger btn-sm" onclick="closeModal(\'detailModal\');setTimeout(function(){openReject(' + p.id + ')},100);"><i class="fas fa-xmark"></i> Tolak</button>';
    } else if (p.status === 'DITERIMA' || p.status === 'TIDAK DITERIMA') {
        actions += '<button class="btn btn-outline-green btn-sm" onclick="closeModal(\'detailModal\');setTimeout(function(){openRevert(' + p.id + ')},100);"><i class="fas fa-rotate-left"></i> Kembalikan</button>';
    }
    document.getElementById('dActions').innerHTML = actions;

    openModal('detailModal');
}

function berkasLink(label, file) {
    return '<a href="<?php echo SITE_URL; ?>' + file + '" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;gap:8px;padding:8px 14px;border-radius:10px;background:var(--forest-50);color:var(--forest-700);font-size:13px;font-weight:600;"><i class="fas fa-file"></i> ' + label + '</a>';
}

function openAccept(id) {
    var p = pendaftarData.find(function(x){ return x.id == id; });
    if (!p) return;
    document.getElementById('acceptId').value = p.id;
    document.getElementById('acceptName').textContent = p.nama_lengkap + ' (' + p.nomor_pendaftaran + ')';
    openModal('acceptModal');
}

function openReject(id) {
    var p = pendaftarData.find(function(x){ return x.id == id; });
    if (!p) return;
    document.getElementById('rejectId').value = p.id;
    document.getElementById('rejectName').textContent = p.nama_lengkap + ' (' + p.nomor_pendaftaran + ')';
    document.getElementById('rejectCatatan').value = '';
    openModal('rejectModal');
}

function openRevert(id) {
    document.getElementById('revertId').value = id;
    openModal('revertModal');
}
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
