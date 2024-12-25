<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'Database.php';

$conn = connectToDatabase();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['akce'])) {
    $nazevAkce = $_POST['akce'];

    // Načtení detailů akce z databáze
    $sql = "SELECT * FROM akce WHERE nazev = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nazevAkce);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $akce = $result->fetch_assoc();
        echo "Vybrali jste akci: " . htmlspecialchars($akce['nazev']);
    } else {
        echo "Akce nebyla nalezena.";
    }
} else {
    echo "Nebyla vybrána žádná akce.";
}

$conn->close();
?>
