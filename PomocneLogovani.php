<?php

// Funkce pro z�pis do logu
function zapisLog($zprava) {
    $soubor = 'prubeh.log'; // N�zev logovac�ho souboru
    $cas = date('Y-m-d H:i:s'); // Aktu�ln� �as
    $logZaznam = "[$cas] $zprava" . PHP_EOL; // Form�t z�znamu

// Z�pis do souboru (p�id�n� na konec)
    file_put_contents($soubor, $logZaznam, FILE_APPEND | LOCK_EX);
    
}

zapisLog("BOD 1 ");
zapisLog("pirat je" . $_POST['pirat'] );
zapisLog("zapis je" . $zapis );

?>


V HTML
<?php zapisLog("HTML hlavi�ka ");?>
