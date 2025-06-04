<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login System with QR Code Scanner</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap');

        * {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: url("https://images.unsplash.com/photo-1507608158173-1dcec673a2e5?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D");
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            height: 100vh;
        }

        .login-container,
        .registration-container {
            backdrop-filter: blur(120px);
            color: rgb(167, 9, 9);
            padding: 25px 40px;
            width: 500px;
            border: 2px solid;
            border-radius: 10px;
        }

        .switch-form-link {
            text-decoration: underline;
            cursor: pointer;
            color: rgb(100, 100, 250);
        }

        .drawingBuffer {
            width: 0;
            padding: 0;
        }
    </style>
</head>

<body>
    <!-- Main Area -->
    <div class="main">

        <!-- Login Area -->
        <div class="login-container">
            <div class="login-form" id="loginForm">
                <h2 class="text-center">Welcome Back!</h2>
                <p class="text-center">Login with your QR code or manually input your code.</p>

                <!-- QR Scanner Video -->
                <video id="interactive" class="viewport" width="415" style="display: block;"></video>

                <!-- QR Detected Form -->
                <div class="qr-detected-container" style="display: none;">
                    <form id="qrLoginForm" action="./endpoint/login.php" method="POST">
                        <h4 class="text-center">QR Code Detected!</h4>
                        <input type="hidden" id="detected-qr-code" name="qr-code">
                        <button type="button" class="btn btn-dark form-control" onclick="redirectToHome()">Login</button>
                    </form>
                </div>

                <!-- Manual Login Form -->
                <div class="manual-login-container mt-3">
                    <form id="manualLoginForm" action="./endpoint/login.php" method="POST">
                        <h5 class="text-center">Or enter your generated code</h5>
                        <input type="text" name="qr-code" class="form-control mb-2" placeholder="Enter your QR code manually" required>
                        <button type="button" class="btn btn-primary form-control" onclick="redirectToHome()">Login</button>
                    </form>
                </div>

                <p class="mt-3">No Account? Register <span class="switch-form-link" onclick="showRegistrationForm()">Here.</span></p>
            </div>
        </div>
    </div>

    <!-- Registration Area -->
    <div class="registration-container">
        <div class="registration-form" id="registrationForm">
            <h2 class="text-center">Registration Form</h2>
            <p class="text-center">Fill in your personal details.</p>
            <form action="./endpoint/add-user.php" method="POST" enctype="multipart/form-data">
                <div class="hide-registration-inputs">
                    <div class="form-group registration">
                        <label for="name">Full Name:</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group registration row">
                        <div class="col-md-6">
                            <label for="contactNumber">Contact Number:</label>
                            <input type="text" class="form-control" id="contactNumber" name="contact_number" required maxlength="11">
                        </div>
                        <div class="col-md-6">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="form-group registration row">
                        <div class="col-md-12">
                            <label for="image">Upload Profile Image(jpg):</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                        </div>
                    </div>
                    <div class="form-group registration row">
                        <div class="col-md-6">
                            <label for="dob">Date of Birth:</label>
                            <input type="date" class="form-control" id="dob" name="dob" required>
                        </div>
                        <div class="col-md-6">
                            <label for="level">Level:</label>
                            <input type="text" class="form-control" id="level" name="level" placeholder="e.g., 200" required>
                        </div>
                    </div>
                    <div class="form-group registration row">
                        <div class="col-md-6">
                            <label for="session">Session:</label>
                            <input type="text" class="form-control" id="session" name="session" placeholder="e.g., 2024/2025" required>
                        </div>
                        <div class="col-md-6">
                            <label for="department">Department:</label>
                            <input type="text" class="form-control" id="department" name="department" required>
                        </div>
                    </div>
                    <p>Already have a QR code account? Login <span class="switch-form-link" onclick="location.reload()">Here.</span></p>
                    <button type="button" class="btn btn-dark login-register form-control" onclick="generateQrCode()">Register and Generate QR Code</button>
                </div>
                <div class="qr-code-container text-center" style="display: none;">
                    <h3>Take a Picture of your QR Code and Login!</h3>
                    <input type="hidden" id="generatedCode" name="generated_code">
                    <div class="m-4" id="qrBox">
                        <img src="" id="qrImg">
                    </div>
                    <button type="submit" class="btn btn-dark">Back to Login Form.</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap Js -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    <!-- instascan Js -->
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>

    <!-- Script Area -->
    <script>
        const loginCon = document.querySelector('.login-container');
        const registrationCon = document.querySelector('.registration-container');
        const registrationForm = document.querySelector('.registration-form');
        const qrCodeContainer = document.querySelector('.qr-code-container');
        let scanner;

        registrationCon.style.display = "none";
        qrCodeContainer.style.display = "none";

        function showRegistrationForm() {
            registrationCon.style.display = "";
            loginCon.style.display = "none";
            if (scanner) {
                scanner.stop();
            }
        }

        function startScanner() {
            scanner = new Instascan.Scanner({
                video: document.getElementById('interactive')
            });

            scanner.addListener('scan', function(content) {
                $("#detected-qr-code").val(content);
                scanner.stop();
                document.querySelector(".qr-detected-container").style.display = '';
                document.querySelector(".viewport").style.display = 'none';
            });

            Instascan.Camera.getCameras()
                .then(function(cameras) {
                    if (cameras.length > 0) {
                        scanner.start(cameras[0]);
                    } else {
                        alert('No cameras found. Please check your device.');
                    }
                })
                .catch(function(err) {
                    alert('Camera access error: ' + err);
                });
        }

        function generateRandomCode(length) {
            const characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz!@#$%^&*()';
            let randomString = '';

            for (let i = 0; i < length; i++) {
                const randomIndex = Math.floor(Math.random() * characters.length);
                randomString += characters.charAt(randomIndex);
            }

            return randomString;
        }

        function generateQrCode() {
            const registrationInputs = document.querySelector('.hide-registration-inputs');
            const h2 = document.querySelector('.registration-form > h2');
            const p = document.querySelector('.registration-form > p');
            const qrImg = document.getElementById('qrImg');
            const qrBox = document.getElementById('qrBox');

            registrationInputs.style.display = 'none';

            let text = generateRandomCode(10);
            $("#generatedCode").val(text);

            if (text === "") {
                alert("Please enter text to generate a QR code.");
                return;
            } else {
                const apiUrl = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(text)}`;

                // Generating image
                qrImg.src = apiUrl;
                qrBox.setAttribute("id", "qrBoxGenerated");
                qrCodeContainer.style.display = "";
                registrationCon.style.display = "";
                h2.style.display = "none";
                p.style.display = "none";
            }
        }
        
        function redirectToHome() {
            window.location.href = "../home.php";
        }

        document.addEventListener('DOMContentLoaded', startScanner);
    </script>

</body>

</html>