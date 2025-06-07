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
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three/examples/js/loaders/GLTFLoader.js"></script>
    <link href="https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css" rel="stylesheet" />
    <script src="https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js"></script>

    <!-- <script>
        let map; // buat global agar bisa diakses dari dashboard.php

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                center: {
                    lat: -8.294,
                    lng: 114.306
                }, // Koordinat tengah Poliwangi
                zoom: 17,
                mapTypeId: 'satellite'
            });
        }
    </script> -->


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
                <a class="nav-link dropdown-toggle text-white" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw text-white"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <!-- <li><a class="dropdown-item" href="#!">Settings</a></li>
                    <li><a class="dropdown-item" href="#!">Activity Log</a></li> -->
                    <!-- <li>
                        <hr class="dropdown-divider" />
                    </li> -->
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
                        <a class="nav-link text-white" href="<?= base_url('laporan') ?>">
                            <div class="sb-nav-link-icon"><i class=" text-white fas fa-clipboard-list"></i></div>
                            Laporan
                        </a>
                        <a class="nav-link text-white" href="<?= base_url('riwayat') ?>">
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
    <script src="<?= base_url('sb-admin') ?>/js/scripts.js"></script>
</body>

</html>