<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $db_name = "contact_form";

    $conn = mysqli_connect($servername, $username, $password, $db_name);

    if (!$conn) {
        // Return JSON error
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit();
    }

    // Get and sanitize inputs
    $user = mysqli_real_escape_string($conn, $_POST['username'] ?? '');
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $message = mysqli_real_escape_string($conn, $_POST['message'] ?? '');

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit();
    }

    // Check for empty fields
    if (empty($user) || empty($email) || empty($message)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit();
    }

    // Use prepared statements
    $sql = "INSERT INTO greetings (username, email, message) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Statement preparation failed']);
        exit();
    }

    mysqli_stmt_bind_param($stmt, "sss", $user, $email, $message);

    if (mysqli_stmt_execute($stmt)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Your message has been sent successfully!']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Failed to send message']);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    exit();
}
?>