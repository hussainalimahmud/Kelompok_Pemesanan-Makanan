<?php
require_once 'koneksi.php';

// Proteksi Halaman: Mahasiswa Wajib Login
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: login.php?tab=mahasiswa');
    exit;
}

$mhs_id = $_SESSION['user_id'];
$riwayat_skrining = [];
$notif_konseling = null;

if ($pdo) {
    // 1. Ambil data profil mahasiswa & dosen PA
    $stmtMhs = $pdo->prepare("SELECT m.*, p.nama_prodi, d.nama_dosen, d.nidn, d.email as email_dosen 
                              FROM `mahasiswa` m 
                              JOIN `dosen` d ON m.dosen_id = d.id 
                              JOIN `program_studi` p ON m.prodi_id = p.id
                              WHERE m.`id` = ?");
    $stmtMhs->execute([$mhs_id]);
    $profil = $stmtMhs->fetch();

    // 2. Ambil riwayat skrining_master mahasiswa ini
    $stmtRiwayat = $pdo->prepare("SELECT sm.*, d.nama_dosen,
                                         (SELECT catatan_bimbingan FROM konseling_riwayat WHERE skrining_id = sm.id ORDER BY id DESC LIMIT 1) as catatan_konseling_terakhir,
                                         (SELECT tanggal_konseling FROM konseling_riwayat WHERE skrining_id = sm.id ORDER BY id DESC LIMIT 1) as tanggal_konseling_terakhir,
                                         (SELECT tempat_metode FROM konseling_riwayat WHERE skrining_id = sm.id ORDER BY id DESC LIMIT 1) as tempat_konseling
                                  FROM `skrining_master` sm 
                                  JOIN `dosen` d ON sm.dosen_id = d.id
                                  WHERE sm.`mahasiswa_id` = ? 
                                  ORDER BY sm.`created_at` DESC");
    $stmtRiwayat->execute([$mhs_id]);
    $riwayat_skrining = $stmtRiwayat->fetchAll();

    // 3. Cek apakah ada notifikasi bimbingan konseling dari Dosen PA
    if (!empty($riwayat_skrining)) {
        $latest = $riwayat_skrining[0];
        if ($latest['status_konseling'] === 'Dijadwalkan Konseling') {
            $notif_konseling = [
                'tipe' => 'warning',
                'judul' => 'Jadwal Bimbingan Konseling Akademik',
                'pesan' => $latest['catatan_konseling_terakhir'] ?: 'Dosen Pembimbing Akademik Anda telah menjadwalkan sesi bimbingan konseling.',
                'jadwal' => $latest['tanggal_konseling_terakhir'] ?: 'Segera konfirmasi waktu dengan Dosen PA',
                'tempat' => $latest['tempat_konseling'] ?: 'Ruang Dosen PA',
                'dosen' => $profil['nama_dosen']
            ];
        } elseif ($latest['status_konseling'] === 'Selesai Konseling') {
            $notif_konseling = [
                'tipe' => 'success',
                'judul' => 'Sesi Bimbingan Selesai',
                'pesan' => 'Sesi konseling telah selesai dilaksanakan. Pertahankan ritme belajar yang sehat dan seimbang!',
                'jadwal' => $latest['tanggal_konseling_terakhir'] ?: '-',
                'tempat' => $latest['tempat_konseling'] ?: '-',
                'dosen' => $profil['nama_dosen']
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Mahasiswa - MindCare SPK</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<main class="container my-4">

  <!-- NOTIFIKASI JADWAL BIMBINGAN / KONSELING DARI DOSEN PA -->
  <?php if ($notif_konseling): ?>
    <div class="card glass-card border-0 mb-4 overflow-hidden" style="border-left: 6px solid <?= ($notif_konseling['tipe'] === 'warning' ? '#f59e0b' : '#10b981') ?> !important;">
      <div class="card-body p-4">
        <div class="d-flex align-items-start gap-3">
          <div class="p-3 rounded-4 d-inline-flex align-items-center justify-content-center" style="background: <?= ($notif_konseling['tipe'] === 'warning' ? 'rgba(245, 158, 11, 0.15)' : 'rgba(16, 185, 129, 0.15)') ?>; color: <?= ($notif_konseling['tipe'] === 'warning' ? '#d97706' : '#059669') ?>; font-size: 1.6rem;">
            <i class="bi <?= ($notif_konseling['tipe'] === 'warning' ? 'bi-bell-fill' : 'bi-check-circle-fill') ?>"></i>
          </div>
          <div class="flex-grow-1">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-1 mb-2">
              <h5 class="fw-bold mb-0 text-slate-800">
                <?= htmlspecialchars($notif_konseling['judul']) ?>
              </h5>
              <span class="badge <?= ($notif_konseling['tipe'] === 'warning' ? 'badge-glass-amber' : 'badge-glass-emerald') ?> rounded-pill px-3 py-1">
                Pemberitahuan Dosen PA
              </span>
            </div>
            <p class="text-secondary mb-3">
              <?= nl2br(htmlspecialchars($notif_konseling['pesan'])) ?>
            </p>
            <div class="row g-2 small text-muted pt-2 border-top">
              <div class="col-sm-6">
                <i class="bi bi-clock me-1 text-primary"></i> <strong>Waktu/Jadwal:</strong> <?= htmlspecialchars($notif_konseling['jadwal']) ?>
              </div>
              <div class="col-sm-6">
                <i class="bi bi-geo-alt me-1 text-primary"></i> <strong>Tempat:</strong> <?= htmlspecialchars($notif_konseling['tempat']) ?>
              </div>
              <div class="col-12 mt-1">
                <i class="bi bi-person-check me-1 text-primary"></i> <strong>Dosen Pembimbing Akademik:</strong> <?= htmlspecialchars($notif_konseling['dosen']) ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- HEADER GLASS BANNER -->
  <div class="glass-banner">
    <div class="row align-items-center">
      <div class="col-lg-8">
        <span class="badge badge-glass-indigo px-3 py-1 rounded-pill mb-2 fw-semibold">
          <i class="bi bi-person-circle me-1"></i> Portal Mahasiswa
        </span>
        <h2 class="fw-bold mb-1 text-slate-900">Halo, <?= htmlspecialchars($_SESSION['nama_lengkap']) ?>!</h2>
        <p class="text-muted mb-0">
          NIM: <strong class="text-dark font-monospace"><?= htmlspecialchars($_SESSION['nim']) ?></strong> &bull; 
          Program Studi: <strong class="text-dark"><?= htmlspecialchars($_SESSION['prodi']) ?></strong> &bull; 
          Angkatan: <strong class="text-dark"><?= htmlspecialchars($_SESSION['angkatan']) ?></strong>
        </p>
      </div>
      <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
        <a href="index.php" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm d-inline-flex align-items-center gap-2">
          <i class="bi bi-clipboard2-pulse"></i>
          <span>Mulai Skrining Baru</span>
        </a>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-4">
    
    <!-- Kartu Informasi Dosen Pembimbing Akademik -->
    <div class="col-md-5">
      <div class="card glass-card border-0 h-100">
        <div class="card-header py-3">
          <h6 class="mb-0 fw-bold"><i class="bi bi-person-badge text-primary me-2"></i>Dosen Pembimbing Akademik (Dosen PA)</h6>
        </div>
        <div class="card-body p-4">
          <div class="d-flex align-items-center gap-3 mb-3">
            <div class="avatar-badge fs-4" style="width: 52px; height: 52px; border-radius: 16px;">
              <i class="bi bi-person-workspace"></i>
            </div>
            <div>
              <h5 class="fw-bold mb-0 text-slate-900"><?= htmlspecialchars($_SESSION['nama_dosen_pa']) ?></h5>
              <small class="text-muted">NIDN: <?= htmlspecialchars($_SESSION['nidn_dosen_pa']) ?></small>
            </div>
          </div>
          <div class="p-3 rounded-3 small" style="background: rgba(241, 245, 249, 0.6); border: 1px solid rgba(226, 232, 240, 0.6);">
            <p class="mb-1 text-muted">Dosen PA bertindak sebagai mentor Anda dalam memantau perkembangan akademik serta kondisi kesehatan psikologis selama masa studi.</p>
            <div class="text-muted"><i class="bi bi-info-circle me-1 text-primary"></i> Data kuesioner Anda tersimpan secara rahasia dan aman.</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Kartu Status Skrining Terkini -->
    <div class="col-md-7">
      <div class="card glass-card border-0 h-100">
        <div class="card-header py-3">
          <h6 class="mb-0 fw-bold"><i class="bi bi-activity text-primary me-2"></i>Status Skrining Terkini (Sesi Terakhir)</h6>
        </div>
        <div class="card-body p-4">
          <?php if (!empty($riwayat_skrining)): 
            $latest = $riwayat_skrining[0];
            $is_high = ($latest['hasil_prediksi'] == 1);
          ?>
            <div class="d-flex align-items-center justify-content-between p-3 rounded-4 mb-3 <?= $is_high ? 'badge-glass-rose' : 'badge-glass-emerald' ?>" style="border-width: 1px;">
              <div>
                <span class="badge <?= $is_high ? 'bg-danger' : 'bg-success' ?> px-3 py-1 rounded-pill mb-1">
                  <?= htmlspecialchars($latest['kategori']) ?>
                </span>
                <h5 class="fw-bold mb-0 <?= $is_high ? 'text-danger' : 'text-success' ?>">
                  <?= $is_high ? 'Tingkat Stres / Beban Tinggi' : 'Kondisi Mental Stabil' ?>
                </h5>
                <small class="text-muted">Kode Sesi: <code><?= htmlspecialchars($latest['kode_sesi']) ?></code> &bull; <?= date('d M Y, H:i', strtotime($latest['tanggal_skrining'])) ?> WIB</small>
              </div>
              <div class="text-end">
                <span class="text-muted small d-block">Probabilitas</span>
                <h3 class="fw-bold mb-0 <?= $is_high ? 'text-danger' : 'text-success' ?>">
                  <?= $is_high ? $latest['probabilitas_tinggi'] : $latest['probabilitas_rendah'] ?>%
                </h3>
              </div>
            </div>
            <a href="hasil.php?id=<?= $latest['id'] ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
              <i class="bi bi-file-earmark-medical me-1"></i> Buka Lembar Hasil Lengkap
            </a>
          <?php else: ?>
            <div class="text-center py-4">
              <i class="bi bi-clipboard2-check display-6 text-muted mb-2 d-block"></i>
              <p class="text-muted mb-3">Anda belum pernah melakukan skrining kesehatan mental.</p>
              <a href="index.php" class="btn btn-primary rounded-pill btn-sm px-4">
                Mulai Skrining Sekarang
              </a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

  </div>

  <!-- TABEL RIWAYAT SKRINING MAHASISWA (Master Sesi) -->
  <div class="card glass-card border-0 mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
      <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Pemeriksaan Kuesioner (Master Transaksi)</h6>
      <span class="badge badge-glass-indigo px-3 py-1 rounded-pill">Total: <?= count($riwayat_skrining) ?> Sesi</span>
    </div>
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead>
          <tr>
            <th>Kode Sesi</th>
            <th>Tanggal Skrining</th>
            <th>Tahun Akademik</th>
            <th>IPK Snapshot</th>
            <th>Hasil SPK AI</th>
            <th>Probabilitas</th>
            <th>Status Konseling</th>
            <th class="text-end">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($riwayat_skrining)): ?>
            <?php foreach ($riwayat_skrining as $r): ?>
              <tr>
                <td><span class="badge bg-light text-dark font-monospace border"><?= htmlspecialchars($r['kode_sesi']) ?></span></td>
                <td><?= date('d/m/Y H:i', strtotime($r['tanggal_skrining'])) ?></td>
                <td><small class="text-muted"><?= htmlspecialchars($r['tahun_akademik']) ?></small></td>
                <td><span class="fw-semibold"><?= number_format($r['ipk_snapshot'], 2) ?></span></td>
                <td>
                  <?php if ($r['hasil_prediksi'] == 1): ?>
                    <span class="badge badge-glass-rose px-2 py-1">
                      <i class="bi bi-exclamation-octagon-fill me-1"></i> Risiko Tinggi
                    </span>
                  <?php else: ?>
                    <span class="badge badge-glass-emerald px-2 py-1">
                      <i class="bi bi-check-circle-fill me-1"></i> Risiko Rendah
                    </span>
                  <?php endif; ?>
                </td>
                <td><strong><?= $r['probabilitas_tinggi'] ?>%</strong> Stres</td>
                <td>
                  <?php if ($r['status_konseling'] === 'Dijadwalkan Konseling'): ?>
                    <span class="badge badge-glass-amber"><i class="bi bi-calendar-event me-1"></i> Dijadwalkan</span>
                  <?php elseif ($r['status_konseling'] === 'Selesai Konseling'): ?>
                    <span class="badge badge-glass-emerald"><i class="bi bi-check2-circle me-1"></i> Selesai</span>
                  <?php else: ?>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary border">Belum Ditindaklanjuti</span>
                  <?php endif; ?>
                </td>
                <td class="text-end">
                  <a href="hasil.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                    <i class="bi bi-eye me-1"></i> Detail
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" class="text-center py-4 text-muted">Belum ada riwayat pengisian kuesioner.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</main>

<?php include 'footer.php'; ?>

</body>
</html>
