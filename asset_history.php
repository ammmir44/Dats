<?php
// 1. Include connection
include 'connect.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

// PROSES SIMPAN DATA (INSERT)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_history'])) {
    $asset_id      = $_POST['asset_id'];
    $new_location  = $_POST['new_location'];
    $history_note  = "Moved to location ID: " . $new_location;
    $change_date   = date('Y-m-d H:i:s');

    // Insert ikut table: id, asset_id, history_details, date_updated
    $stmt = $conn->prepare("INSERT INTO asset_history (asset_id, history_details, date_updated) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $asset_id, $history_note, $change_date);
    
    if ($stmt->execute()) {
        // Kemaskini lokasi semasa di table assets
        $update_asset = $conn->prepare("UPDATE assets SET location_id = ? WHERE asset_id = ?");
        $update_asset->bind_param("ii", $new_location, $asset_id);
        $update_asset->execute();
        
        $message = "Success: Movement recorded!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Ambil senarai Asset & Location untuk dropdown
$assets_list = $conn->query("SELECT asset_id, asset_name, asset_tag FROM assets");
$locations_list = $conn->query("SELECT location_id, location_name FROM locations");

// AMBIL DATA UNTUK PAPARAN TABLE (JOIN)
$sql = "SELECT h.*, a.asset_name, a.asset_tag 
        FROM asset_history h
        JOIN assets a ON h.asset_id = a.asset_id
        ORDER BY h.date_updated DESC";
$history_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Asset History - DATS</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f6f8; margin: 0; }
        header { background-color: #007bff; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        header h1 { margin: 0; font-size: 20px; }
        nav a { color: white; text-decoration: none; margin-left: 20px; font-weight: 600; }
        .container { max-width: 900px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 3px 15px rgba(0,0,0,0.1); margin-bottom: 30px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        select, button { width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; }
        button { background: #007bff; color: white; border: none; cursor: pointer; font-weight: bold; margin-top: 10px; }
        .alert { padding: 10px; border-radius: 5px; margin-bottom: 20px; background: #d4edda; color: #155724; text-align:center; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #f8f9fa; }
    </style>
</head>
<body>

<header>
    <h1>Digital Asset Tracking System</h1>
    <nav>
        <a href="index.php">Dashboard</a>
        <a href="assets.php">Assets</a>
        <a href="assetshistory.php">Asset History</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <h2>Asset History Logs</h2>

    <?php if ($message): ?>
        <div class="alert"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="POST" action="assetshistory.php" class="form-grid">
            <div>
                <label>Select Asset</label>
                <select name="asset_id" required>
                    <?php while($a = $assets_list->fetch_assoc()): ?>
                        <option value="<?= $a['asset_id'] ?>"><?= $a['asset_name'] ?> (<?= $a['asset_tag'] ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label>Move To Location</label>
                <select name="new_location" required>
                    <?php while($l = $locations_list->fetch_assoc()): ?>
                        <option value="<?= $l['location_id'] ?>"><?= $l['location_name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div style="grid-column: span 2;">
                <button type="submit" name="add_history">Update & Log Movement</button>
            </div>
        </form>
    </div>

    <div class="card" style="padding:0">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Asset Tag</th>
                    <th>Asset Name</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($history_result && $history_result->num_rows > 0): ?>
                    <?php while($row = $history_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= date('d M Y, H:i', strtotime($row['date_updated'])) ?></td>
                            <td><code><?= $row['asset_tag'] ?></code></td>
                            <td><?= $row['asset_name'] ?></td>
                            <td><?= $row['history_details'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align:center; padding:20px;">No records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>