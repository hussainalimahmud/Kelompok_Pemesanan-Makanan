<?php
require_once 'auth_check.php';

$dosen_id = $_SESSION['user_id'];
$dosen_nama = $_SESSION['nama_lengkap'];

$filter_status = $_GET['status'] ?? '';
$search = trim($_GET['search'] ?? '');
$pesan_sukses = null;

if (isset($_GET['pesan']) && $_GET['pesan'] === 'diupdate') {
    $pesan_sukses = 'Status konseling dan log riwayat bimbingan berhasil diperbarui.';
}

// Ambil 20 mahasiswa perwalian dosen ini beserta hasil skrining_master terbarunya
$sql = "SELECT m.*, 
               p.nama_prodi,
               sm.id AS skrining_id,
               sm.kode_sesi,
               sm.hasil_prediksi,
               sm.kategori,
               sm.probabilitas_rendah,
               sm.probabilitas_tinggi,
               sm.status_konseling,
               sm.tanggal_skrining
        FROM `mahasiswa` m
        JOIN `program_studi` p ON m.prodi_id = p.id
        LEFT JOIN `skrining_master` sm ON sm.id = (
            SELECT id FROM `skrining_master` 
            WHERE mahasiswa_id = m.id 
            ORDER BY created_at DESC 
            LIMIT 1
        )
        WHERE m.dosen_id = ?";

$params = [$dosen_id];

if ($search !== '') {
    $sql .= " AND (m.nim LIKE ? OR m.nama_mahasiswa LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

if ($filter_status === 'risiko_tinggi') {
    $sql .= " AND sm.hasil_prediksi = 1";
} elseif ($filter_status === 'risiko_rendah') {
    $sql .= " AND sm.hasil_prediksi = 0";
} elseif ($filter_status === 'belum_skrining') {
    $sql .= " AND sm.id IS NULL";
} elseif ($filter_status === 'sudah_skrining') {
    $sql .= " AND sm.id IS NOT NULL";
} elseif ($filter_status === 'jadwal_konseling') {
    $sql .= " AND sm.status_konseling = 'Dijadwalkan Konseling'";
}

$sql .= " ORDER BY m.nim ASC";

$daftar_mhs = [];
if ($pdo) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $daftar_mhs = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data 20 Mahasiswa Perwalian - MindCare SPK</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include 'navbar_dosen.php'; ?>

<main class="container my-4">
  
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
      <h3 class="fw-bold mb-1 text-slate-900"><i class="bi bi-people text-primary me-2"></i>Daftar 20 Mahasiswa Perwalian</h3>
      <p class="text-muted mb-0">Dosen Pembimbing Akademik: <strong><?= htmlspecialchars($dosen_nama) ?></strong> (NIDN: <?= htmlspecialchars($_SESSION['nidn']) ?>)</p>
    </div>
    <div class="d-flex gap-2">
      <a href="cetak_laporan.php" target="_blank" class="btn btn-primary rounded-pill px-4 shadow-sm">
        <i class="bi bi-printer me-1"></i> Cetak Lembar Perwalian (PDF)
      </a>
    </div>
  </div>

  <?php if ($pesan_sukses): ?>
    <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($pesan_sukses) ?>
    </div>
  <?php endif; ?>

  <!-- Card Filter & Pencarian Glass -->
  <div class="card glass-card border-0 mb-4">
    <div class="card-body p-3">
      <form action="data_mahasiswa.php" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-search text-muted"></i></span>
            <input type="text" class="form-control" name="search" placeholder="Cari Nama / NIM mahasiswa..." value="<?= htmlspecialchars($search) ?>">
          </div>
        </div>
        <div class="col-md-5">
          <select name="status" class="form-select">
            <option value="">-- Semua Status Skrining & Konseling --</option>
            <option value="risiko_tinggi" <?= $filter_status === 'risiko_tinggi' ? 'selected' : '' ?>>⚠️ Risiko Tinggi (Stres Berat)</option>
            <option value="risiko_rendah" <?= $filter_status === 'risiko_rendah' ? 'selected' : '' ?>>✅ Risiko Rendah (Stabil)</option>
            <option value="jadwal_konseling" <?= $filter_status === 'jadwal_konseling' ? 'selected' : '' ?>>📅 Dijadwalkan Konseling</option>
            <option value="belum_skrining" <?= $filter_status === 'belum_skrining' ? 'selected' : '' ?>>⏳ Belum Skrining</option>
            <option value="sudah_skrining" <?= $filter_status === 'sudah_skrining' ? 'selected' : '' ?>>📝 Sudah Skrining</option>
          </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
          <button type="submit" class="btn btn-primary w-100 rounded-pill">
            <i class="bi bi-funnel-fill me-1"></i> Filter
          </button>
          <?php if ($filter_status !== '' || $search !== ''): ?>
            <a href="data_mahasiswa.php" class="btn btn-glass rounded-pill" title="Reset Filter">
              <i class="bi bi-x-circle"></i>
            </a>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </div>

  <!-- Tabel 20 Mahasiswa Glass -->
  <div class="card glass-card border-0">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
      <span class="fw-bold"><i class="bi bi-table text-primary me-2"></i>Daftar Perwalian: <?= count($daftar_mhs) ?> Mahasiswa</span>
      <small class="text-muted"><i class="bi bi-shield-check me-1"></i> 1 Dosen PA = 20 Mahasiswa</small>
    </div>
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead>
          <tr>
            <th>No</th>
            <th>NIM</th>
            <th>Nama Mahasiswa</th>
            <th>Gender / Usia</th>
            <th>Status Sesi Terkini</th>
            <th>Probabilitas</th>
            <th>Status Konseling PA</th>
            <th class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($daftar_mhs)): ?>
            <?php foreach ($daftar_mhs as $i => $row): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td><span class="badge bg-light text-dark border font-monospace"><?= htmlspecialchars($row['nim']) ?></span></td>
                <td>
                  <strong><?= htmlspecialchars($row['nama_mahasiswa']) ?></strong>
                  <div class="small text-muted"><?= htmlspecialchars($row['nama_prodi']) ?> (<?= htmlspecialchars($row['angkatan']) ?>)</div>
                </td>
                <td class="small text-muted"><?= htmlspecialchars($row['jenis_kelamin']) ?>, <?= $row['usia'] ?> th</td>
                <td>
                  <?php if ($row['skrining_id']): ?>
                    <?php if ($row['hasil_prediksi'] == 1): ?>
                      <span class="badge badge-glass-rose px-2 py-1">
                        <i class="bi bi-exclamation-octagon-fill me-1"></i> Risiko Tinggi
                      </span>
                    <?php else: ?>
                      <span class="badge badge-glass-emerald px-2 py-1">
                        <i class="bi bi-check-circle-fill me-1"></i> Risiko Rendah
                      </span>
                    <?php endif; ?>
                    <div class="small text-muted mt-1"><?= date('d/m/Y', strtotime($row['tanggal_skrining'])) ?></div>
                  <?php else: ?>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary border">Belum Mengisi</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($row['skrining_id']): ?>
                    <strong><?= $row['probabilitas_tinggi'] ?>%</strong> Stres
                  <?php else: ?>
                    <span class="text-muted">-</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($row['skrining_id']): ?>
                    <?php if ($row['status_konseling'] === 'Dijadwalkan Konseling'): ?>
                      <span class="badge badge-glass-amber"><i class="bi bi-calendar-check me-1"></i> Dijadwalkan</span>
                    <?php elseif ($row['status_konseling'] === 'Selesai Konseling'): ?>
                      <span class="badge badge-glass-emerald"><i class="bi bi-check2-circle me-1"></i> Selesai</span>
                    <?php else: ?>
                      <span class="badge bg-secondary bg-opacity-10 text-secondary border">Belum Ditindaklanjuti</span>
                    <?php endif; ?>
                  <?php else: ?>
                    <span class="text-muted small">-</span>
                  <?php endif; ?>
                </td>
                <td class="text-center">
                  <?php if ($row['skrining_id']): ?>
                    <a href="detail_mahasiswa.php?id=<?= $row['skrining_id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                      <i class="bi bi-pencil-square me-1"></i> Evaluasi & Konseling
                    </a>
                  <?php else: ?>
                    <button class="btn btn-sm btn-glass rounded-pill text-muted" disabled title="Mahasiswa belum mengisi kuesioner">
                      <i class="bi bi-clock me-1"></i> Menunggu
                    </button>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" class="text-center py-5 text-muted">
                Tidak ada data mahasiswa bimbingan yang sesuai kriteria filter.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</main>

<footer class="footer-glass mt-auto py-3">
  <div class="container text-center text-muted small">
    MindCare SPK Dosen &copy; <?= date('Y') ?> &bull; Dosen PA: <?= htmlspecialchars($dosen_nama) ?>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
