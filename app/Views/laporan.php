<?= $this->extend('template') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Daftar Laporan</h1>
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-clipboard-list me-2"></i> Data Laporan Bencana
        </div>
        <div class="card-body">
            <div class="mb-3">
                <button id="btnTambah" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Tambah Laporan
                </button>
            </div>
            <!-- Form Tambah Laporan (sembunyi secara default) -->
            <div id="formLaporan" class="mb-4" style="display: none;">
                <form action="<?= base_url('laporan/simpan') ?>" method="post">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="id_kebencanaan" class="form-label">Kebencanaan</label>
                            <select name="id_kebencanaan" id="id_kebencanaan" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                <?php foreach ($daftar_kebencanaan as $k): ?>
                                    <option value="<?= esc($k['id_kebencanaan']) ?>">
                                        <?= esc($k['nama_bencana']) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="status_bencana" class="form-label">Status</label>
                            <select name="status_bencana" id="status_bencana" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                <option value="Aktif">Aktif</option>
                                <option value="Ditangani">Ditangani</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                    <button type="button" id="btnBatal" class="btn btn-secondary btn-sm">Batal</button>
                </form>
            </div>

            <!-- Tabel Data Laporan -->
            <div id="tabelLaporan">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Nama Bencana</th>
                                <th scope="col">Deskripsi</th>
                                <th scope="col">Status</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($laporan as $l): ?>
                                <tr>
                                    <td><?= esc($l['id_laporan']) ?></td>
                                    <td><?= esc($l['nama_bencana']) ?></td>
                                    <td><?= esc($l['deskripsi']) ?></td>
                                    <td><?= esc($l['status_bencana']) ?></td>
                                    <td>
                                        <a href="<?= base_url('laporan/edit/' . $l['id_laporan']) ?>" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('laporan/hapus/' . $l['id_laporan']) ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus laporan ini?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    const btnTambah = document.getElementById('btnTambah');
    const btnBatal = document.getElementById('btnBatal');
    const formLaporan = document.getElementById('formLaporan');
    const tabelLaporan = document.getElementById('tabelLaporan');

    btnTambah.addEventListener('click', function() {
        formLaporan.style.display = 'block';
        tabelLaporan.style.display = 'none';
        btnTambah.style.display = 'none';
    });

    btnBatal.addEventListener('click', function() {
        formLaporan.style.display = 'none';
        tabelLaporan.style.display = 'block';
        btnTambah.style.display = 'inline-block';
    });
</script>

<?= $this->endSection() ?>