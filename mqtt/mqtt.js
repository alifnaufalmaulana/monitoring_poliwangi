const mqtt = require('mqtt');
const mysql = require('mysql');
const client = mqtt.connect('mqtt://localhost'); // broker lokal Mosquitto

// Koneksi database MySQL
const db = mysql.createConnection({
  host: 'localhost',
  user: 'root',
  password: '',
  database: 'monitor_bencana', // Ganti sesuai database kamu
});

db.connect((err) => {
  if (err) throw err;
  console.log('Terhubung ke database MySQL!');
});

client.on('connect', () => {
  console.log('Terhubung ke broker MQTT.');
  client.subscribe('perangkat/kebencanaan', (err) => {
    if (err) console.error('Gagal subscribe:', err);
    else console.log("Berhasil subscribe ke topik 'perangkat/kebencanaan'");
  });
});

client.on('message', (topic, message) => {
  try {
    const data = JSON.parse(message.toString());

    const id_perangkat = data.id_perangkat;
    const status = data.status;
    const aksi = data.aksi;
    const waktu = data.waktu;

    const sql = 'INSERT INTO riwayat_perangkat (id_perangkat, status_perangkat, aksi, waktu) VALUES (?, ?, ?, ?)';
    db.query(sql, [id_perangkat, status, aksi, waktu], (err, result) => {
      if (err) {
        console.error('Gagal insert ke database:', err);
      } else {
        console.log('Data berhasil disimpan:', data);
      }
    });
  } catch (err) {
    console.error('Format data tidak valid:', err.message);
  }
});
