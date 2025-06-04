<?php
session_start(); // Start the session
include('../conn/conn.php');

// Check if form data is received
if (
    isset(
        $_POST['name'], $_POST['contact_number'], $_POST['email'],
        $_POST['generated_code'], $_FILES['image'], $_POST['dob'],
        $_POST['level'], $_POST['session'], $_POST['department']
    )
) {
    $name = $_POST['name'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];
    $generatedCode = $_POST['generated_code'];
    $dob = $_POST['dob'];
    $level = $_POST['level'];
    $sessionVal = $_POST['session'];
    $department = $_POST['department'];

    // Optional fallback values
    $dob = !empty($dob) ? $dob : '2000-01-01';
    $level = !empty($level) ? $level : 0;
    $sessionVal = !empty($sessionVal) ? $sessionVal : 'Not Available';
    $department = !empty($department) ? $department : 'Undecided';

    // Image upload handling
    $imageName = $_FILES['image']['name'];
    $imageTmp = $_FILES['image']['tmp_name'];
    $imageSize = $_FILES['image']['size'];
    $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png'];
    $imagePath = '../uploads/' . uniqid('user_', true) . '.' . $imageExtension;

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
            echo "<script>alert('Invalid image format. Please use jpg, jpeg or png'); window.location.href = 'http://localhost/QrLogin/';</script>";
            exit;
        }

        // Validate image size
        if ($imageSize > 5 * 1024 * 1024) {
            $conn->rollBack();
            echo "<script>alert('Image size exceeds 5MB. Please upload a smaller image.'); window.location.href = 'http://localhost/QrLogin/';</script>";
            exit;
        }

        // Upload image and insert into database
        if (move_uploaded_file($imageTmp, $imagePath)) {
            $insertStmt = $conn->prepare("
                INSERT INTO `tbl_user` (`name`, `contact_number`, `email`, `generated_code`, `image`, `dob`, `level`, `session`, `department`)
                VALUES (:name, :contact_number, :email, :generated_code, :image, :dob, :level, :session, :department)
            ");

            $insertStmt->execute([
                'name' => $name,
                'contact_number' => $contact_number,
                'email' => $email,
                'generated_code' => $generatedCode,
                'image' => basename($imagePath),
                'dob' => $dob,
                'level' => $level,
                'session' => $sessionVal,
                'department' => $department
            ]);

            $conn->commit();

            // Auto-login user after registration
            $_SESSION['email'] = $email;
            $_SESSION['name'] = $name;
            $_SESSION['qr_code'] = $generatedCode;

            echo "<script>alert('Registration successful! Redirecting to dashboard...'); window.location.href = 'http://localhost/QrLogin/home.php';</script>";
        } else {
            $conn->rollBack();
            echo "<script>alert('Error uploading image.'); window.location.href = 'http://localhost/QrLogin/';</script>";
        }
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "<script>alert('All fields are required.'); window.location.href = 'http://localhost/QrLogin/';</script>";
}
?>
