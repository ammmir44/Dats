<?php
include 'connect.php'; 
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    $sql = "SELECT * FROM users WHERE email = '$email' AND role = '$role'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $password_betul = false;
        if (password_verify($password, $user['password'])) { $password_betul = true; }
        elseif ($password == $user['password']) { $password_betul = true; }

        if ($password_betul) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            echo "<script>alert('Login Berjaya!'); window.location.href='homepage.php';</script>";
            exit();
        } else { $error = "Password salah!"; }
    } else { $error = "Akaun tidak dijumpai!"; }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DATS | Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background: #f0f2f5; padding-top: 50px; font-family: sans-serif; }
        .login-card { 
            background: white; 
            border-radius: 15px; 
            padding: 30px; 
            width: 95%; 
            max-width: 400px; 
            margin: auto; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
            border-top: 8px solid #10b981;
        }
        .form-label { font-weight: bold; color: #444; margin-top: 10px; }
        .btn-emerald { background: #10b981; color: white; width: 100%; padding: 12px; border: none; border-radius: 8px; font-weight: bold; }
    </style>
</head>
<body>

<div class="login-card">
    <div class="text-center mb-4">
        <h2 class="fw-bold" style="color: #10b981;"><i class="bi bi-shield-lock-fill"></i> DATS LOGIN</h2>
        <p class="text-muted small">Digital Asset Tracking System</p>
    </div>

    <?php if($error): ?>
        <div class="alert alert-danger py-2 small text-center"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <label class="form-label text-uppercase small">Login As</label>
        <select name="role" class="form-select mb-3">
            <option value="user">Staff / User</option>
            <option value="admin">Administrator</option>
        </select>
        
        <label class="form-label text-uppercase small">Email Address</label>
        <input type="email" name="email" class="form-control mb-3" placeholder="Masukkan email" required>

        <label class="form-label text-uppercase small">Password</label>
        <input type="password" name="password" class="form-control mb-4" placeholder="Masukkan password" required>

        <button type="submit" class="btn btn-emerald">SIGN IN</button>
    </form>

    <div class="text-center mt-3">
        <a href="signup.php" class="text-decoration-none small fw-bold" style="color: #10b981;">Create New Account</a>
    </div>
</div>

</body>
</html>