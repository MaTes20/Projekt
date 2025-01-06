<?php
session_start();
require 'Database.php';

$conn = connectToDatabase();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_SESSION['username'] ?? 'Guest';
    $message = $_POST['message'];

    if ($message) {
        $stmt = $conn->prepare("INSERT INTO messages (user_name, message) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $message);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Chyba při ukládání zprávy.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Zpráva je prázdná.']);
    }
} else {
    $stmt = $conn->prepare("SELECT user_name, message, created_at FROM messages ORDER BY created_at DESC LIMIT 20");
    $stmt->execute();
    $result = $stmt->get_result();

    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($messages);
}

$conn->close();

?>
