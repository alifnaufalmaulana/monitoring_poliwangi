<?= $this->extend('template') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Riwayat Perangkat</h1>
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-history me-2"></i> Riwayat Perangkat Kebencanaan
        </div>
        <div class="card-body">
            <!-- FORM FILTER -->
            <form method="get" action="<?= base_url('riwayat-perangkat') ?>" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="tanggal_awal" class="form-control" value="<?= esc($_GET['tanggal_awal'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="tanggal_akhir" class="form-control" value="<?= esc($_GET['tanggal_akhir'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Aksi</label>
                    <select name="aksi" class="form-select">
                        <option value="">-- Semua Aksi --</option>
                        <option value="aktif" <?= @$_GET['aksi'] == 'aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="bahaya" <?= @$_GET['aksi'] == 'bahaya' ? 'selected' : '' ?>>Bahaya</option>
                        <option value="mati" <?= @$_GET['aksi'] == 'mati' ? 'selected' : '' ?>>Mati</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Gedung</label>
                    <select name="id_gedung" class="form-select">
                        <option value="">-- Semua Gedung --</option>
                        <?php foreach ($gedung as $g): ?>
                            <option value="<?= $g['id_gedung'] ?>" <?= @$_GET['id_gedung'] == $g['id_gedung'] ? 'selected' : '' ?>>
                                <?= esc($g['nama_gedung']) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Lantai</label>
                    <select name="id_lantai" class="form-select">
                        <option value="">-- Semua Lantai --</option>
                        <?php foreach ($lantai as $l): ?>
                            <option value="<?= $l['id_lantai'] ?>" <?= @$_GET['id_lantai'] == $l['id_lantai'] ? 'selected' : '' ?>>
                                <?= esc($l['nama_lantai']) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="col-md-3 align-self-end">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                    <a href="<?= base_url('riwayat-perangkat') ?>" class="btn btn-secondary">Reset</a>
                </div>
            </form>
            <!-- TABEL RIWAYAT -->
            <div id="tabelRiwayat" class="card mb-4 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatablesSimple" class="table table-striped table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Perangkat</th>
                                    <th>Lokasi</th>
                                    <th>Aksi</th>
                                    <th>Status</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                foreach ($riwayat as $r): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= esc($r['nama_perangkat']) ?></td>
                                        <td>
                                            Gedung: <?= esc($r['nama_gedung']) ?><br>
                                            Lantai: <?= esc($r['nama_lantai']) ?><br>
                                            Ruangan: <?= esc($r['nama_ruangan']) ?>
                                        </td>
                                        <td><?= ucfirst(esc($r['aksi'])) ?></td>
                                        <td>
                                            <?php
                                            $status = strtolower($r['status_perangkat']);
                                            $badgeClass = match ($status) {
                                                'bahaya' => 'badge bg-danger',
                                                'aktif' => 'badge bg-success',
                                                'mati' => 'badge bg-secondary',
                                                default => 'badge bg-warning',
                                            };
                                            ?>
                                            <span class="<?= $badgeClass ?>">
                                                <?= ucfirst($status) ?>
                                            </span>
                                        </td>

                                        <td><?= date('d-m-Y H:i:s', strtotime($r['waktu'])) ?></td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>

                        </table>


                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>