<?php
include 'connect.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 1. PROSES TAMBAH LOKASI (Jika form dihantar)
if (isset($_POST['add_location'])) {
    $location_name = $conn->real_escape_string($_POST['location_name']);
    
    if (!empty($location_name)) {
        $sql = "INSERT INTO locations (location_name) VALUES ('$location_name')";
        if ($conn->query($sql)) {
            echo "<script>alert('Lokasi berjaya ditambah!'); window.location.href='locations.php';</script>";
        }
    }
}

// 2. QUERY UNTUK PAPAR LOKASI
$result = $conn->query("SELECT * FROM locations ORDER BY location_id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DATS | Manage Locations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; }
        .main-card { border: none; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .btn-emerald { background: #10b981; color: white; border: none; }
        .btn-emerald:hover { background: #059669; color: white; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold"><i class="bi bi-geo-alt-fill text-success"></i> Asset Locations</h2>
            <p class="text-muted">Urus lokasi penempatan aset syarikat di sini.</p>
        </div>
        <a href="homepage.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card main-card p-4">
                <h5 class="fw-bold mb-3">Tambah Lokasi Baru</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">NAMA LOKASI</label>
                        <input type="text" name="location_name" class="form-control" placeholder="Contoh: Aras 2, Bilik Server" required>
                    </div>
                    <button type="submit" name="add_location" class="btn btn-emerald w-100">Simpan Lokasi</button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card main-card p-4">
                <h5 class="fw-bold mb-3">Senarai Lokasi Sedia Ada</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nama Lokasi</th>
                                <th class="text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $row['location_id']; ?></td>
                                    <td><strong><?php echo $row['location_name']; ?></strong></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Tiada data lokasi dijumpai.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FIREBASE REALTIME SYNC -->
<script type="module">
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
import { getFirestore, collection, onSnapshot } 
  from "https://www.gstatic.com/firebasejs/10.7.1/firebase-firestore.js";

const firebaseConfig = {
  apiKey: "AIzaSyBJYhJiIoMD-S2xi40KVmPfkwUMyN8QTNY",
  authDomain: "assetinventory-3nv7fd.firebaseapp.com",
  projectId: "assetinventory-3nv7fd",
  storageBucket: "assetinventory-3nv7fd.firebasestorage.app",
  messagingSenderId: "341780673492",
  appId: "1:341780673492:web:3f810d7a79261f2b751c41"
};

const app = initializeApp(firebaseConfig);
const db = getFirestore(app);

// Listen location dari FlutterFlow
onSnapshot(collection(db, "locations"), (snapshot) => {
  snapshot.forEach((doc) => {
    console.log("Location from FlutterFlow:", doc.data().location_name);
  });
});
</script>


</body>
</html>