<?php
session_start();
include('./conn/conn.php');

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch full user data
    $stmt = $conn->prepare("SELECT * FROM `tbl_user` WHERE `tbl_user_id` = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        echo "<script>alert('User not found!'); window.location.href = 'http://localhost/QrLogin/';</script>";
        exit;
    }
} else {
    // Redirect to login page with a message
    echo "<script>alert('You are not logged in. Please log in first.'); window.location.href = 'http://localhost/QrLogin/';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - QR Code System</title>
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

        .container {
            backdrop-filter: blur(10px);
            color: white;
            padding: 30px;
            border-radius: 12px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            width: 600px;
        }

        .user-info label {
            font-weight: bold;
        }

        .logout-btn {
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <div class="container text-center">
        <h2 class="mb-4">Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h2>

        <div class="user-info text-left">
            <p>
                <img src=".uploads/<?php 
                    $imageFile = !empty($user['image']) && file_exists('./uploads/' . $user['image']) ? $user['image'] : 'default.png';
                    echo htmlspecialchars($imageFile);
                ?>" 
                alt="User Image" 
                class="img-thumbnail mb-3" 
                width="150" height="150">
            </p>
            <p><label>Name:</label> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><label>Contact Number:</label> <?php echo htmlspecialchars($user['contact_number']); ?></p>
            <p><label>Email:</label> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><label>Generated Code:</label> <?php echo htmlspecialchars($user['generated_code']); ?></p>
            <p><label>Date of Birth:</label> <?php echo htmlspecialchars($user['dob']); ?></p>
            <p><label>Level:</label> <?php echo htmlspecialchars($user['level']); ?></p>
            <p><label>Study Session:</label> <?php echo htmlspecialchars($user['session']); ?></p>
            <p><label>Department:</label> <?php echo htmlspecialchars($user['department']); ?></p>
        </div>

        <a class="btn btn-dark logout-btn" href="./endpoint/logout.php">Logout</a>
    </div>

</body>

</html>
