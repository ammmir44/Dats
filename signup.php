<?php
include 'connect.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($check->num_rows > 0) {
        $message = "Email already registered!";
    } else {
        $sql = "INSERT INTO users (full_name, email, password, role) VALUES ('$full_name', '$email', '$password', 'user')";
        if ($conn->query($sql)) {
            $message = "Registration successful! <a href='login.php'>Login here</a>.";
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Sign Up - Digital Asset Tracking System</title>
<style>
    body {
        background: #f0f2f5;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }
    .container {
        background: white;
        padding: 30px 40px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        width: 350px;
        text-align: center;
    }
    h2 {
        margin-bottom: 25px;
        color: #333;
    }
    input[type="text"], input[type="email"], input[type="password"] {
        width: 100%;
        padding: 10px 12px;
        margin: 8px 0 18px 0;
        border: 1.5px solid #ddd;
        border-radius: 6px;
        box-sizing: border-box;
        font-size: 14px;
    }
    button {
        width: 100%;
        background-color: #007bff;
        color: white;
        padding: 12px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s ease;
    }
    button:hover {
        background-color: #0056b3;
    }
    p.message {
        color: #e74c3c;
        font-weight: 600;
    }
    p.success {
        color: #27ae60;
    }
    .link {
        margin-top: 15px;
        font-size: 14px;
    }
    .link a {
        color: #007bff;
        text-decoration: none;
    }
    .link a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>
<div class="container">
    <h2>Sign Up</h2>
    <?php
    if($message) {
        $class = strpos($message, 'successful') !== false ? 'success' : 'message';
        echo "<p class='$class'>$message</p>";
    }
    ?>
    <form method="POST" action="">
        <input type="text" name="full_name" placeholder="Full Name" required />
        <input type="email" name="email" placeholder="Email Address" required />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit">Sign Up</button>
    </form>
    <p class="link">Already have an account? <a href="login.php">Login here</a></p>
</div>
</body>
</html>
