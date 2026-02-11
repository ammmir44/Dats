<?php
include 'connect.php';

$message = "";
$edit_asset = null;

// Fungsi generate dropdown options dengan selected value
function getOptions($conn, $table, $id_field, $name_field, $selected_id = null) {
    $options = "";
    $sql = "SELECT $id_field, $name_field FROM $table ORDER BY $name_field ASC";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $selected = ($selected_id == $row[$id_field]) ? "selected" : "";
            $options .= "<option value='".$row[$id_field]."' $selected>".htmlspecialchars($row[$name_field])."</option>";
        }
    }
    return $options;
}

// Proses tambah / update asset
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $asset_name = $_POST['asset_name'];
    $asset_tag = $_POST['asset_tag'];
    $asset_type = $_POST['asset_type'];
    $asset_status = $_POST['asset_status'];
    $purchase_date = $_POST['purchase_date'];
    $dept_id = $_POST['dept_id'];
    $location_id = $_POST['location_id'];
    $assigned_to = $_POST['assigned_to'];

    if (!empty($_POST['asset_id'])) {
        // Update asset
        $asset_id = $_POST['asset_id'];
        $stmt = $conn->prepare("UPDATE assets SET asset_name=?, asset_tag=?, asset_type=?, asset_status=?, purchase_date=?, dept_id=?, location_id=?, assigned_to=? WHERE asset_id=?");
        $stmt->bind_param("sssssiisi", $asset_name, $asset_tag, $asset_type, $asset_status, $purchase_date, $dept_id, $location_id, $assigned_to, $asset_id);
        $stmt->execute();
        $message = "Asset updated successfully.";
    } else {
        // Insert asset baru
        $stmt = $conn->prepare("INSERT INTO assets (asset_name, asset_tag, asset_type, asset_status, purchase_date, dept_id, location_id, assigned_to) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssiis", $asset_name, $asset_tag, $asset_type, $asset_status, $purchase_date, $dept_id, $location_id, $assigned_to);
        $stmt->execute();
        $message = "Asset added successfully.";
    }
}

// Ambil data asset untuk edit
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM assets WHERE asset_id=$id");
    if ($result && $result->num_rows > 0) {
        $edit_asset = $result->fetch_assoc();
    }
}

// Delete asset
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM assets WHERE asset_id=$id");
    header("Location: assets.php");
    exit();
}

// Ambil semua data asset dengan join ke department, location, user
$sql = "SELECT a.*, d.dept_name, l.location_name, u.full_name AS assigned_full_name
        FROM assets a
        LEFT JOIN users u ON a.assigned_to = u.user_id
        LEFT JOIN departments d ON a.dept_id = d.dept_id
        LEFT JOIN locations l ON a.location_id = l.location_id
        ORDER BY a.asset_id DESC";

$assets_result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Assets Management - DATS</title>
<style>
/* Gaya sederhana dan bersih */
body { font-family: Arial, sans-serif; background: #f9faff; margin: 20px auto; max-width: 900px; color: #333; }
h1 { text-align: center; color: #123456; }
form { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 0 8px #ccc; margin-bottom: 40px; }
label { font-weight: bold; margin-top: 15px; display: block; }
input, select { width: 100%; padding: 10px; margin-top: 5px; border-radius: 6px; border: 1px solid #ccc; box-sizing: border-box; }
button { background-color: #123456; color: white; border: none; padding: 12px 20px; border-radius: 8px; margin-top: 20px; cursor: pointer; font-weight: bold; }
button:hover { background-color: #0a234a; }
.message { color: green; font-weight: bold; text-align: center; margin-bottom: 20px; }
table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 0 8px #ccc; border-radius: 10px; overflow: hidden; }
th, td { padding: 12px 15px; border-bottom: 1px solid #ddd; text-align: left; }
th { background-color: #123456; color: white; }
tr:nth-child(even) { background-color: #f1f5ff; }
tr:hover { background-color: #d9e1ff; }
a { color: #123456; font-weight: bold; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
</head>
<body>

<h1>Manage Assets</h1>

<?php if($message): ?>
    <div class="message"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<form method="POST" action="assets.php">
    <input type="hidden" name="asset_id" value="<?php echo $edit_asset ? $edit_asset['asset_id'] : ''; ?>" />
    
    <label>Asset Name</label>
    <input type="text" name="asset_name" required value="<?php echo $edit_asset ? htmlspecialchars($edit_asset['asset_name']) : ''; ?>" />
    
    <label>Asset Tag</label>
    <input type="text" name="asset_tag" required value="<?php echo $edit_asset ? htmlspecialchars($edit_asset['asset_tag']) : ''; ?>" />
    
    <label>Asset Type</label>
    <input type="text" name="asset_type" required value="<?php echo $edit_asset ? htmlspecialchars($edit_asset['asset_type']) : ''; ?>" />
    
    <label>Asset Status</label>
    <select name="asset_status" required>
        <?php
        $statuses = ['Active', 'Under Maintenance', 'Retired'];
        $current_status = $edit_asset ? $edit_asset['asset_status'] : '';
        foreach ($statuses as $status) {
            $sel = ($current_status == $status) ? "selected" : "";
            echo "<option value='$status' $sel>$status</option>";
        }
        ?>
    </select>
    
    <label>Purchase Date</label>
    <input type="date" name="purchase_date" required value="<?php echo $edit_asset ? $edit_asset['purchase_date'] : ''; ?>" />
    
    <label>Department</label>
    <select name="dept_id" required>
        <option value="">-- Select Department --</option>
        <?php echo getOptions($conn, 'departments', 'dept_id', 'dept_name', $edit_asset ? $edit_asset['dept_id'] : null); ?>
    </select>
    
    <label>Location</label>
    <select name="location_id" required>
        <option value="">-- Select Location --</option>
        <?php echo getOptions($conn, 'locations', 'location_id', 'location_name', $edit_asset ? $edit_asset['location_id'] : null); ?>
    </select>
    
    <label>Assigned To</label>
    <select name="assigned_to" required>
        <option value="">-- Select User --</option>
        <?php echo getOptions($conn, 'users', 'user_id', 'full_name', $edit_asset ? $edit_asset['assigned_to'] : null); ?>
    </select>
    
    <button type="submit"><?php echo $edit_asset ? "Update Asset" : "Add Asset"; ?></button>
    <?php if($edit_asset): ?>
        <a href="assets.php" style="margin-left: 15px;">Cancel</a>
    <?php endif; ?>
</form>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Asset Name</th>
            <th>Tag</th>
            <th>Type</th>
            <th>Status</th>
            <th>Purchase Date</th>
            <th>Department</th>
            <th>Location</th>
            <th>Assigned To</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($assets_result && $assets_result->num_rows > 0): ?>
            <?php while($row = $assets_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['asset_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['asset_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['asset_tag']); ?></td>
                    <td><?php echo htmlspecialchars($row['asset_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['asset_status']); ?></td>
                    <td><?php echo $row['purchase_date']; ?></td>
                    <td><?php echo htmlspecialchars($row['dept_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['location_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['assigned_full_name']); ?></td>
                    <td>
                        <a href="assets.php?edit=<?php echo $row['asset_id']; ?>">Edit</a> | 
                        <a href="assets.php?delete=<?php echo $row['asset_id']; ?>" onclick="return confirm('Are you sure to delete this asset?');">Delete</a>
                    </td>
                </tr>

            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="10" style="text-align:center;">No assets found</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
