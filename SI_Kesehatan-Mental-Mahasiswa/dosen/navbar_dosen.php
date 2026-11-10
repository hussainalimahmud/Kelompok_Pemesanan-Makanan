<?php
// dosen/navbar_dosen.php
$active_sub = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg navbar-glass sticky-top py-3">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="dashboard.php">
      <span class="brand-icon">
        <i class="bi bi-heart-pulse-fill"></i>
      </span>
      <span>MindCare <small class="text-muted fw-normal">| Portal Dosen PA</small></span>
    </a>
    <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navDosen" aria-controls="navDosen" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navDosen">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
        <li class="nav-item">
          <a class="nav-link <?= ($active_sub === 'dashboard.php') ? 'active' : '' ?>" href="dashboard.php">
            <i class="bi bi-speedometer2 me-1"></i> Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($active_sub === 'data_mahasiswa.php' || $active_sub === 'detail_mahasiswa.php') ? 'active' : '' ?>" href="data_mahasiswa.php">
            <i class="bi bi-people me-1"></i> 20 Mahasiswa Perwalian
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($active_sub === 'cetak_laporan.php') ? 'active' : '' ?>" href="cetak_laporan.php" target="_blank">
            <i class="bi bi-printer me-1"></i> Cetak Laporan (PDF)
          </a>
        </li>
      </ul>
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="avatar-badge"><i class="bi bi-person-fill"></i></span>
            <span><?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Dosen PA') ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-4 p-2">
            <li><h6 class="dropdown-header text-uppercase small text-muted">NIDN: <?= htmlspecialchars($_SESSION['nidn'] ?? '-') ?></h6></li>
            <li><a class="dropdown-item rounded-3" href="dashboard.php"><i class="bi bi-grid me-2 text-primary"></i>Dashboard</a></li>
            <li><a class="dropdown-item rounded-3" href="data_mahasiswa.php"><i class="bi bi-people me-2 text-primary"></i>Mahasiswa Bimbingan</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item rounded-3 text-danger" href="../logout.php"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
