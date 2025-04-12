<?php
header('Content-Type: application/json');

require 'vendor/autoload.php'; // If needed by your Matter CLI PHP wrapper

// Hardcoded setup code
$setupCode = '04174023181';
$input = json_decode(file_get_contents('php://input'), true);
$command = $input['command'] ?? null;

// Discover the Matter device using the known setup code
$deviceId = trim(shell_exec("matter-cli setup-code $setupCode")); // Trim to remove newline

if (!$deviceId) {
    echo json_encode(['error' => 'Device not found using the setup code.']);
    exit;
}

if ($command) {
    $controlResult = shell_exec("matter-cli device $deviceId $command");

    if ($controlResult) {
        echo json_encode(['success' => true, 'message' => "Device turned $command."]);
    } else {
        echo json_encode(['error' => 'Failed to control the device.']);
    }
    exit;
}

// If no command, return device info
echo json_encode(['success' => true, 'deviceId' => $deviceId]);
