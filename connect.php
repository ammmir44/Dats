<?php
// Mulakan session supaya boleh guna $_SESSION di file lain
session_start();

// Set parameter connection
$servername = "localhost";
$username = "root";       // Default XAMPP username MySQL
$password = "";           // Default kosong untuk XAMPP local
$dbname = "dats_db";     // Ganti dengan nama database kamu

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
