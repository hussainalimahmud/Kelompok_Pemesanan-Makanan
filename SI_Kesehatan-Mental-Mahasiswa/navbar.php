<?php
// navbar.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
$is_mhs = isset($_SESSION['role']) && $_SESSION['role'] === 'mahasiswa';
$is_dsn = isset($_SESSION['role']) && $_SESSION['role'] === 'dosen';
?>
<nav class="navbar navbar-expand-lg navbar-glass sticky-top py-3">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
      <span class="brand-icon">
        <i class="bi bi-heart-pulse-fill"></i>
      </span>
      <span>MindCare <small class="text-muted fw-normal">SPK</small></span>
    </a>
    <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
        <?php if ($is_mhs): ?>
          <li class="nav-item">
            <a class="nav-link <?= ($current_page === 'dashboard_mahasiswa.php') ? 'active' : '' ?>" href="dashboard_mahasiswa.php">
              <i class="bi bi-grid me-1"></i> Dashboard Saya
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= ($current_page === 'index.php') ? 'active' : '' ?>" href="index.php">
              <i class="bi bi-clipboard2-pulse me-1"></i> Form Skrining
            </a>
          </li>
        <?php elseif ($is_dsn): ?>
          <li class="nav-item">
            <a class="nav-link <?= (strpos($_SERVER['PHP_SELF'], 'dosen/') !== false) ? 'active' : '' ?>" href="dosen/dashboard.php">
              <i class="bi bi-speedometer2 me-1"></i> Dashboard Monitoring
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="dosen/data_mahasiswa.php">
              <i class="bi bi-people me-1"></i> 20 Mahasiswa Perwalian
            </a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link <?= ($current_page === 'index.php') ? 'active' : '' ?>" href="index.php">
              <i class="bi bi-info-circle me-1"></i> Tentang Skrining
            </a>
          </li>
        <?php endif; ?>
      </ul>

      <ul class="navbar-nav ms-auto align-items-lg-center gap-2">
        <?php if ($is_mhs): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <span class="avatar-badge"><i class="bi bi-mortarboard-fill"></i></span>
              <span><?= htmlspecialchars($_SESSION['nama_lengkap']) ?> <small class="text-muted">(<?= htmlspecialchars($_SESSION['nim']) ?>)</small></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-4 p-2">
              <li><h6 class="dropdown-header text-uppercase small text-muted">Akun Mahasiswa</h6></li>
              <li><a class="dropdown-item rounded-3" href="dashboard_mahasiswa.php"><i class="bi bi-person-circle me-2 text-primary"></i>Dashboard Saya</a></li>
              <li><a class="dropdown-item rounded-3" href="index.php"><i class="bi bi-clipboard2-pulse me-2 text-primary"></i>Isi Kuesioner Baru</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item rounded-3 text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a></li>
            </ul>
          </li>
        <?php elseif ($is_dsn): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <span class="avatar-badge"><i class="bi bi-person-fill"></i></span>
              <span><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-4 p-2">
              <li><h6 class="dropdown-header text-uppercase small text-muted">NIDN: <?= htmlspecialchars($_SESSION['nidn']) ?></h6></li>
              <li><a class="dropdown-item rounded-3" href="dosen/dashboard.php"><i class="bi bi-grid me-2 text-primary"></i>Panel Monitoring</a></li>
              <li><a class="dropdown-item rounded-3" href="dosen/data_mahasiswa.php"><i class="bi bi-people me-2 text-primary"></i>Mahasiswa Bimbingan</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item rounded-3 text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="btn btn-glass btn-sm px-4 rounded-pill d-inline-flex align-items-center gap-2" href="login.php">
              <i class="bi bi-box-arrow-in-right text-primary"></i>
              <span>Masuk / Login</span>
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
