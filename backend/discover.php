<?php
header('Content-Type: application/json');

require 'vendor/autoload.php'; // Autoload dependencies for Matter SDK

// Step 1: Get POST data
$input = json_decode(file_get_contents('php://input'), true);
$setupCode = $input['setupCode'] ?? null;
$command = $input['command'] ?? null; // 'on' or 'off'

if (!$setupCode) {
    echo json_encode(['error' => 'Setup code is missing.']);
    exit;
}

// Step 2: Discover the Matter device using the setup code
// Example using Matter SDK CLI commands. Replace this with real SDK implementation.
$deviceId = shell_exec("matter-cli setup-code $setupCode");

if (!$deviceId) {
    echo json_encode(['error' => 'Device not found or invalid setup code.']);
    exit;
}

// Step 3: Control the Matter device (Turn ON/OFF)
if ($command) {
    $controlResult = shell_exec("matter-cli device $deviceId $command");

    if ($controlResult) {
        echo json_encode(['success' => true, 'message' => "Device turned $command."]);
    } else {
        echo json_encode(['error' => 'Failed to control the device.']);
    }
    exit;
}

// Default Response: Return device info
echo json_encode(['success' => true, 'deviceId' => $deviceId]);
