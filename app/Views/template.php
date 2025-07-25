<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>PEWS | <?= esc($judul ?? '') ?></title>
    <link href="<?= base_url('sb-admin') ?>/css/styles.css" rel="stylesheet" />
    <link href="<?= base_url('css/kustom.css') ?>" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three/examples/js/loaders/GLTFLoader.js"></script>
    <link href="https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css" rel="stylesheet" />
    <script src="https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

</head>

<body>
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-primary">

        <!-- Sidebar Toggle-->
        <button class="btn btn-lg text-white order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Navbar Brand-->
        <a class="navbar-brand ps-2 fs-4 d-flex align-items-center" href="<?= base_url('home') ?>">
            POLIWANGI EARLY WARNING SYSTEM <img src="<?= base_url('aset/img/logo-poliwangi.png') ?>" alt="Logo" style="height: 45px;" class="me-2 ps-2 ">
        </a>

        <!-- Navbar-->
        <ul class="navbar-nav ms-auto me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-white" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user fa-fw text-white"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <?php if (session()->has('username') && session()->has('role')) : ?>
                        <li>
                            <span class="dropdown-item disabled">
                                Halo, <?= esc(session('username')) ?> (<?= esc(session('role')) ?>)
                            </span>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= base_url('logout') ?>">
                                <i class="fa fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    <?php else : ?>
                        <li>
                            <a class="dropdown-item" href="<?= base_url('login') ?>">
                                <i class="fa fa-sign-in-alt"></i> Login
                            </a>
                        </li>
                    <?php endif; ?>
                    <li>
                        <button onclick="enableAudio()" class="dropdown-item">
                            <i class="fa fa-volume-up"></i> Aktifkan Suara
                        </button>
                    </li>
                </ul>
            </li>
        </ul>


    </nav>

    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark bg-primary" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <?php $role = session()->get('role'); ?>

                        <!-- Menu Dashboard -->
                        <a class="nav-link text-white" href="<?= base_url('/home') ?>">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt text-white"></i></div>
                            Dashboard
                        </a>

                        <!-- Menu untuk Role Pihak Admin -->
                        <?php if ($role === 'admin'): ?>
                            <a class="nav-link text-white" href="<?= base_url('gedung') ?>">
                                <div class="sb-nav-link-icon"><i class="fas fa-building text-white"></i></div>
                                Gedung
                            </a>
                            <a class="nav-link text-white" href="<?= base_url('perangkat') ?>">
                                <div class="sb-nav-link-icon"><i class="fas fa-microchip text-white"></i></div>
                                Perangkat
                            </a>
                            <a class="nav-link text-white" href="<?= base_url('riwayat-perangkat') ?>">
                                <div class="sb-nav-link-icon"><i class="fas fa-clock-rotate-left text-white"></i></div>
                                Riwayat
                            </a>

                            <!-- Menu untuk Role Pihak Keamanan -->
                        <?php elseif ($role === 'keamanan'): ?>
                            <a class="nav-link text-white" href="<?= base_url('laporan') ?>">
                                <div class="sb-nav-link-icon"><i class="fas fa-clipboard-list text-white"></i></div>
                                Laporan
                            </a>
                            <a class="nav-link text-white" href="<?= base_url('riwayat-perangkat') ?>">
                                <div class="sb-nav-link-icon"><i class="fas fa-clock-rotate-left text-white"></i></div>
                                Riwayat
                            </a>
                        <?php endif; ?>
                    </div>

                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4 py-2">
                    <?= $this->renderSection('content') ?>
                </div>
            </main>
            <footer class=" bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Sistem Monitoring Kebencanaan</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= base_url('sb-admin') ?>/js/scripts.js"></script>
</body>

</html>