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

                <!-- WAKTU -->
                <div class="col-md-3">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="tanggal_awal" class="form-control" value="<?= esc($_GET['tanggal_awal'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="tanggal_akhir" class="form-control" value="<?= esc($_GET['tanggal_akhir'] ?? '') ?>">
                </div>

                <!-- STATUS PERANGKAT -->
                <div class="col-md-3">
                    <label class="form-label">Status Perangkat</label>
                    <select name="status_perangkat" class="form-select">
                        <option value="">-- Pilih Status --</option>
                        <option value="aktif" <?= ($filters['status_perangkat'] ?? '') === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="mati" <?= ($filters['status_perangkat'] ?? '') === 'mati' ? 'selected' : '' ?>>Mati</option>
                        <option value="bahaya" <?= ($filters['status_perangkat'] ?? '') === 'bahaya' ? 'selected' : '' ?>>Bahaya</option>
                    </select>
                </div>

                <!-- BENCANA -->
                <div class="col-md-3">
                    <label class="form-label">Jenis Bencana</label>
                    <select name="jenis_bencana" class="form-select">
                        <option value="">-- Pilih Bencana --</option>
                        <?php foreach ($kebencanaan as $b): ?>
                            <option value="<?= esc($b['jenis_bencana']) ?>" <?= @$_GET['jenis_bencana'] == $b['jenis_bencana'] ? 'selected' : '' ?>>
                                <?= ucfirst(esc($b['jenis_bencana'])) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>

                <!-- GEDUNG -->
                <div class="col-md-3">
                    <label class="form-label">Gedung</label>
                    <select name="id_gedung" id="gedungSelect" class="form-select">
                        <option value="">-- Pilih Gedung --</option>
                        <?php foreach ($gedung as $g): ?>
                            <option value="<?= $g['id_gedung'] ?>" <?= @$_GET['id_gedung'] == $g['id_gedung'] ? 'selected' : '' ?>>
                                <?= esc($g['nama_gedung']) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>

                <!-- LANTAI -->
                <div class="col-md-3">
                    <label class="form-label">Lantai</label>
                    <select name="id_lantai" id="lantaiSelect" class="form-select">
                        <option value="">-- Pilih Lantai --</option>
                        <?php foreach ($lantai as $l): ?>
                            <option value="<?= $l['id_lantai'] ?>" <?= @$_GET['id_lantai'] == $l['id_lantai'] ? 'selected' : '' ?>>
                                <?= esc($l['nama_lantai']) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>

                <!-- RUANGAN -->
                <div class="col-md-3">
                    <label class="form-label">Ruangan</label>
                    <select name="id_ruangan" id="ruanganSelect" class="form-select">
                        <option value="">-- Pilih Ruangan --</option>
                        <?php foreach ($ruangan as $r): ?>
                            <option value="<?= $r['id_ruangan'] ?>" <?= @$_GET['id_ruangan'] == $r['id_ruangan'] ? 'selected' : '' ?>>
                                <?= esc($r['nama_ruangan']) ?>
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
                                    <th>Bencana</th>
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
                                        <td>
                                            <?php if (strtolower($r['status_perangkat']) === 'bahaya'): ?>
                                                <?= esc($r['jenis_bencana'] ?? '-') ?>
                                            <?php else: ?>
                                                -
                                            <?php endif ?>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const gedungSelect = document.getElementById('gedungSelect');
        const lantaiSelect = document.getElementById('lantaiSelect');
        const ruanganSelect = document.getElementById('ruanganSelect');

        // Saat memilih GEDUNG
        gedungSelect.addEventListener('change', function() {
            const gedungId = this.value;
            lantaiSelect.innerHTML = '<option value="">-- Pilih Lantai --</option>';
            ruanganSelect.innerHTML = '<option value="">-- Pilih Ruangan --</option>';

            if (gedungId) {
                fetch(`<?= base_url('/api/lantai/') ?>/${gedungId}`)
                    .then(res => res.json())
                    .then(data => {
                        data.forEach(lantai => {
                            lantaiSelect.innerHTML += `<option value="${lantai.id_lantai}">${lantai.nama_lantai}</option>`;
                        });
                    });
            }
        });

        // Saat memilih LANTAI
        lantaiSelect.addEventListener('change', function() {
            const lantaiId = this.value;
            ruanganSelect.innerHTML = '<option value="">-- Pilih Ruangan --</option>';

            if (lantaiId) {
                fetch(`<?= base_url('/api/ruangan') ?>/${lantaiId}`)
                    .then(res => res.json())
                    .then(data => {
                        data.forEach(ruangan => {
                            ruanganSelect.innerHTML += `<option value="${ruangan.id_ruangan}">${ruangan.nama_ruangan}</option>`;
                        });
                    });
            }
        });
    });
</script>

<?= $this->endSection() ?>