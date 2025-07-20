<?= $this->extend('template') ?>
<?= $this->section('content') ?>
<main>
    <div class="container-fluid px-2">
        <h1 class="mt-4">Data Bangunan Poliwangi</h1>
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-building me-1"></i> Data Bangunan Poliwangi
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatablesSimple" class="table table-striped table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Nama Gedung</th>
                                <th scope="col">Deskripsi Gedung</th>
                                <!-- <th scope="col">Aksi</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($gedung as $g): ?>
                                <tr>
                                    <td><?= esc($g['id_gedung']) ?></td>
                                    <td><?= esc($g['nama_gedung']) ?></td>
                                    <td><?= esc($g['deskripsi']) ?></td>
                                    <!-- <td>
                                        <a href="<?= base_url('perangkat/edit/' . $g['id_gedung']) ?>" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('perangkat/hapus/' . $g['id_gedung']) ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td> -->
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</main>
<?= $this->endSection() ?>