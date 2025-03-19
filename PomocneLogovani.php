<?php

// Funkce pro zápis do logu
function zapisLog($zprava) {
    $soubor = 'prubeh.log'; // Název logovacího souboru
    $cas = date('Y-m-d H:i:s'); // Aktuální èas
    $logZaznam = "[$cas] $zprava" . PHP_EOL; // Formát záznamu

// Zápis do souboru (pøidání na konec)
    file_put_contents($soubor, $logZaznam, FILE_APPEND | LOCK_EX);
    
}

zapisLog("BOD 1 ");
zapisLog("pirat je" . $_POST['pirat'] );
zapisLog("zapis je" . $zapis );

?>


V HTML
<?php zapisLog("HTML hlavièka ");?>
