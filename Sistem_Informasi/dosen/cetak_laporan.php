<?php
require_once 'auth_check.php';

$dosen_id = $_SESSION['user_id'];
$dosen_nama = $_SESSION['nama_lengkap'];
$dosen_nidn = $_SESSION['nidn'];

$sql = "SELECT m.*, 
               p.nama_prodi,
               sm.id AS skrining_id,
               sm.kode_sesi,
               sm.ipk_snapshot,
               sm.kategori,
               sm.probabilitas_tinggi,
               sm.status_konseling,
               sm.tanggal_skrining,
               (SELECT catatan_bimbingan FROM konseling_riwayat WHERE skrining_id = sm.id ORDER BY id DESC LIMIT 1) as catatan_terakhir
        FROM `mahasiswa` m
        JOIN `program_studi` p ON m.prodi_id = p.id
        LEFT JOIN `skrining_master` sm ON sm.id = (
            SELECT id FROM `skrining_master` 
            WHERE mahasiswa_id = m.id 
            ORDER BY created_at DESC 
            LIMIT 1
        )
        WHERE m.dosen_id = ?
        ORDER BY m.nim ASC";

$data = [];
if ($pdo) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$dosen_id]);
    $data = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Cetak Rekapitulasi 20 Mahasiswa Perwalian - <?= htmlspecialchars($dosen_nama) ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <style>
    body {
      font-family: 'Times New Roman', Times, serif;
      color: #000;
      background: #fff;
    }
    .kop-surat {
      border-bottom: 3px double #000;
      padding-bottom: 12px;
      margin-bottom: 18px;
    }
    table th, table td {
      font-size: 10pt;
      padding: 5px 6px !important;
    }
    @media print {
      .no-print {
        display: none !important;
      }
      body {
        margin: 0;
        padding: 0;
      }
    }
  </style>
</head>
<body class="p-4">

<!-- Action Buttons -->
<div class="mb-4 no-print text-end">
  <button onclick="window.print()" class="btn btn-primary btn-sm me-2">
    <i class="bi bi-printer"></i> Cetak / Simpan PDF
  </button>
  <button onclick="window.close()" class="btn btn-secondary btn-sm">
    Tutup Halaman
  </button>
</div>

<!-- KOP LAPORAN RESMI -->
<div class="kop-surat text-center">
  <h4 class="fw-bold mb-1 text-uppercase">PROGRAM STUDI SISTEM INFORMASI &bull; FAKULTAS ILMU KOMPUTER</h4>
  <h5 class="fw-bold mb-1 text-uppercase">SISTEM PENDUKUNG KEPUTUSAN SKRINING KESEHATAN MENTAL MAHASISWA</h5>
  <p class="mb-0 small">Laporan Rekapitulasi Pemantauan Beban Akademik & Kesehatan Mental 20 Mahasiswa Perwalian</p>
</div>

<div class="d-flex justify-content-between mb-3 small">
  <div>
    <strong>Dosen Pembimbing Akademik:</strong> <?= htmlspecialchars($dosen_nama) ?> (NIDN: <?= htmlspecialchars($dosen_nidn) ?>)<br>
    <strong>Tahun Akademik:</strong> 2025/2026 Ganjil &bull; <strong>Basis Model:</strong> Random Forest Classifier
  </div>
  <div class="text-end">
    <strong>Tanggal Cetak:</strong> <?= date('d F Y') ?><br>
    <strong>Total Bimbingan:</strong> <?= count($data) ?> Mahasiswa Perwalian
  </div>
</div>

<!-- TABEL REKAP 20 MAHASISWA -->
<table class="table table-bordered table-sm align-middle">
  <thead class="table-light text-center">
    <tr>
      <th style="width: 4%;">No</th>
      <th style="width: 12%;">NIM</th>
      <th style="width: 20%;">Nama Mahasiswa</th>
      <th style="width: 10%;">Gender/Usia</th>
      <th style="width: 12%;">Kode Sesi</th>
      <th style="width: 16%;">Hasil Skrining (SPK AI)</th>
      <th style="width: 12%;">Status Konseling</th>
      <th style="width: 14%;">Catatan Bimbingan</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($data)): ?>
      <?php foreach ($data as $i => $row): ?>
        <tr>
          <td class="text-center"><?= $i + 1 ?></td>
          <td class="text-center font-monospace"><?= htmlspecialchars($row['nim']) ?></td>
          <td><?= htmlspecialchars($row['nama_mahasiswa']) ?></td>
          <td class="text-center small"><?= htmlspecialchars($row['jenis_kelamin']) ?>, <?= $row['usia'] ?> th</td>
          <td class="text-center small font-monospace">
            <?= htmlspecialchars($row['kode_sesi'] ?: '-') ?>
          </td>
          <td>
            <?php if ($row['skrining_id']): ?>
              <strong><?= htmlspecialchars($row['kategori']) ?></strong><br>
              <small class="text-muted">Risiko: <?= $row['probabilitas_tinggi'] ?>% &bull; IPK: <?= number_format($row['ipk_snapshot'], 2) ?></small>
            <?php else: ?>
              <span class="text-muted fst-italic">Belum Skrining</span>
            <?php endif; ?>
          </td>
          <td class="text-center small">
            <?php if ($row['skrining_id']): ?>
              <?= htmlspecialchars($row['status_konseling']) ?>
            <?php else: ?>
              -
            <?php endif; ?>
          </td>
          <td class="small">
            <?= htmlspecialchars($row['catatan_terakhir'] ?: '-') ?>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr>
        <td colspan="8" class="text-center py-3">Tidak ada data mahasiswa bimbingan.</td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>

<!-- AREA TANDA TANGAN -->
<div class="row mt-5">
  <div class="col-7"></div>
  <div class="col-5 text-center">
    <p class="mb-1">Mengetahui,</p>
    <p class="mb-5">Dosen Pembimbing Akademik,</p>
    <br><br>
    <strong><u><?= htmlspecialchars($dosen_nama) ?></u></strong><br>
    <span>NIDN. <?= htmlspecialchars($dosen_nidn) ?></span>
  </div>
</div>

</body>
</html>
