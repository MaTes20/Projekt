<?php


function connectToDatabase(){
    // Připojení k databázi
$servername = "localhost";
$usernameDB = "root"; // Renamed variable to avoid conflict with $username
$password = "";
$dbname = "tabor";

$conn = mysqli_connect($servername, $usernameDB, $password, $dbname);

if (!$conn) {
    die("Připojení k databázi selhalo: " . mysqli_connect_error());
}

return $conn;
}






?>