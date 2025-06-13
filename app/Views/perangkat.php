<?= $this->extend('template') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Daftar Perangkat</h1>
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-microchip me-2"></i> Daftar Perangkat
        </div>
        <div class="card-body">
            <div class="mb-3">
                <button id="btnTambah" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Tambah Perangkat
                </button>
            </div>

            <!-- FORM TAMBAH -->
            <div id="formTambah" class="card mb-4 shadow-sm" style="display: none;">
                <div class="card-body">
                    <form action="<?= base_url('/perangkat/simpan') ?>" method="post" onsubmit="return validateKoordinat();">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="nama_perangkat" class="form-label">Nama Perangkat</label>
                            <input type="text" class="form-control" name="nama_perangkat" id="nama_perangkat" required>
                        </div>

                        <div class="mb-2">
                            <label for="id_gedung" class="form-label">Pilih Gedung</label>
                            <select name="id_gedung" id="id_gedung" class="form-select" required>
                                <option value="">-- Pilih Gedung --</option>
                                <?php foreach ($gedung as $g): ?>
                                    <option value="<?= esc($g['id_gedung']) ?>"><?= esc($g['nama_gedung']) ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="id_lantai" class="form-label">Pilih Lantai</label>
                            <select name="id_lantai" id="id_lantai" class="form-select" required>
                                <option value="">-- Pilih Lantai --</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="id_ruangan" class="form-label">Pilih Ruangan</label>
                            <select name="id_ruangan" id="id_ruangan" class="form-select" required>
                                <option value="">-- Pilih Ruangan --</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="jenis_perangkat" class="form-label">Jenis Perangkat</label>
                            <input type="text" class="form-control" name="jenis_perangkat" id="jenis_perangkat" required>
                        </div>

                        <!-- Input hidden untuk koordinat -->
                        <input type="hidden" name="pos_x" id="pos_x">
                        <input type="hidden" name="pos_y" id="pos_y">

                        <!-- Denah -->
                        <div class="mb-3" id="denahContainer" style="display: none;">
                            <label class="form-label">Klik pada Denah untuk menentukan posisi perangkat</label>

                            <!-- Denah & Marker Container -->
                            <div style="position: relative; display: inline-block; width: 100%;">
                                <img id="denahImage" src="" alt="Denah Lantai" style="width: 100%; border: 1px solid #ccc; cursor: crosshair;">
                                <div id="markersOnFloorplan" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none;"></div>
                            </div>

                            <div class="mt-2"><strong>Posisi Klik:</strong> <span id="koordinatTeks">Belum dipilih</span></div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm mt-2">Simpan</button>
                        <button type="button" id="btnBatal" class="btn btn-secondary btn-sm mt-2">Batal</button>
                    </form>
                </div>
            </div>

            <!-- TABEL PERANGKAT -->
            <div id="tabelPerangkat" class="card mb-4 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatablesSimple" class="table table-striped table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Perangkat</th>
                                    <th>Posisi</th>
                                    <th>Aksi</th>
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
                                            <a href="<?= base_url('perangkat/edit/' . $p['id_perangkat']) ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= base_url('perangkat/hapus/' . $p['id_perangkat']) ?>" class="btn btn-sm btn-danger btn-hapus">
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
</div>

<!-- SCRIPT -->
<script>
    const btnTambah = document.getElementById('btnTambah');
    const btnBatal = document.getElementById('btnBatal');
    const formTambah = document.getElementById('formTambah');
    const tabelPerangkat = document.getElementById('tabelPerangkat');
    const denahContainer = document.getElementById('denahContainer');
    const denahImage = document.getElementById('denahImage');
    const koordinatTeks = document.getElementById('koordinatTeks');
    const markersOnFloorplan = document.getElementById('markersOnFloorplan');

    // Tampilkan form tambah
    btnTambah.addEventListener('click', () => {
        formTambah.style.display = 'block';
        tabelPerangkat.style.display = 'none';
        btnTambah.style.display = 'none';
    });

    // Batal tambah
    btnBatal.addEventListener('click', () => {
        formTambah.style.display = 'none';
        tabelPerangkat.style.display = 'block';
        btnTambah.style.display = 'inline-block';
        denahContainer.style.display = 'none';
        denahImage.src = "";
        koordinatTeks.textContent = "Belum dipilih";
        markersOnFloorplan.innerHTML = '';
    });

    // Dropdown gedung -> lantai
    document.getElementById('id_gedung').addEventListener('change', function() {
        const idGedung = this.value;
        document.getElementById('id_lantai').innerHTML = '<option value="">-- Pilih Lantai --</option>';
        document.getElementById('id_ruangan').innerHTML = '<option value="">-- Pilih Ruangan --</option>';
        denahContainer.style.display = 'none';
        denahImage.src = "";
        markersOnFloorplan.innerHTML = '';

        if (idGedung) {
            fetch(`<?= base_url('perangkat/getLantai') ?>/${idGedung}`)
                .then(response => response.json())
                .then(data => {
                    const lantaiSelect = document.getElementById('id_lantai');
                    data.forEach(lantai => {
                        lantaiSelect.innerHTML += `<option value="${lantai.id_lantai}">${lantai.nama_lantai}</option>`;
                    });
                });
        }
    });

    // Dropdown lantai -> ruangan + denah
    document.getElementById('id_lantai').addEventListener('change', function() {
        const idLantai = this.value;
        document.getElementById('id_ruangan').innerHTML = '<option value="">-- Pilih Ruangan --</option>';
        denahContainer.style.display = 'none';
        denahImage.src = "";
        markersOnFloorplan.innerHTML = '';

        if (idLantai) {
            fetch(`<?= base_url('perangkat/getRuangan') ?>/${idLantai}`)
                .then(response => response.json())
                .then(data => {
                    const ruanganSelect = document.getElementById('id_ruangan');
                    data.forEach(ruangan => {
                        ruanganSelect.innerHTML += `<option value="${ruangan.id_ruangan}">${ruangan.nama_ruangan}</option>`;
                    });
                });

            fetch(`<?= base_url('perangkat/getDenah') ?>/${idLantai}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.denah) {
                        denahImage.src = `<?= base_url('aset/denah') ?>/${data.denah}`;
                        denahContainer.style.display = 'block';
                    } else {
                        denahImage.src = "";
                        denahContainer.style.display = 'none';
                    }
                });
        }
    });

    // Klik denah untuk ambil posisi relatif dan tampilkan marker
    denahImage.addEventListener('click', function(e) {
        const rect = this.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        const width = this.offsetWidth;
        const height = this.offsetHeight;

        const percentX = ((x / width) * 100).toFixed(2);
        const percentY = ((y / height) * 100).toFixed(2);

        document.getElementById('pos_x').value = percentX;
        document.getElementById('pos_y').value = percentY;
        koordinatTeks.textContent = `X: ${percentX}%, Y: ${percentY}%`;

        // Hapus marker lama
        markersOnFloorplan.innerHTML = '';

        // Tambahkan marker baru
        const marker = document.createElement('div');
        marker.style.position = 'absolute';
        marker.style.width = '12px';
        marker.style.height = '12px';
        marker.style.backgroundColor = 'green';
        marker.style.border = '2px solid white';
        marker.style.borderRadius = '50%';
        marker.style.left = `${percentX}%`;
        marker.style.top = `${percentY}%`;
        marker.style.transform = 'translate(-50%, -50%)';
        marker.title = `X: ${percentX}%, Y: ${percentY}%`;

        markersOnFloorplan.appendChild(marker);
    });

    // Validasi sebelum submit
    function validateKoordinat() {
        const posX = document.getElementById('pos_x').value;
        const posY = document.getElementById('pos_y').value;

        if (!posX || !posY) {
            alert('Silakan klik pada denah untuk menentukan posisi perangkat terlebih dahulu.');
            return false;
        }

        return true;
    }
</script>

<?= $this->endSection() ?>