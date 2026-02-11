<?php
// 1. Mesti include connect.php supaya PHP boleh akses database
include 'connect.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. QUERY LIVE - Kira jumlah baris dalam setiap table
$resAssets = $conn->query("SELECT COUNT(*) AS total FROM assets");
$totalAssets = ($resAssets) ? $resAssets->fetch_assoc()['total'] : 0;

$resDept = $conn->query("SELECT COUNT(*) AS total FROM departments");
$totalDept = ($resDept) ? $resDept->fetch_assoc()['total'] : 0;

$resLoc = $conn->query("SELECT COUNT(*) AS total FROM locations");
$totalLoc = ($resLoc) ? $resLoc->fetch_assoc()['total'] : 0;

$resMaint = $conn->query("SELECT COUNT(*) AS total FROM maintenance WHERE status != 'Completed'");
$totalMaint = ($resMaint) ? $resMaint->fetch_assoc()['total'] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DATS | Live Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .stat-card {
            border: none;
            border-radius: 15px;
            padding: 25px;
            color: white;
            transition: 0.3s;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .stat-card:hover { transform: translateY(-10px); }
        .bg-gradient-primary { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); }
        .bg-gradient-success { background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%); }
        .bg-gradient-warning { background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%); }
        .bg-gradient-danger { background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%); }
        .icon-bg { font-size: 3rem; opacity: 0.3; position: absolute; right: 20px; bottom: 10px; }
        .profile-header { background: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="container py-5">
    
    <div class="profile-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0 fw-bold text-dark">Digital Asset Tracking System</h5>
        </div>
        <div class="d-flex align-items-center">
            <div class="text-end me-3">
                <p class="mb-0 fw-bold"><?php echo $_SESSION['full_name']; ?></p>
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <span class="badge bg-danger rounded-pill" style="font-size: 10px;">ADMINISTRATOR</span>
                <?php else: ?>
                    <span class="badge bg-primary rounded-pill" style="font-size: 10px;">STAFF</span>
                <?php endif; ?>
            </div>
            <i class="bi bi-person-circle fs-1 text-secondary"></i>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold text-dark">System Overview</h2>
            <p class="text-muted">Data ini dikemaskini secara automatik dari setiap modul.</p>
        </div>
    </div>

    <div class="row g-4 text-center">
        <div class="col-md-3">
            <div class="card stat-card bg-gradient-primary position-relative">
                <h6 class="text-uppercase small fw-bold">Total Assets</h6>
                <h2 class="display-4 fw-bold"><?php echo $totalAssets; ?></h2>
                <i class="bi bi-laptop icon-bg"></i>
                <a href="assets.php" class="text-white text-decoration-none small mt-2 d-inline-block">Manage Assets →</a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card bg-gradient-success position-relative">
                <h6 class="text-uppercase small fw-bold">Departments</h6>
                <h2 class="display-4 fw-bold"><?php echo $totalDept; ?></h2>
                <i class="bi bi-building icon-bg"></i>
                <a href="departments.php" class="text-white text-decoration-none small mt-2 d-inline-block">View Dept →</a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card bg-gradient-warning position-relative">
                <h6 class="text-uppercase small fw-bold">Locations</h6>
                <h2 class="display-4 fw-bold"><?php echo $totalLoc; ?></h2>
                <i class="bi bi-geo-alt icon-bg"></i>
                <a href="locations.php" class="text-white text-decoration-none small mt-2 d-inline-block">Explore Areas →</a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card bg-gradient-danger position-relative">
                <h6 class="text-uppercase small fw-bold">Active Repairs</h6>
                <h2 class="display-4 fw-bold"><?php echo $totalMaint; ?></h2>
                <i class="bi bi-tools icon-bg"></i>
                <a href="maintenance.php" class="text-white text-decoration-none small mt-2 d-inline-block">Fix Issues →</a>
            </div>
        </div>
    </div>

    <div class="mt-5 p-4 bg-white rounded shadow-sm border-start border-primary border-5">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold">Tips Penyelenggaraan</h5>
                <p class="mb-0 text-muted">Sebaik sahaja abang tambah data di mana-mana page, dashboard akan berubah serta-merta.</p>
            </div>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Sign Out</a>
        </div>
    </div>
</div>

</body>
</html>