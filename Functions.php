<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Zjisti, zda je uživatel přihlášen
function getCurrentUsername() {
    return isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
}

// Funkce pro registraci
function registerUser($conn, $username, $email, $password) {
    $username = $conn->real_escape_string($username);
    $email = $conn->real_escape_string($email);
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO ucet (uzivatelske_jmeno, email, heslo) VALUES ('$username', '$email', '$hashedPassword')";

    if ($conn->query($sql) === TRUE) {
        return "Registrace byla úspěšná!";
    } else {
        return "Chyba: " . $conn->error;
    }
}

// Funkce pro přihlášení
function loginUser($conn, $username, $password) {
    $username = $conn->real_escape_string($username);

    $sql = "SELECT heslo FROM ucet WHERE uzivatelske_jmeno = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['heslo'])) {
            $_SESSION['username'] = $username; // Uložíme uživatele do session
            return true;
        } else {
            return "Chybné heslo.";
        }
    } else {
        return "Uživatel neexistuje.";
    }
}



?>