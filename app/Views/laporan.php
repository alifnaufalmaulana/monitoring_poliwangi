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
            <div id="formTambahEdit" class="card mb-4 shadow-sm" style="display: none;">
                <div class="card-body">
                    <form id="formLaporan" action="<?= base_url('laporan/simpan') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id_laporan" id="id_laporan">
                        <input type="hidden" name="id_ruangan_terpilih" id="id_ruangan_terpilih">

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="id_perangkat" class="form-label">Perangkat</label>
                                <select name="id_perangkat" id="id_perangkat" class="form-select" required>
                                    <option value="">-- Pilih Perangkat --</option>
                                    <?php foreach ($perangkat as $p) : ?>
                                        <option value="<?= esc($p['id_perangkat']) ?>">
                                            <?= esc($p['nama_perangkat']) ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Jenis Bencana</label>
                                <select name="nama_bencana" id="nama_bencana" class="form-select" required>
                                    <option value="">-- Pilih Jenis Bencana --</option>
                                    <?php foreach ($daftar_kebencanaan as $b) : ?>
                                        <option value="<?= esc($b['jenis_bencana']) ?>">
                                            <?= ucfirst(esc($b['jenis_bencana'])) ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="status_bencana" class="form-label">Status Kebencanaan</label>
                                <select name="status_bencana" id="status_bencana" class="form-select" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="Sedang Ditangani">Sedang Ditangani</option>
                                    <option value="Selesai Ditangani">Selesai Ditangani</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <p class="mb-1">
                                    **Lokasi Perangkat Terpilih:**
                                    <span id="lokasi_gedung_display">N/A</span>,
                                    <span id="lokasi_lantai_display">N/A</span>,
                                    <span id="lokasi_ruangan_display">N/A</span>
                                </p>
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
            </div>

            <div id="tabelLaporan" class="card mb-4 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatablesSimple" class="table table-bordered table-striped dataTable dtr-inline">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Perangkat</th>
                                    <th>Jenis Bencana</th>
                                    <th>Lokasi</th>
                                    <th>Status</th>
                                    <th>Deskripsi</th>
                                    <th>Waktu Laporan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                <?php foreach ($laporan as $row) : ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= esc($row['nama_perangkat']) ?></td>
                                        <td><?= esc($row['nama_bencana']) ?></td>
                                        <td>
                                            Gedung: <?= esc($row['nama_gedung'] ?? 'N/A') ?><br>
                                            Lantai: <?= esc($row['nama_lantai'] ?? 'N/A') ?><br>
                                            Ruangan: <?= esc($row['nama_ruangan'] ?? 'N/A') ?>
                                        </td>
                                        <td><?= esc($row['status_bencana']) ?></td>
                                        <td><?= esc($row['deskripsi']) ?></td>
                                        <td><?= date('d/m/Y H:i:s', strtotime($row['waktu_laporan'])) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning btn-edit" data-id="<?= $row['id_laporan'] ?>"><i class="fas fa-edit"></i></button>
                                            <button class="btn btn-sm btn-danger btn-hapus" data-id="<?= $row['id_laporan'] ?>"><i class="fas fa-trash-alt"></i></button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($laporan)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data laporan.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const formTambahEdit = document.getElementById('formTambahEdit');
    const btnTambah = document.getElementById('btnTambah');
    const btnBatal = document.getElementById('btnBatal');
    const formLaporan = document.getElementById('formLaporan');
    const idLaporanInput = document.getElementById('id_laporan');
    const tabelLaporan = document.getElementById('tabelLaporan');

    const idPerangkatSelect = document.getElementById('id_perangkat');
    const jenisBencanaSelect = document.getElementById('nama_bencana');
    const idRuanganTerpilihInput = document.getElementById('id_ruangan_terpilih');
    const lokasiGedungDisplay = document.getElementById('lokasi_gedung_display');
    const lokasiLantaiDisplay = document.getElementById('lokasi_lantai_display');
    const lokasiRuanganDisplay = document.getElementById('lokasi_ruangan_display');
    const statusBencanaSelect = document.getElementById('status_bencana');
    const deskripsiTextarea = document.getElementById('deskripsi');

    btnTambah.addEventListener('click', () => {
        resetForm();
        formLaporan.action = '<?= base_url('/laporan/simpan') ?>';
        formTambahEdit.style.display = 'block';
        btnTambah.style.display = 'none';
        tabelLaporan.style.display = 'none'; // Sembunyikan container tabel
    });

    btnBatal.addEventListener('click', () => {
        resetForm();
        tabelLaporan.style.display = 'block'; // Tampilkan kembali container tabel
    });

    function resetForm() {
        formLaporan.reset();
        idLaporanInput.value = '';
        // Penting: Reset hidden input dan display lokasi di sini juga!
        idRuanganTerpilihInput.value = '';
        lokasiGedungDisplay.textContent = '';
        lokasiLantaiDisplay.textContent = 'N/A';
        lokasiRuanganDisplay.textContent = 'N/A';
        formTambahEdit.style.display = 'none';
        btnTambah.style.display = 'inline-block';
        formLaporan.action = '<?= base_url('/laporan/simpan') ?>';
    }

    document.addEventListener('DOMContentLoaded', function() {
        // currentPerangkatData akan menyimpan detail perangkat yang dipilih
        let currentPerangkatData = null;

        idPerangkatSelect.addEventListener('change', function() {
            const perangkatId = this.value;

            // Selalu reset display dan hidden input saat perangkat berubah atau dipilih ulang
            idRuanganTerpilihInput.value = '';
            lokasiGedungDisplay.textContent = '';
            lokasiLantaiDisplay.textContent = 'N/A';
            lokasiRuanganDisplay.textContent = 'N/A';
            currentPerangkatData = null; // Reset data perangkat

            if (perangkatId) {
                // Fetch detail perangkat (termasuk lokasi) dari API
                fetch(`<?= base_url('/api/perangkat/') ?>/${perangkatId}`)
                    .then(res => {
                        if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                        return res.json();
                    })
                    .then(response => {
                        if (response.status === 'success' && response.data) {
                            currentPerangkatData = response.data;

                            // Simpan ID ruangan ke hidden input
                            idRuanganTerpilihInput.value = currentPerangkatData.id_ruangan;

                            // Update elemen display untuk feedback pengguna
                            lokasiGedungDisplay.textContent = currentPerangkatData.nama_gedung || 'N/A';
                            lokasiLantaiDisplay.textContent = currentPerangkatData.nama_lantai || 'N/A';
                            lokasiRuanganDisplay.textContent = currentPerangkatData.nama_ruangan || 'N/A';

                        } else {
                            Swal.fire('Info', response.message || 'Detail perangkat tidak ditemukan.', 'info');
                            // Pastikan reset jika tidak ditemukan
                            idRuanganTerpilihInput.value = '';
                            lokasiGedungDisplay.textContent = '';
                            lokasiLantaiDisplay.textContent = 'N/A';
                            lokasiRuanganDisplay.textContent = 'N/A';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching perangkat details:', error);
                        Swal.fire('Error', 'Gagal memuat detail lokasi perangkat. Pastikan API endpoint dan data di database sudah benar.', 'error');
                        // Reset on error
                        idRuanganTerpilihInput.value = '';
                        lokasiGedungDisplay.textContent = '';
                        lokasiLantaiDisplay.textContent = 'N/A';
                        lokasiRuanganDisplay.textContent = 'N/A';
                    });
            }
        });

        formLaporan.addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data laporan akan disimpan!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData(formLaporan);
                    const url = formLaporan.action;

                    fetch(url, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire('Berhasil!', data.message, 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Gagal!', data.message || 'Terjadi kesalahan saat menyimpan data.', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error!', 'Terjadi kesalahan saat menyimpan data.', 'error');
                        });
                }
            });
        });

        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                formLaporan.action = `<?= base_url('/laporan/update') ?>/${id}`;

                fetch(`<?= base_url('laporan/edit') ?>/${id}`)
                    .then(response => {
                        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                        return response.json();
                    })
                    .then(dataLaporanEdit => {
                        btnTambah.style.display = 'none';
                        formTambahEdit.style.display = 'block';
                        tabelLaporan.style.display = 'none';

                        idLaporanInput.value = dataLaporanEdit.id_laporan;
                        idPerangkatSelect.value = dataLaporanEdit.id_perangkat;
                        jenisBencanaSelect.value = dataLaporanEdit.nama_bencana;
                        statusBencanaSelect.value = dataLaporanEdit.status_bencana;
                        deskripsiTextarea.value = dataLaporanEdit.deskripsi;

                        idRuanganTerpilihInput.value = dataLaporanEdit.id_ruangan || '';
                        lokasiGedungDisplay.textContent = dataLaporanEdit.nama_gedung || 'N/A';
                        lokasiLantaiDisplay.textContent = dataLaporanEdit.nama_lantai || 'N/A';
                        lokasiRuanganDisplay.textContent = dataLaporanEdit.nama_ruangan || 'N/A';

                        const event = new Event('change');
                        idPerangkatSelect.dispatchEvent(event);

                    })
                    .catch(error => {
                        console.error('Error fetching laporan data for edit:', error);
                        Swal.fire('Error', 'Gagal memuat data laporan untuk diedit.', 'error');
                    });
            });
        });

        document.querySelectorAll('.btn-hapus').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Anda tidak akan bisa mengembalikan data ini!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`<?= base_url('laporan/hapus') ?>/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => {
                                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                                return response.json();
                            })
                            .then(data => {
                                if (data.status === 'success') {
                                    Swal.fire('Dihapus!', data.message, 'success').then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire('Gagal!', data.message || 'Terjadi kesalahan saat menghapus data.', 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data.', 'error');
                            });
                    }
                });
            });
        });

        <?php if (session()->getFlashdata('message')): ?>
            Swal.fire({
                icon: '<?= session()->getFlashdata('type') ?>',
                title: '<?= session()->getFlashdata('title') ?>',
                text: '<?= session()->getFlashdata('message') ?>',
                confirmButtonText: 'Oke'
            });
        <?php endif; ?>
    });
</script>

<?= $this->endSection() ?>