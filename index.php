<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: home.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>QR Login & Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1f4037, #99f2c8);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .form-control, .btn {
            border-radius: 8px;
        }

        #qr_image {
            display: none;
            width: 150px;
            margin-top: 15px;
        }

        video {
            width: 100%;
            border-radius: 10px;
            margin-top: 10px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="card p-4 text-dark" style="max-width: 500px; margin: auto; background: #fff;">
            <h3 class="text-center mb-4">QR Code Login & Registration</h3>

            <ul class="nav nav-tabs mb-3" id="myTab">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#loginTab">Login</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#registerTab">Register</button></li>
            </ul>

            <div class="tab-content">
                <!-- Login Tab -->
                <div class="tab-pane fade show active" id="loginTab">
                    <form action="./endpoint/login.php" method="POST">
                        <div class="mb-3">
                            <label>Enter Code Manually</label>
                            <input type="text" name="qr-code" class="form-control" placeholder="Your QR Code" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100 mb-3">Login</button>
                    </form>

                    <h5 class="text-center mb-2">Or scan QR Code</h5>
                    <video id="preview"></video>
                </div>

                <!-- Register Tab -->
                <div class="tab-pane fade" id="registerTab">
                    <form action="./endpoint/add-user.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <input type="text" name="name" class="form-control" placeholder="Full Name" required>
                        </div>
                        <div class="mb-3">
                            <input type="text" name="contact_number" class="form-control" placeholder="Contact Number" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                        </div>
                        <div class="mb-3">
                            <input type="file" name="image" class="form-control" accept="image/*" required>
                        </div>
                        <div class="mb-3">
                            <input type="date" name="dob" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <input type="text" name="level" class="form-control" placeholder="Level (e.g., 200)" required>
                        </div>
                        <div class="mb-3">
                            <input type="text" name="session" class="form-control" placeholder="Session (e.g., 2024/2025)" required>
                        </div>
                        <div class="mb-3">
                            <input type="text" name="department" class="form-control" placeholder="Department" required>
                        </div>

                        <input type="hidden" name="generated_code" id="generated_code">
                        <div class="text-center">
                            <img id="qr_image" src="">
                        </div>

                        <button type="button" onclick="generateQr()" class="btn btn-warning w-100 mb-2">Generate QR Code</button>
                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>

    <script>
        // QR Scanner Setup
        let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
        scanner.addListener('scan', function(content) {
            window.location.href = "./endpoint/login.php?qr-code=" + encodeURIComponent(content);
        });

        Instascan.Camera.getCameras().then(function(cameras) {
            if (cameras.length > 0) {
                scanner.start(cameras[0]);
            } else {
                alert('No camera found');
            }
        });

        // QR Code Generator + Alert
        function generateRandomCode(length = 10) {
            let chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            let code = "";
            for (let i = 0; i < length; i++) {
                code += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            return code;
        }

        function generateQr() {
            let code = generateRandomCode();
            document.getElementById('generated_code').value = code;

            // Display QR Image
            let img = document.getElementById('qr_image');
            img.src = `https://api.qrserver.com/v1/create-qr-code/?data=${encodeURIComponent(code)}&size=150x150`;
            img.style.display = "block";

            // Show Alert with Code
            alert("Your unique generated ID is: " + code + "\nPlease save it in case your device doesn't support QR login.");
        }
    </script>

</body>

</html>
