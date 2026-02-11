<?php

include 'connect.php';

// Pastikan user dah login seperti di index.php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

// 2. Proses Tambah Department Baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_dept'])) {
    $dept_name = $conn->real_escape_string($_POST['dept_name']);
    
    // Semak jika nama jabatan sudah wujud
    $check = $conn->query("SELECT * FROM departments WHERE dept_name='$dept_name'");
    if ($check->num_rows > 0) {
        $message = "Department already exists!";
    } else {
        $sql = "INSERT INTO departments (dept_name) VALUES ('$dept_name')";
        if ($conn->query($sql)) {
            $message = "Department added successfully!";
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}

// 3. Ambil senarai department
$depts = $conn->query("SELECT * FROM departments ORDER BY dept_id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Departments - DATS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f6f8; font-family: 'Segoe UI', sans-serif; }
        .navbar { background-color: #007bff; }
        .dept-card { transition: transform 0.2s; border: none; border-radius: 12px; }
        .dept-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .icon-box { font-size: 2.5rem; color: #007bff; margin-bottom: 15px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">DATS SYSTEM</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="index.php">Dashboard</a>
            <a class="nav-link active" href="departments.php">Departments</a>
            <a class="nav-link" href="assets.php">Assets</a>
            <a class="nav-link" href="logout.php text-danger">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-4 mb-4">
                <h4 class="fw-bold mb-3"><i class="bi bi-plus-circle-fill me-2"></i>Add New</h4>
                <?php if($message) echo "<div class='alert alert-info'>$message</div>"; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Department Name</label>
                        <input type="text" name="dept_name" class="form-control" placeholder="e.g. IT Department" required>
                    </div>
                    <button type="submit" name="add_dept" class="btn btn-primary w-100">Save Department</button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <h4 class="fw-bold mb-4">Department List</h4>
            <div class="row">
                <?php if ($depts->num_rows > 0): ?>
                    <?php while($row = $depts->fetch_assoc()): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card dept-card shadow-sm text-center p-4">
                                <div class="icon-box">
                                    <i class="bi bi-building"></i>
                                </div>
                                <h5 class="fw-bold"><?php echo htmlspecialchars($row['dept_name']); ?></h5>
                                <p class="text-muted mb-0">ID: #<?php echo $row['dept_id']; ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center text-muted py-5">
                        <i class="bi bi-inbox fs-1"></i>
                        <p>No departments found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>