<?php
session_start();
include('../conn/conn.php');

// Accept both GET and POST
$qrCode = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['qr-code'])) {
    $qrCode = $_POST['qr-code'];
} elseif ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['qr-code'])) {
    $qrCode = $_GET['qr-code'];
} else {
    echo "<script>alert('Invalid request!'); window.location.href = 'http://localhost/QrLogin/';</script>";
    exit;
}

// Prepare query to fetch user data
$stmt = $conn->prepare("
    SELECT `tbl_user_id`, `name`, `email`, `generated_code`, `image`, `dob`, `level`, `session`, `department`
    FROM `tbl_user`
    WHERE `generated_code` = :generated_code
");
$stmt->bindParam(':generated_code', $qrCode);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // Store relevant user data in session
    $_SESSION['user_id'] = $user['tbl_user_id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['qr_code'] = $user['generated_code'];
    $_SESSION['image'] = $user['image'];
    $_SESSION['dob'] = $user['dob'];
    $_SESSION['level'] = $user['level'];
    $_SESSION['session'] = $user['session'];
    $_SESSION['department'] = $user['department'];

    echo "
    <script>
        alert('Login Successful!');
        window.location.href = 'http://localhost/QrLogin/home.php';
    </script>
    ";
} else {
    echo "
    <script>
        alert('QR Code does not exist!');
        window.location.href = 'http://localhost/QrLogin/';
    </script>
    ";
}
?>
