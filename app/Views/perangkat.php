<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-2">
            <h1 class="mt-4">Daftar Perangkat</h1>
            <!-- <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                <li class="breadcrumb-item active">Tables</li>
            </ol> -->
            <!-- <div class="card mb-4">
                <div class="card-body">
                    DataTables is a third party plugin that is used to generate the demo table below. For more information about DataTables, please visit the
                    <a target="_blank" href="https://datatables.net/">official DataTables documentation</a>
                    .
                </div>
            </div> -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-microchip me-2"></i> Daftar Perangkat
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatablesSimple" class="table table-striped table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Nama Perangkat</th>
                                    <th scope="col">Posisi</th>
                                    <th scope="col">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($perangkat as $p): ?>
                                    <tr>
                                        <td><?= esc($p['id_perangkat']) ?></td>
                                        <td><?= esc($p['nama_perangkat']) ?></td>
                                        <td>
                                            Gedung: <?= esc($p['nama_gedung']) ?><br>
                                            Lantai: <?= esc($p['nama_lantai']) ?><br>
                                            Ruangan: <?= esc($p['nama_ruangan']) ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('perangkat/edit/' . $p['id_perangkat']) ?>" class="btn btn-sm btn-warning" title="Edit Perangkat">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= base_url('perangkat/hapus/' . $p['id_perangkat']) ?>" class="btn btn-sm btn-danger" title="Hapus Perangkat" onclick="return confirm('Yakin ingin menghapus perangkat ini?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </main>
    <footer class="py-4 bg-light mt-auto">
        <div class="container-fluid px-4">
            <div class="d-flex align-items-center justify-content-between small">
                <div class="text-muted">Copyright &copy; Your Website 2023</div>
                <div>
                    <a href="#">Privacy Policy</a>
                    &middot;
                    <a href="#">Terms &amp; Conditions</a>
                </div>
            </div>
        </div>
    </footer>
</div>