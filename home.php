<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background:#f5f5f5;">
<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">QR Code Dashboard</a>
        <a href="./endpoint/logout.php" class="btn btn-outline-light">Logout</a>
    </div>
</nav>

<div class="container my-5">
    <div class="card mx-auto" style="max-width:500px;">
        <div class="card-body text-center">
            <img src="./uploads/<?php echo $_SESSION['image']; ?>" class="rounded-circle mb-3" width="120" height="120" alt="User Image">
            <h4><?php echo htmlspecialchars($_SESSION['name']); ?></h4>
            <p><?php echo htmlspecialchars($_SESSION['email']); ?></p>
            <p>Department: <?php echo htmlspecialchars($_SESSION['department']); ?></p>
            <p>Level: <?php echo htmlspecialchars($_SESSION['level']); ?></p>
            <p>Session: <?php echo htmlspecialchars($_SESSION['session']); ?></p>
            <p>QR Code: <?php echo htmlspecialchars($_SESSION['qr_code']); ?></p>
            <img src="https://api.qrserver.com/v1/create-qr-code/?data=<?php echo urlencode($_SESSION['qr_code']); ?>&size=150x150" alt="QR Code">
        </div>
    </div>
</div>
</body>
</html>
