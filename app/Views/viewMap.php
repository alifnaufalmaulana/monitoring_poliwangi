<div id="map" style="width: 100%; height: 80vh;"></div>

<script>
    const map = L.map('map').setView([-8.29503382017551, 114.30717587038237], 50); // Koordinat Poliwangi

    // 🔗 Layer Satelit dari Esri (bebas digunakan dan cukup update)
    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri'
    }).addTo(map);

    // 🧭 Tambahkan marker (contoh: kampus Poliwangi)
    const markerTi = L.marker([-8.294243754270415, 114.30725686875967]).addTo(map)
        .bindPopup("<b>Jurusan Bisnis & Informatika</b>")
        .openPopup();

    const markerTm = L.marker([-8.293922994375071, 114.30686611224104]).addTo(map)
        .bindPopup("<b>Jurusan Teknik Mesin</b>")
        .openPopup();

    const markerTs = L.marker([-8.294406331097305, 114.30685501120455]).addTo(map)
        .bindPopup("<b>Jurusan Teknik Sipil</b>")
        .openPopup();

    const markerGPTA = L.marker([-8.294328137735747, 114.30573328680325]).addTo(map)
        .bindPopup("<b>Gedung Prabu Tawangalun</b>")
        .openPopup();

    const markerHotel = L.marker([-8.293274882825061, 114.30682170808706]).addTo(map)
        .bindPopup("<b>Hotel Poliwangi</b>")
        .openPopup();

    const markerGKT = L.marker([-8.292446616676521, 114.3057493479127]).addTo(map)
        .bindPopup("<b>Gedung Kuliah Tepadu</b>")
        .openPopup();

    const markerPerpus = L.marker([-8.295537691873253, 114.30674387841817]).addTo(map)
        .bindPopup("<b>Perpustakaan Poliwangi</b>")
        .openPopup();

    const markerAula = L.marker([-8.29546070879696, 114.30727612044645]).addTo(map)
        .bindPopup("<b>Aula Poliwangi</b>")
        .openPopup();

    const markerSatpam = L.marker([-8.295885987620325, 114.30763378424578]).addTo(map)
        .bindPopup("<b>Pos Satpam</b>")
        .openPopup();
</script>