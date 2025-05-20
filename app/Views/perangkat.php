<?= $this->extend('template') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Daftar Perangkat</h1>

    <!-- Tombol Tambah -->
    <button id="btnTambah" class="btn btn-success btn-sm mb-3">
        <i class="fas fa-plus"></i> Tambah Perangkat
    </button>

    <!-- Form Tambah Perangkat (hidden by default) -->
    <div id="formTambah" class="card mb-4 shadow-sm" style="display:none;">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-microchip me-2"></i> Tambah Perangkat
        </div>
        <div class="card-body">
            <form action="<?= base_url('/perangkat/simpan') ?>" method="post">
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
                <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                <button type="button" id="btnBatal" class="btn btn-secondary btn-sm">Batal</button>
            </form>
        </div>
    </div>

    <!-- Tabel Perangkat -->
    <div id="tabelPerangkat" class="card mb-4 shadow-sm">
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

<!-- AJAX Script -->
<script>
    const btnTambah = document.getElementById('btnTambah');
    const btnBatal = document.getElementById('btnBatal');
    const formTambah = document.getElementById('formTambah');
    const tabelPerangkat = document.getElementById('tabelPerangkat');

    btnTambah.addEventListener('click', function() {
        formTambah.style.display = 'block';
        tabelPerangkat.style.display = 'none';
        btnTambah.style.display = 'none';
    });

    btnBatal.addEventListener('click', function() {
        formTambah.style.display = 'none';
        tabelPerangkat.style.display = 'block';
        btnTambah.style.display = 'inline-block';
    });

    //Panggil SweetAlert dari CDN
    // Tangani klik tombol hapus pakai SweetAlert confirm
    // document.querySelectorAll('.btn-hapus').forEach(button => {
    //     button.addEventListener('click', function(e) {
    //         e.preventDefault();
    //         const href = this.getAttribute('href');

    //         Swal.fire({
    //             title: 'Yakin ingin menghapus?',
    //             text: "Data yang dihapus tidak bisa dikembalikan!",
    //             icon: 'warning',
    //             showCancelButton: true,
    //             confirmButtonColor: '#d33',
    //             cancelButtonColor: '#3085d6',
    //             confirmButtonText: 'Ya, hapus!',
    //             cancelButtonText: 'Batal'
    //         }).then((result) => {
    //             if (result.isConfirmed) {
    //                 // Kalau user klik 'Ya', arahkan ke link hapus
    //                 window.location.href = href;
    //             }
    //         });
    //     });
    // });

    // // Tampilkan alert sukses kalau ada flashdata 'success'
    // <?php if (session()->getFlashdata('success')): ?>
    //     Swal.fire({
    //         icon: 'success',
    //         title: 'Sukses',
    //         text: '<?= session()->getFlashdata('success') ?>',
    //         timer: 2000,
    //         showConfirmButton: false
    //     });
    // <?php endif; ?>

    document.getElementById('id_gedung').addEventListener('change', function() {
        const idGedung = this.value;

        if (!idGedung) {
            // Reset dropdown lantai dan ruangan kalau tidak ada gedung
            document.getElementById('id_lantai').innerHTML = '<option value="">-- Pilih Lantai --</option>';
            document.getElementById('id_ruangan').innerHTML = '<option value="">-- Pilih Ruangan --</option>';
            return;
        }

        fetch(`<?= base_url('perangkat/getLantai') ?>/${idGedung}`)
            .then(response => response.json())
            .then(data => {
                const lantaiSelect = document.getElementById('id_lantai');
                lantaiSelect.innerHTML = '<option value="">-- Pilih Lantai --</option>';
                data.forEach(lantai => {
                    lantaiSelect.innerHTML += `<option value="${lantai.id_lantai}">${lantai.nama_lantai}</option>`;
                });

                document.getElementById('id_ruangan').innerHTML = '<option value="">-- Pilih Ruangan --</option>';
            });
    });

    document.getElementById('id_lantai').addEventListener('change', function() {
        const idLantai = this.value;

        if (!idLantai) {
            document.getElementById('id_ruangan').innerHTML = '<option value="">-- Pilih Ruangan --</option>';
            return;
        }

        fetch(`<?= base_url('perangkat/getRuangan') ?>/${idLantai}`)
            .then(response => response.json())
            .then(data => {
                const ruanganSelect = document.getElementById('id_ruangan');
                ruanganSelect.innerHTML = '<option value="">-- Pilih Ruangan --</option>';
                data.forEach(ruangan => {
                    ruanganSelect.innerHTML += `<option value="${ruangan.id_ruangan}">${ruangan.nama_ruangan}</option>`;
                });
            });
    });
</script>

<?= $this->endSection() ?>