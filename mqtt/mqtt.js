const mqtt = require('mqtt');
const mysql = require('mysql');
const WebSocket = require('ws');

// Koneksi ke database MySQL
const db = mysql.createConnection({
  host: 'localhost',
  user: 'root',
  password: '',
  database: 'monitor_bencana',
});

db.connect((err) => {
  if (err) {
    console.error('Gagal terhubung ke database:', err);
    return;
  }
  console.log('Terhubung ke database MySQL');
});

// Membuat WebSocket server
const wss = new WebSocket.Server({ port: 8080 });

// Koneksi ke broker MQTT
const client = mqtt.connect('ws://test.mosquitto.org:8080'); // gunakan port 9001 jika perlu

client.on('connect', () => {
  console.log('Terhubung ke broker MQTT');

  // Subscribe ke semua perangkat menggunakan wildcard
  client.subscribe('monitoring/perangkat/+', (err) => {
    if (err) {
      console.error('Gagal subscribe:', err);
    } else {
      console.log("Berhasil subscribe ke topik 'monitoring/perangkat/+'");
    }
  });
});

// Saat menerima pesan MQTT
client.on('message', (topic, message) => {
  const payload = message.toString();
  console.log(`Pesan diterima dari [${topic}]: ${payload}`);

  try {
    const data = JSON.parse(payload);

    // Ambil data terakhir untuk perangkat ini
    const cekSql = 'SELECT status_perangkat, aksi, waktu FROM riwayat_perangkat WHERE id_perangkat = ? ORDER BY waktu DESC LIMIT 1';
    db.query(cekSql, [data.id_perangkat], (err, results) => {
      if (err) {
        console.error('Gagal mengambil data terakhir:', err);
        return;
      }

      if (results.length === 0) {
        // Jika belum ada data, insert baru
        const insertSql = `INSERT INTO riwayat_perangkat (id_perangkat, status_perangkat, aksi, waktu) VALUES (?, ?, ?, ?)`;
        const values = [data.id_perangkat, data.status_perangkat, data.aksi, data.waktu];
        db.query(insertSql, values, (err) => {
          if (err) {
            console.error('Gagal menyimpan ke database:', err);
          } else {
            console.log(`Data baru disimpan untuk perangkat ${data.id_perangkat}`);
          }
        });
      } else {
        const lastData = results[0];

        if (lastData.status_perangkat === data.status_perangkat && lastData.aksi === data.aksi && lastData.waktu !== data.waktu) {
          // status & aksi sama, waktu beda → update waktu saja
          const updateSql = `UPDATE riwayat_perangkat SET waktu = ? WHERE id_perangkat = ? AND waktu = ?`;
          db.query(updateSql, [data.waktu, data.id_perangkat, lastData.waktu], (err) => {
            if (err) {
              console.error('Gagal update waktu:', err);
            } else {
              console.log(`Waktu diperbarui untuk perangkat ${data.id_perangkat}`);
            }
          });
        } else if (lastData.status_perangkat === data.status_perangkat && lastData.aksi !== data.aksi) {
          // status sama, aksi beda → update aksi & waktu
          const updateSql = `UPDATE riwayat_perangkat SET aksi = ?, waktu = ? WHERE id_perangkat = ? AND waktu = ?`;
          db.query(updateSql, [data.aksi, data.waktu, data.id_perangkat, lastData.waktu], (err) => {
            if (err) {
              console.error('Gagal update aksi & waktu:', err);
            } else {
              console.log(`Aksi & waktu diperbarui untuk perangkat ${data.id_perangkat}`);
            }
          });
        } else if (lastData.status_perangkat !== data.status_perangkat || lastData.aksi !== data.aksi || lastData.waktu !== data.waktu) {
          // Ada perubahan status, aksi, atau waktu → insert data baru
          const insertSql = `INSERT INTO riwayat_perangkat (id_perangkat, status_perangkat, aksi, waktu) VALUES (?, ?, ?, ?)`;
          const values = [data.id_perangkat, data.status_perangkat, data.aksi, data.waktu];
          db.query(insertSql, values, (err) => {
            if (err) {
              console.error('Gagal menyimpan ke database:', err);
            } else {
              console.log(`Data baru disimpan untuk perangkat ${data.id_perangkat}`);
            }
          });
        } else {
          // Tidak ada perubahan
          console.log(`Tidak ada perubahan untuk perangkat ${data.id_perangkat}`);
        }
      }

      // Kirim payload ke WebSocket client yang aktif
      wss.clients.forEach((wsClient) => {
        if (wsClient.readyState === WebSocket.OPEN) {
          wsClient.send(payload);
        }
      });
    });
  } catch (e) {
    console.error('Gagal parse payload JSON:', e);
  }
});
