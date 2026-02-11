<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Penting untuk FlutterFlow Web
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'connect.php';

$method = $_SERVER['REQUEST_METHOD'];

// --- MENGAMBIL DATA (GET) ---
if ($method == 'GET') {
    $sql = "SELECT a.*, d.dept_name, l.location_name, u.full_name AS assigned_full_name
            FROM assets a
            LEFT JOIN users u ON a.assigned_to = u.user_id
            LEFT JOIN departments d ON a.dept_id = d.dept_id
            LEFT JOIN locations l ON a.location_id = l.location_id
            ORDER BY a.asset_id DESC";

    $result = $conn->query($sql);
    $assets = [];

    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $assets[] = $row;
        }
    }
    echo json_encode($assets);
}

// --- MENAMBAH DATA (POST) ---
if ($method == 'POST') {
    // Mengambil data dari JSON body (FlutterFlow mengirimkan JSON)
    $data = json_decode(file_get_contents("php://input"), true);

    if (!empty($data)) {
        $stmt = $conn->prepare("INSERT INTO assets (asset_name, asset_tag, asset_type, asset_status, purchase_date, dept_id, location_id, assigned_to) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssiis", 
            $data['asset_name'], 
            $data['asset_tag'], 
            $data['asset_type'], 
            $data['asset_status'], 
            $data['purchase_date'], 
            $data['dept_id'], 
            $data['location_id'], 
            $data['assigned_to']
        );

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Asset added successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => $stmt->error]);
        }
    }
}
?>