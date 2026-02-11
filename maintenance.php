<?php
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

// Proses Tambah Rekod Maintenance
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_maintenance'])) {
    $asset_id = $_POST['asset_id'];
    $issue = $conn->real_escape_string($_POST['issue_description']);
    $m_date = $_POST['maintenance_date'];
    $status = $_POST['status'];

    $sql = "INSERT INTO maintenance (asset_id, issue_description, maintenance_date, status) 
            VALUES ('$asset_id', '$issue', '$m_date', '$status')";
    
    if ($conn->query($sql)) {
        $message = "Maintenance record added!";
    }
}

// Ambil data untuk paparan
$assets = $conn->query("SELECT asset_id, asset_name FROM assets");
$records = $conn->query("SELECT m.*, a.asset_name FROM maintenance m JOIN assets a ON m.asset_id = a.asset_id ORDER BY m.maintenance_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Maintenance - DATS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f8; padding: 20px; }
        .status-pending { color: orange; font-weight: bold; }
        .status-completed { color: green; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="bi bi-tools"></i> Asset Maintenance</h2>
        <a href="index.php" class="btn btn-secondary">Back</a>
    </div>

    <?php if($message) echo "<div class='alert alert-success'>$message</div>"; ?>

    <div class="card p-4 mb-4 shadow-sm border-0">
        <form method="POST" class="row g-3">
            <div class="col-md-4">
                <label>Select Asset</label>
                <select name="asset_id" class="form-control" required>
                    <?php while($row = $assets->fetch_assoc()): ?>
                        <option value="<?= $row['asset_id'] ?>"><?= $row['asset_name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label>Date</label>
                <input type="date" name="maintenance_date" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="Pending">Pending</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>
            <div class="col-md-12">
                <label>Issue Description</label>
                <textarea name="issue_description" class="form-control" rows="2" placeholder="Describe the problem..."></textarea>
            </div>
            <div class="col-md-12">
                <button type="submit" name="add_maintenance" class="btn btn-primary">Save Record</button>
            </div>
        </form>
    </div>

    <div class="card shadow-sm border-0">
        <table class="table mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Asset</th>
                    <th>Issue</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $records->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['asset_name'] ?></td>
                    <td><?= $row['issue_description'] ?></td>
                    <td><?= $row['maintenance_date'] ?></td>
                    <td><span class="status-<?= strtolower(str_replace(' ', '', $row['status'])) ?>"><?= $row['status'] ?></span></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>