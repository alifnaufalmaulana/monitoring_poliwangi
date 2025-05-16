<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>GIS | <?= $judul ?></title>
    <link href="<?= base_url('sb-admin') ?>/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three/examples/js/loaders/GLTFLoader.js"></script>
</head>

<body>
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-primary">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3 fs-4" href="<?= base_url('home') ?>">Sistem Monitoring</a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-sm text-white order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <!-- Navbar-->
        <ul class="navbar-nav ms-auto me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-white" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw text-white"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="#!">Settings</a></li>
                    <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li><a class="dropdown-item" href="#!">Login</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark bg-primary" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <!-- <hr class="border border-white border-2"> -->
                        <!-- <div class="sb-sidenav-menu-heading">Core</div> -->
                        <a class="nav-link text-white" href="<?= base_url('home') ?>">
                            <div class="sb-nav-link-icon"><i class=" text-white fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        <a class="nav-link text-white" href="<?= base_url('gedung') ?>">
                            <div class="sb-nav-link-icon"><i class=" text-white fas fa-building"></i></div>
                            Gedung
                        </a>
                        <a class="nav-link text-white" href="<?= base_url('perangkat') ?>">
                            <div class="sb-nav-link-icon"><i class=" text-white fas fa-microchip"></i></div>
                            Perangkat
                        </a>
                        <a class="nav-link text-white" href="<?= base_url('perangkat') ?>">
                            <div class="sb-nav-link-icon"><i class=" text-white fas fa-clipboard-list"></i></div>
                            Laporan
                        </a>
                        <a class="nav-link text-white" href="<?= base_url('perangkat') ?>">
                            <div class="sb-nav-link-icon"><i class=" text-white fas fa-clock-rotate-left"></i></div>
                            Riwayat
                        </a>
                    </div>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4 py-2">
                    <!-- Navbar Search -->
                    <!-- <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-2">
                        <div class="input-group">
                            <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                            <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
                        </div>
                    </form> -->
                    <?php if ($page) {
                        echo view($page);
                    } ?>
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
    <script src="<?= base_url('sb-admin') ?>/js/scripts.js"></script>
</body>

</html>