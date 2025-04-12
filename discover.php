<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matter & Zigbee Device Control</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsQR/1.4.0/jsQR.min.js"></script>
    <style>
        body {
            padding: 20px;
        }
        #qr-video {
            width: 100%;
            max-width: 400px;
            margin: 20px auto;
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Matter & Zigbee Device Control Panel</h1>

        <div class="mt-4">
            <h3>Scan QR Code</h3>
            <video id="qr-video"></video>
            <button id="start-scan" class="btn btn-primary">Start Scanning</button>
            <p id="qr-result" class="text-success mt-3"></p>
        </div>

        <div class="mt-4">
            <h3>Or Enter Setup Code</h3>
            <input type="text" id="setup-code" class="form-control" placeholder="Enter Device Setup Code">
            <button id="submit-code" class="btn btn-success mt-2">Submit</button>
        </div>

        <div class="mt-4">
            <h3>Discovered Devices</h3>
            <ul id="device-list" class="list-group">
                <li class="list-group-item">No devices found yet.</li>
            </ul>
        </div>
    </div>

    <script>
        const video = document.getElementById('qr-video');
        const qrResult = document.getElementById('qr-result');
        const startScanButton = document.getElementById('start-scan');
        const submitCodeButton = document.getElementById('submit-code');
        const setupCodeInput = document.getElementById('setup-code');
        const deviceList = document.getElementById('device-list');

        let scanning = false;
        let videoStream;

        async function startQRScan() {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert('Camera not supported on this browser.');
                return;
            }

            videoStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
            video.srcObject = videoStream;
            video.setAttribute('playsinline', true);
            video.play();
            scanning = true;

            scanLoop();
        }

        function stopQRScan() {
            if (videoStream) {
                videoStream.getTracks().forEach(track => track.stop());
            }
            scanning = false;
        }

        async function scanLoop() {
            if (!scanning) return;

            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
            const qrCode = jsQR(imageData.data, canvas.width, canvas.height);

            if (qrCode) {
                qrResult.textContent = `QR Code Detected: ${qrCode.data}`;
                stopQRScan();
                discoverDevice(qrCode.data);
            } else {
                requestAnimationFrame(scanLoop);
            }
        }

        async function discoverDevice(code) {
            const response = await fetch('backend/discover.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ setupCode: code })
            });

            const devices = await response.json();
            displayDevices(devices);
        }

        function displayDevices(devices) {
            deviceList.innerHTML = '';

            if (devices.length === 0) {
                deviceList.innerHTML = '<li class="list-group-item">No devices found.</li>';
                return;
            }

            devices.forEach(device => {
                const listItem = document.createElement('li');
                listItem.className = 'list-group-item';
                listItem.textContent = `Device: ${device.name} - Status: ${device.status}`;
                deviceList.appendChild(listItem);
            });
        }

        startScanButton.addEventListener('click', startQRScan);

        submitCodeButton.addEventListener('click', () => {
            const code = setupCodeInput.value;
            if (code) {
                discoverDevice(code);
            } else {
                alert('Please enter a setup code.');
            }
        });
    </script>
</body>
</html>
