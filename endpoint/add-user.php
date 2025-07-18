<?php
session_start();
include('../conn/conn.php');

if (
    isset(
        $_POST['name'], $_POST['contact_number'], $_POST['email'],
        $_POST['generated_code'], $_FILES['image'], $_POST['dob'],
        $_POST['level'], $_POST['session'], $_POST['department']
    )
) {
    $name = trim($_POST['name']);
    $contact_number = trim($_POST['contact_number']);
    $email = trim($_POST['email']);
    $generatedCode = trim($_POST['generated_code']);
    $dob = !empty($_POST['dob']) ? $_POST['dob'] : '2000-01-01';
    $level = !empty($_POST['level']) ? $_POST['level'] : 'Not Specified';
    $sessionVal = !empty($_POST['session']) ? $_POST['session'] : 'Not Available';
    $department = !empty($_POST['department']) ? $_POST['department'] : 'Undecided';

    // Image upload handling
    $imageName = $_FILES['image']['name'];
    $imageTmp = $_FILES['image']['tmp_name'];
    $imageSize = $_FILES['image']['size'];
    $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png'];
    $uniqueImageName = uniqid('user_', true) . '.' . $imageExtension;
    $uploadDir = '../uploads/';
    $imagePath = $uploadDir . $uniqueImageName;

    // Validate required fields
    if (
        empty($name) || empty($contact_number) || empty($email) ||
        empty($generatedCode) || empty($imageName)
    ) {
        echo "<script>alert('Please fill in all fields and upload an image.'); window.location.href = 'http://localhost/QrLogin/';</script>";
        exit;
    }

    try {
        $conn->beginTransaction();

        // Check for duplicate email
        $stmt = $conn->prepare("SELECT `email` FROM `tbl_user` WHERE `email` = :email");
        $stmt->execute(['email' => $email]);

        if ($stmt->rowCount() > 0) {
            $conn->rollBack();
            echo "<script>alert('Email already exists!'); window.location.href = 'http://localhost/QrLogin/';</script>";
            exit;
        }

        // Validate image extension
        if (!in_array($imageExtension, $allowedExtensions)) {
            $conn->rollBack();
            echo "<script>alert('Invalid image format. Please use jpg, jpeg or png.'); window.location.href = 'http://localhost/QrLogin/';</script>";
            exit;
        }

        // Validate image size (max 5MB)
        if ($imageSize > 5 * 1024 * 1024) {
            $conn->rollBack();
            echo "<script>alert('Image size exceeds 5MB. Please upload a smaller image.'); window.location.href = 'http://localhost/QrLogin/';</script>";
            exit;
        }

        // Upload image
        if (!move_uploaded_file($imageTmp, $imagePath)) {
            $conn->rollBack();
            echo "<script>alert('Failed to upload image.'); window.location.href = 'http://localhost/QrLogin/';</script>";
            exit;
        }

        // Insert into database
        $insertStmt = $conn->prepare("
            INSERT INTO `tbl_user` (`name`, `contact_number`, `email`, `generated_code`, `image`, `dob`, `level`, `session`, `department`)
            VALUES (:name, :contact_number, :email, :generated_code, :image, :dob, :level, :session, :department)
        ");

        $insertStmt->execute([
            'name' => $name,
            'contact_number' => $contact_number,
            'email' => $email,
            'generated_code' => $generatedCode,
            'image' => $uniqueImageName,
            'dob' => $dob,
            'level' => $level,
            'session' => $sessionVal,
            'department' => $department
        ]);

        $userId = $conn->lastInsertId();
        $conn->commit();

        // Auto-login user after registration
        $_SESSION['user_id'] = $userId;
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
        $_SESSION['qr_code'] = $generatedCode;
        $_SESSION['image'] = $uniqueImageName;
        $_SESSION['dob'] = $dob;
        $_SESSION['level'] = $level;
        $_SESSION['session'] = $sessionVal;
        $_SESSION['department'] = $department;

        echo "<script>alert('Registration successful! Redirecting to dashboard...'); window.location.href = 'http://localhost/QrLogin/home.php';</script>";

    } catch (PDOException $e) {
        $conn->rollBack();
        echo "<script>alert('Database Error: " . addslashes($e->getMessage()) . "'); window.location.href = 'http://localhost/QrLogin/';</script>";
    }

} else {
    echo "<script>alert('All fields are required.'); window.location.href = 'http://localhost/QrLogin/';</script>";
}
?>
