<?php
header("Content-Type: application/json");

// Simulate database or logic
$devices = [
    ["name" => "Light Bulb", "status" => "Online"],
    ["name" => "Thermostat", "status" => "Offline"]
];

// Read incoming POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (isset($data['setupCode'])) {
    echo json_encode($devices); // Respond with dummy devices
} else {
    echo json_encode(["error" => "Invalid request"]);
}
?>
