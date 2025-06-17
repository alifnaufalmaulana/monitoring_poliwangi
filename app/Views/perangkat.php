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

            <div id="formTambah" class="card mb-4 shadow-sm" style="display: none;">
                <div class="card-body">
                    <form id="formPerangkat" action="<?= base_url('/perangkat/simpan') ?>" method="post" onsubmit="return validateKoordinat();">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id_perangkat" id="id_perangkat">
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

                        <input type="hidden" name="pos_x" id="pos_x">
                        <input type="hidden" name="pos_y" id="pos_y">

                        <div id="denahContainer" style="display: none; margin-top: 20px;">
                            <label class="form-label">Klik pada Denah untuk menentukan posisi perangkat</label>

                            <div id="floorplanWrapper" style="width: 100%; height: 60vh; position: relative; border: 1px solid #ccc;">
                                <img id="denahImage" src="" alt="Denah Lantai"
                                    style="width: 100%; height: 100%; object-fit: contain; cursor: crosshair;">
                                <div id="markersOnFloorplan"
                                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none;"></div>
                            </div>

                            <div class="mt-2"><strong>Posisi Klik:</strong> <span id="koordinatTeks">Belum dipilih</span></div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm mt-2">Simpan</button>
                        <button type="button" id="btnBatal" class="btn btn-secondary btn-sm mt-2">Batal</button>
                    </form>
                </div>
            </div>

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
                                <?php $no = 1; ?>
                                <?php foreach ($perangkat as $p): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= esc($p['nama_perangkat']) ?></td>
                                        <td>
                                            Gedung: <?= esc($p['nama_gedung']) ?><br>
                                            Lantai: <?= esc($p['nama_lantai']) ?><br>
                                            Ruangan: <?= esc($p['nama_ruangan']) ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-warning btn-edit" data-id="<?= $p['id_perangkat'] ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger btn-hapus" data-id="<?= $p['id_perangkat'] ?>">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
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

<script>
    const formTambah = document.getElementById('formTambah');
    const btnTambah = document.getElementById('btnTambah');
    const btnBatal = document.getElementById('btnBatal');
    const denahContainer = document.getElementById('denahContainer');
    const denahImage = document.getElementById('denahImage');
    const markersOnFloorplan = document.getElementById('markersOnFloorplan');
    const koordinatTeks = document.getElementById('koordinatTeks');
    const formPerangkat = document.getElementById('formPerangkat');
    const idPerangkatInput = document.getElementById('id_perangkat');
    const tabelPerangkat = document.getElementById('tabelPerangkat'); // <<< BARU: Referensi ke elemen tabel

    btnTambah.addEventListener('click', () => {
        resetForm();
        formPerangkat.action = '<?= base_url('/perangkat/simpan') ?>'; // Set action untuk simpan baru
        formTambah.style.display = 'block';
        btnTambah.style.display = 'none';
        tabelPerangkat.style.display = 'none'; // <<< BARU: Sembunyikan tabel
    });

    btnBatal.addEventListener('click', () => {
        resetForm();
        tabelPerangkat.style.display = 'block'; // <<< BARU: Tampilkan kembali tabel saat batal
    });

    function resetForm() {
        formPerangkat.reset();
        idPerangkatInput.value = '';
        document.getElementById('id_lantai').innerHTML = '<option value="">-- Pilih Lantai --</option>';
        document.getElementById('id_ruangan').innerHTML = '<option value="">-- Pilih Ruangan --</option>';
        markersOnFloorplan.innerHTML = '';
        denahImage.src = '';
        koordinatTeks.textContent = 'Belum dipilih';
        denahContainer.style.display = 'none';
        formTambah.style.display = 'none';
        btnTambah.style.display = 'inline-block';
        formPerangkat.action = '<?= base_url('/perangkat/simpan') ?>';
    }

    function validateKoordinat() {
        if (!document.getElementById('pos_x').value || !document.getElementById('pos_y').value) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                text: 'Silakan klik pada denah untuk menentukan posisi perangkat.',
                confirmButtonText: 'Oke'
            });
            return false;
        }
        return true;
    }

    function tampilkanMarker(posX, posY) {
        markersOnFloorplan.innerHTML = '';
        const marker = document.createElement('div');
        marker.style.position = 'absolute';
        marker.style.width = '12px';
        marker.style.height = '12px';
        marker.style.backgroundColor = 'green';
        marker.style.border = '2px solid white';
        marker.style.borderRadius = '50%';
        marker.style.left = `${posX}%`;
        marker.style.top = `${posY}%`;
        marker.style.transform = 'translate(-50%, -50%)';
        markersOnFloorplan.appendChild(marker);
        koordinatTeks.textContent = `X: ${posX}%, Y: ${posY}%`;
    }

    document.getElementById('id_gedung').addEventListener('change', function() {
        const idGedung = this.value;
        if (idGedung) {
            fetch(`<?= base_url('perangkat/getLantai') ?>/${idGedung}`)
                .then(response => response.json())
                .then(data => {
                    const lantaiSelect = document.getElementById('id_lantai');
                    lantaiSelect.innerHTML = '<option value="">-- Pilih Lantai --</option>';
                    data.forEach(l => lantaiSelect.innerHTML += `<option value="${l.id_lantai}">${l.nama_lantai}</option>`);
                    document.getElementById('id_ruangan').innerHTML = '<option value="">-- Pilih Ruangan --</option>'; // Reset ruangan
                    denahImage.src = ''; // Reset denah
                    denahContainer.style.display = 'none';
                    markersOnFloorplan.innerHTML = '';
                    koordinatTeks.textContent = 'Belum dipilih';
                    document.getElementById('pos_x').value = '';
                    document.getElementById('pos_y').value = '';
                })
                .catch(error => {
                    console.error('Error fetching lantai:', error);
                    Swal.fire('Error', 'Gagal memuat daftar lantai.', 'error');
                });
        } else {
            document.getElementById('id_lantai').innerHTML = '<option value="">-- Pilih Lantai --</option>';
            document.getElementById('id_ruangan').innerHTML = '<option value="">-- Pilih Ruangan --</option>';
            denahImage.src = '';
            denahContainer.style.display = 'none';
            markersOnFloorplan.innerHTML = '';
            koordinatTeks.textContent = 'Belum dipilih';
            document.getElementById('pos_x').value = '';
            document.getElementById('pos_y').value = '';
        }
    });

    document.getElementById('id_lantai').addEventListener('change', function() {
        const idLantai = this.value;
        if (idLantai) {
            // Ambil daftar ruangan
            fetch(`<?= base_url('perangkat/getRuangan') ?>/${idLantai}`)
                .then(response => response.json())
                .then(data => {
                    const ruanganSelect = document.getElementById('id_ruangan');
                    ruanganSelect.innerHTML = '<option value="">-- Pilih Ruangan --</option>';
                    data.forEach(r => ruanganSelect.innerHTML += `<option value="${r.id_ruangan}">${r.nama_ruangan}</option>`);
                })
                .catch(error => {
                    console.error('Error fetching ruangan:', error);
                    Swal.fire('Error', 'Gagal memuat daftar ruangan.', 'error');
                });

            // Ambil denah
            fetch(`<?= base_url('perangkat/getDenah') ?>/${idLantai}`)
                .then(response => response.json())
                .then(data => {
                    if (data.denah) {
                        denahImage.src = `<?= base_url('aset/denah') ?>/${data.denah}`;
                        denahContainer.style.display = 'block';
                        // Jika sedang edit dan ada koordinat, tampilkan marker
                        if (idPerangkatInput.value && document.getElementById('pos_x').value && document.getElementById('pos_y').value) {
                            tampilkanMarker(document.getElementById('pos_x').value, document.getElementById('pos_y').value);
                        } else {
                            markersOnFloorplan.innerHTML = ''; // Pastikan marker kosong jika bukan edit
                            koordinatTeks.textContent = 'Belum dipilih';
                            document.getElementById('pos_x').value = '';
                            document.getElementById('pos_y').value = '';
                        }
                    } else {
                        denahImage.src = '';
                        denahContainer.style.display = 'none';
                        markersOnFloorplan.innerHTML = '';
                        koordinatTeks.textContent = 'Belum dipilih';
                        document.getElementById('pos_x').value = '';
                        document.getElementById('pos_y').value = '';
                    }
                })
                .catch(error => {
                    console.error('Error fetching denah:', error);
                    Swal.fire('Error', 'Gagal memuat denah.', 'error');
                });
        } else {
            document.getElementById('id_ruangan').innerHTML = '<option value="">-- Pilih Ruangan --</option>';
            denahImage.src = '';
            denahContainer.style.display = 'none';
            markersOnFloorplan.innerHTML = '';
            koordinatTeks.textContent = 'Belum dipilih';
            document.getElementById('pos_x').value = '';
            document.getElementById('pos_y').value = '';
        }
    });

    denahImage.addEventListener('click', function(e) {
        const rect = this.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        const percentX = ((x / this.offsetWidth) * 100).toFixed(2);
        const percentY = ((y / this.offsetHeight) * 100).toFixed(2);
        document.getElementById('pos_x').value = percentX;
        document.getElementById('pos_y').value = percentY;
        tampilkanMarker(percentX, percentY);
    });

    // Menangani submit form dengan SweetAlert
    formPerangkat.addEventListener('submit', function(e) {
        e.preventDefault(); // Mencegah submit default

        if (!validateKoordinat()) {
            return;
        }

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data perangkat akan disimpan!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData(formPerangkat);
                const url = formPerangkat.action;
                const method = 'POST'; // Tetap POST, CI4 akan menangani PUT/PATCH via method spoofing jika perlu

                fetch(url, {
                        method: method,
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire(
                                'Berhasil!',
                                data.message,
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Gagal!',
                                data.message,
                                'error'
                            );
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire(
                            'Error!',
                            'Terjadi kesalahan saat menyimpan data.',
                            'error'
                        );
                    });
            }
        });
    });

    // tombol edit
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            formPerangkat.action = `<?= base_url('/perangkat/update') ?>/${id}`; // Set action untuk update

            fetch(`<?= base_url('perangkat/getPerangkat') ?>/${id}`)
                .then(response => response.json())
                .then(data => {
                    btnTambah.style.display = 'none';
                    formTambah.style.display = 'block';
                    tabelPerangkat.style.display = 'none'; // <<< BARU: Sembunyikan tabel saat mode edit
                    idPerangkatInput.value = data.id_perangkat;
                    document.getElementById('nama_perangkat').value = data.nama_perangkat;
                    document.getElementById('jenis_perangkat').value = data.jenis_perangkat;
                    document.getElementById('pos_x').value = data.pos_x;
                    document.getElementById('pos_y').value = data.pos_y;

                    // isi dropdown
                    document.getElementById('id_gedung').value = data.id_gedung;
                    fetch(`<?= base_url('perangkat/getLantai') ?>/${data.id_gedung}`)
                        .then(r => r.json()).then(lantai => {
                            const s = document.getElementById('id_lantai');
                            s.innerHTML = '<option value="">-- Pilih Lantai --</option>';
                            lantai.forEach(l => {
                                s.innerHTML += `<option value="${l.id_lantai}" ${l.id_lantai == data.id_lantai ? 'selected' : ''}>${l.nama_lantai}</option>`;
                            });

                            // ambil ruangan dan denah
                            fetch(`<?= base_url('perangkat/getRuangan') ?>/${data.id_lantai}`)
                                .then(r => r.json()).then(ruangan => {
                                    const s2 = document.getElementById('id_ruangan');
                                    s2.innerHTML = '<option value="">-- Pilih Ruangan --</option>';
                                    ruangan.forEach(r => {
                                        s2.innerHTML += `<option value="${r.id_ruangan}" ${r.id_ruangan == data.id_ruangan ? 'selected' : ''}>${r.nama_ruangan}</option>`;
                                    });
                                });

                            fetch(`<?= base_url('perangkat/getDenah') ?>/${data.id_lantai}`)
                                .then(r => r.json()).then(d => {
                                    if (d.denah) {
                                        denahImage.src = `<?= base_url('aset/denah') ?>/${d.denah}`;
                                        denahContainer.style.display = 'block';
                                        if (data.pos_x && data.pos_y) {
                                            tampilkanMarker(data.pos_x, data.pos_y);
                                        } else {
                                            markersOnFloorplan.innerHTML = '';
                                            koordinatTeks.textContent = 'Belum dipilih';
                                        }
                                    } else {
                                        denahImage.src = '';
                                        denahContainer.style.display = 'none';
                                        markersOnFloorplan.innerHTML = '';
                                        koordinatTeks.textContent = 'Belum dipilih';
                                    }
                                });
                        });
                })
                .catch(error => {
                    console.error('Error fetching perangkat data:', error);
                    Swal.fire('Error', 'Gagal memuat data perangkat untuk diedit.', 'error');
                });
        });
    });

    // tombol hapus dengan SweetAlert (tidak ada perubahan di sini)
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
                    fetch(`<?= base_url('perangkat/hapus') ?>/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire(
                                    'Dihapus!',
                                    data.message,
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire(
                                    'Gagal!',
                                    data.message,
                                    'error'
                                );
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat menghapus data.',
                                'error'
                            );
                        });
                }
            });
        });
    });

    // Check for flashdata messages (from CodeIgniter controller)
    <?php if (session()->getFlashdata('message')): ?>
        Swal.fire({
            icon: '<?= session()->getFlashdata('type') ?>',
            title: '<?= session()->getFlashdata('title') ?>',
            text: '<?= session()->getFlashdata('message') ?>',
            confirmButtonText: 'Oke'
        });
    <?php endif; ?>
</script>

<?= $this->endSection() ?>