<?php
// 1. Include the centralized connection file
include 'connect.php';

// Check if user is logged in (Consistency with index.php)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

// Process new history entry
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_history'])) {
    $asset_id     = $_POST['asset_id'];
    $old_location = $_POST['old_location'];
    $new_location = $_POST['new_location'];
    $changed_by   = $_SESSION['user_id']; // Automatically use the logged-in user's ID
    $change_date  = date('Y-m-d H:i:s');

    if ($old_location == $new_location) {
        $message = "Error: Old and new locations cannot be the same.";
    } else {
        // Insert into asset_history (Matches your ERD columns)
        $stmt = $conn->prepare("INSERT INTO asset_history (asset_id, old_location, new_location, changed_by, change_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiis", $asset_id, $old_location, $new_location, $changed_by, $change_date);
        
        if ($stmt->execute()) {
            // Success: Update the current location in the 'assets' table as well
            $update_asset = $conn->prepare("UPDATE assets SET location_id = ? WHERE asset_id = ?");
            $update_asset->bind_param("ii", $new_location, $asset_id);
            $update_asset->execute();
            
            $message = "Success: Asset movement recorded.";
        } else {
            $message = "Error: Could not save history.";
        }
    }
}

// Fetch Assets for dropdown
$assets_list = $conn->query("SELECT asset_id, asset_name, asset_tag FROM assets");
// Fetch Locations for dropdown
$locations_list = $conn->query("SELECT location_id, location_name FROM locations");

// Fetch History for the table with JOINs (Matches your ERD)
$sql = "SELECT h.*, a.asset_name, a.asset_tag, 
               l_old.location_name AS old_loc, 
               l_new.location_name AS new_loc, 
               u.full_name AS staff_name
        FROM asset_history h
        JOIN assets a ON h.asset_id = a.asset_id
        JOIN locations l_old ON h.old_location = l_old.location_id
        JOIN locations l_new ON h.new_location = l_new.location_id
        JOIN users u ON h.changed_by = u.user_id
        ORDER BY h.change_date DESC";
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
        .container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 3px 15px rgba(0,0,0,0.1); margin-bottom: 30px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; align-items: end; }
        label { display: block; font-weight: bold; margin-bottom: 5px; color: #555; }
        select, button { width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; }
        button { background: #007bff; color: white; border: none; cursor: pointer; font-weight: bold; }
        button:hover { background: #0056b3; }
        .alert { padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #f8f9fa; color: #333; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; }
        .bg-old { background: #e9ecef; color: #495057; }
        .bg-new { background: #d1e7dd; color: #0f5132; }
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
    <h2>Asset Movement Logs</h2>

    <?php if ($message): ?>
        <div class="alert"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="card">
        <h4 style="margin-top:0">Record New Transfer</h4>
        <form method="POST" action="assetshistory.php" class="form-grid">
            <div>
                <label>Asset Name</label>
                <select name="asset_id" required>
                    <option value="">-- Select Asset --</option>
                    <?php while($a = $assets_list->fetch_assoc()): ?>
                        <option value="<?= $a['asset_id'] ?>"><?= $a['asset_name'] ?> (<?= $a['asset_tag'] ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label>From Location</label>
                <select name="old_location" required>
                    <option value="">-- Select Origin --</option>
                    <?php 
                    $locations_list->data_seek(0); // Reset pointer
                    while($l = $locations_list->fetch_assoc()): 
                    ?>
                        <option value="<?= $l['location_id'] ?>"><?= $l['location_name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label>To Location</label>
                <select name="new_location" required>
                    <option value="">-- Select Destination --</option>
                    <?php 
                    $locations_list->data_seek(0); 
                    while($l = $locations_list->fetch_assoc()): 
                    ?>
                        <option value="<?= $l['location_id'] ?>"><?= $l['location_name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div style="grid-column: span 3; text-align: right;">
                <button type="submit" name="add_history" style="width: 200px;">Log Movement</button>
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
                    <th>Previous Loc</th>
                    <th>New Loc</th>
                    <th>Operator</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($history_result->num_rows > 0): ?>
                    <?php while($row = $history_result->fetch_assoc()): ?>
                        <tr>
                            <td><small><?= date('d M Y, H:i', strtotime($row['change_date'])) ?></small></td>
                            <td><code><?= $row['asset_tag'] ?></code></td>
                            <td><strong><?= $row['asset_name'] ?></strong></td>
                            <td><span class="badge bg-old"><?= $row['old_loc'] ?></span></td>
                            <td><span class="badge bg-new"><?= $row['new_loc'] ?></span></td>
                            <td><?= $row['staff_name'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center; padding:20px;">No transfer records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>