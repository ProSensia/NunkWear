let pairedDevice = null;
let deviceStatus = false;

// QR Code Scanner Setup
function openQrScanner() {
  const video = document.getElementById("qrScanner");
  video.style.display = "block";

  const qrScanner = new QrScanner(
    video,
    (result) => {
      document.getElementById("qrResult").innerText = `QR Code: ${result}`;
      pairedDevice = result;
      document.getElementById("qrConnectButton").style.display = "inline-block";
      qrScanner.stop(); // Stop the scanner
    },
    { highlightScanRegion: true }
  );
  qrScanner.start();
}

// Device Connection
function connectDevice(method) {
  if (method === "qr" && pairedDevice) {
    alert(`Device ${pairedDevice} connected via QR code!`);
  } else if (method === "manual") {
    pairedDevice = document.getElementById("manualCode").value;
    if (!pairedDevice) {
      alert("Please enter a device code!");
      return;
    }
    alert(`Device ${pairedDevice} connected via manual code!`);
  }

  document.getElementById("controlSection").style.display = "block";
  document.getElementById("pairedDevice").innerText = `Paired Device: ${pairedDevice}`;
}

// Toggle Device
function toggleDevice() {
  deviceStatus = !deviceStatus;

  // Send request to backend
  fetch("backend.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ device: pairedDevice, status: deviceStatus }),
  })
    .then((response) => response.json())
    .then((data) => {
      alert(data.message);
      document.getElementById("deviceStatus").innerText = `Device is ${deviceStatus ? "ON" : "OFF"}`;
    });
}
