<?php
header("Content-Type: application/json");

// Get the POST data
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['device']) || !isset($data['status'])) {
    echo json_encode(["message" => "Invalid request"]);
    exit;
}

$device = $data['device'];
$status = $data['status'] ? "ON" : "OFF";

// Simulate device control logic
$response = [
    "message" => "Device '$device' has been turned $status successfully!",
];

echo json_encode($response);
