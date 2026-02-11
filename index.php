<?php
session_start();

// Pastikan user dah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Dashboard - Digital Asset Tracking System</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f4f6f8;
        margin: 0; padding: 0;
    }
    header {
        background-color: #007bff;
        color: white;
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    header h1 {
        margin: 0;
        font-size: 24px;
    }
    nav a {
        color: white;
        text-decoration: none;
        margin-left: 20px;
        font-weight: 600;
        font-size: 16px;
    }
    nav a:hover {
        text-decoration: underline;
    }
    .container {
        max-width: 1000px;
        margin: 30px auto;
        background: white;
        padding: 20px 30px;
        border-radius: 10px;
        box-shadow: 0 3px 15px rgba(0,0,0,0.1);
    }
    h2 {
        color: #333;
    }
    canvas {
        max-width: 100%;
        height: 400px;
    }
</style>
</head>
<body>

<header>
    <h1>Digital Asset Tracking System</h1>
    <nav>
        <a href="index.php">Dashboard</a>
        <a href="assets.php">Assets</a>
        <a href="departments.php">Department</a>
        <a href="locations.php">Location</a>
        <a href="maintenance.php">maintenance</a>
        <a href="logout.php">Logout</a>
		
    </nav>
</header>

<div class="container">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></h2>
    <p>This is your dashboard.</p>
    <canvas id="assetChart"></canvas>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('assetChart').getContext('2d');
const assetChart = new Chart(ctx, {
    type: 'bar',  
    data: {
        labels: ['Laptops', 'Printers', 'Monitors', 'Network Equipment', 'Others'],
        datasets: [{
            label: 'Number of Assets',
            data: [12, 7, 15, 5, 3],
            backgroundColor: [
                'rgba(54, 162, 235, 0.7)',
                'rgba(255, 99, 132, 0.7)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(75, 192, 192, 0.7)',
                'rgba(153, 102, 255, 0.7)'
            ],
            borderColor: [
                'rgba(54, 162, 235, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                stepSize: 1
            }
        }
    }
});
</script>

</body>
</html>

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

const ctx = document.getElementById('assetChart').getContext('2d');

let assetChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: [],
    datasets: [{
      label: 'Number of Assets',
      data: [],
      backgroundColor: 'rgba(54, 162, 235, 0.7)'
    }]
  }
});

// REALTIME FROM FIREBASE
onSnapshot(collection(db, "assets"), (snapshot) => {
  let counts = {
    Laptop: 0,
    Printer: 0,
    Monitor: 0,
    Network: 0,
    Others: 0
  };

  snapshot.forEach(doc => {
    const type = doc.data().asset_type;
    if(counts[type] !== undefined){
      counts[type]++;
    } else {
      counts.Others++;
    }
  });

  assetChart.data.labels = Object.keys(counts);
  assetChart.data.datasets[0].data = Object.values(counts);
  assetChart.update();
});
</script>
