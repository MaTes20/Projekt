<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}





//function connectToDatabase(){
    // Připojení k databázi
$servername = "a058um.forpsi.com";
$usernameDB = "f187209"; // Renamed variable to avoid conflict with $username
$password = "usK48Xtk";
$dbname = "f187209";
//$nastaveni["db_nastaveni"] = "mysql://web.bezvatabor.cz:Rychl039py@sql13.pipni.cz/web_bezvatabor_cz";
//$conn = mysql_connect($servername, $usernameDB, $password, $dbname);

$conn = new mysqli("a058um.forpsi.com", "f187209", "usK48Xtk", "f187209");
//$conn = mysqli_connect("mysql", "web.bezvatabor.cz", "Rychl039py", "web_bezvatabor_cz");
//$conn = mysql_select_db('web_bezvatabor_cz', $db);


//}


// Připojení k databázi
//$conn = connectToDatabase();


if ($conn->connect_errno) {
        echo $conn;
        die('Chyba připojení k databázi.');
    }
else {

    $conn->set_charset("utf8mb4");
    echo "!!!! Připojeno !!!!";
    echo "";
    echo 'Verze PHP:' . phpversion();
    echo "";
    phpinfo();
    
    }


?>
