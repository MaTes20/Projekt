<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Načtení souborů
require 'Database.php';
require 'Functions.php';

// Připojení k databázi
$conn = connectToDatabase();

// Získání aktuálního uživatele
$currentUsername = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

// Zpracování registrace
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['register'])) {
    $message = registerUser($conn, $_POST['new_username'], $_POST['email'], $_POST['new_password']);
    echo $message;
    header("Location: Index.php");
    exit();
}

// Zpracování přihlášení
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
    $result = loginUser($conn, $_POST['username'], $_POST['password']);
    if ($result === true) {
        header("Location: Index.php"); // Přesměrování na aktuální stránku
        exit();
    } else {
        echo $result; // Zobrazení chyby
    }
}

// Funkce pro vložení dat do databáze
function vlozitDataBezBindParam(
    $conn, 
    $nazev, $jmeno, $prijmeni, $adresa, $narozeni, $rodic1, $telefonrod1, 
    $rodic2, $telefonrod2, $email, $plavec, $doprava, $platba, 
    $vernostni, $sourozenec, $kamarad, $kamaradjmeno, $zdravi, $poznamka
) {
    // Ošetření vstupů proti SQL injection
    $nazev = $conn->real_escape_string($nazev);
    $jmeno = $conn->real_escape_string($jmeno);
    $prijmeni = $conn->real_escape_string($prijmeni);
    $adresa = $conn->real_escape_string($adresa);
    $narozeni = $conn->real_escape_string($narozeni);
    $rodic1 = $conn->real_escape_string($rodic1);
    $telefonrod1 = $conn->real_escape_string($telefonrod1);
    $rodic2 = $conn->real_escape_string($rodic2);
    $telefonrod2 = $conn->real_escape_string($telefonrod2);
    $email = $conn->real_escape_string($email);
    $plavec = $conn->real_escape_string ($plavec);
    $doprava = $conn->real_escape_string ($doprava);
    $platba = $conn->real_escape_string($platba);
    $vernostni = $conn->real_escape_string ($vernostni);
    $sourozenec = $conn->real_escape_string ($sourozenec);
    $kamarad = $conn->real_escape_string ($kamarad);
    $kamaradjmeno = $conn->real_escape_string($kamaradjmeno);
    $zdravi = $conn->real_escape_string($zdravi);
    $poznamka = $conn->real_escape_string($poznamka);

    // SQL dotaz
    $sql = "INSERT INTO prihlaseni 
        (nazev, jmeno, prijmeni, adresa, narozeni, rodic1, telefonrod1, rodic2, telefonrod2, 
         email, plavec, doprava, platba, vernostni, sourozenec, kamarad, kamaradjmeno, zdravi, poznamka)
        VALUES 
        ('$nazev', '$jmeno', '$prijmeni', '$adresa', '$narozeni', '$rodic1', '$telefonrod1', '$rodic2', '$telefonrod2',
         '$email', $plavec, $doprava, $platba, $vernostni, $sourozenec, $kamarad, '$kamaradjmeno', '$zdravi', '$poznamka')";

    // Provedení dotazu
    if ($conn->query($sql) === TRUE) {
        return "Data byla úspěšně vložena.";
    } else {
        return "Chyba při vkládání dat: " . $conn->error;
    }
}

    


$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=, initial-scale=1.0">
    <link rel="stylesheet" href="prihlaska.css">

    <title>prihlaska</title>
</head>
<body>
    
   
    <!-- Hlavička s navigací -->
    <header>
        <img src="/images/logoBAT.png">
        
        <nav>
            <ul>
                <li><a href="Index.php" target="_self">Úvod</a></li>
                <li><a href="Poradatel.php" target="_self">Pořadatel</a></li>
                <li><a href="Akce.php">Akce</a></li>
                <li><a href="Dovednosti.php">Dovednosti</a></li>
                <li><a href="#">Vzkazy</a></li>
                <li><a href="#">Fotoalbum</a></li>
            </ul>
            
        </nav>
        <div class="account">
       
        <!-- Profile section with hover effect -->
<div class="profile-dropdown">
    <div class="profile">
    <?= htmlspecialchars($currentUsername) ?>

    </div>

    <!-- Dropdown menu for login/logout -->
    <div class="dropdown">
       
        <div class="dropdown-content">
            <?php if (isset($_SESSION['username'])): ?>
                <!-- Show 'Logout' if the user is logged in -->
                <a href="logout.php">Odhlásit se</a>
            <?php else: ?>
                <!-- Show 'Login' if the user is not logged in -->
                <a href="#" onclick="openForm()">Přihlásit se</a>
            <?php endif; ?>
        </div>
    </div>
</div>

    </div>


    </header>

<form name="prihlaska"  id="prihlaska" method="POST" action="Prihlaska.php">
    <p><i>Pole označené <b style="color: red; font-size: 20px;">*</b> jsou povinné. Bez jejich vyplnění nelze přihlášku odeslat.</i></p>
    <hr>

    <!-- Jméno a příjmení -->
    <label for="jmeno"><b>Jméno a příjmení:</b> <b style="color: red; font-size: 20px;">*</b></label><br>
    <input type="text" id="jmeno" name="jmeno" placeholder="Jméno" maxlength="20" required>
    <input type="text" id="prijmeni" name="prijmeni" placeholder="Příjmení" maxlength="25" required>
    <br><br>

    <!-- Adresa -->
    <label><b>Adresa:</b> <b style="color: red; font-size: 20px;">*</b></label><br>
    <input type="text" id="ulice" name="ulice" placeholder="Ulice" maxlength="30" required>
    <input type="text" id="mesto" name="mesto" placeholder="Město" maxlength="30" required>
    <input type="number" id="psc" name="psc" placeholder="PSČ" min="10000" max="99999" required>
    <br><br>

    <!-- Datum narození -->
    <label for="narozeni"><b>Datum narození:</b> <b style="color: red; font-size: 20px;">*</b></label><br>
    <input type="date" id="narozeni" name="narozeni" required>
    <small>Vyplňte v nezkráceném tvaru (např. 02.01.2015).</small>
    <br><br>

    <hr>

    <!-- Sekce rodiče -->
    <p><b style="color: red;">Další část přihlášky nech vyplnit rodiče.</b></p>

    <!-- Spojení na rodiče -->
    <label><b>Spojení na rodiče / zákonného zástupce:</b></label><br>
    <label>Jméno a kontaktní telefon: <b style="color: red; font-size: 20px;">*</b></label><br>
    <input type="text" id="jmenorod1" name="jmenorod1" placeholder="Jméno" maxlength="20" required>
    <input type="text" id="prijmenirod1" name="prijmenirod1" placeholder="Příjmení" maxlength="25" required>
    <input type="tel" id="telefonrod1" name="telefonrod1" placeholder="Telefon" pattern="\d{9}" required>
    <br><br>
    <label>Jméno a kontaktní telefon:</label><br>
    <input type="text" id="jmenorod2" name="jmenorod2" placeholder="Jméno" maxlength="20">
    <input type="text" id="prijmenirod2" name="prijmenirod2" placeholder="Příjmení" maxlength="25">
    <input type="tel" id="telefonrod2" name="telefonrod2" placeholder="Telefon" pattern="\d{9}">
    <br><small>Telefon vyplňujte bez předvolby (+420) a bez mezer.</small>
    <br><br>

    <!-- Kontaktní email -->
    <label for="email"><b>Kontaktní emailová adresa:</b> <b style="color: red; font-size: 20px;">*</b></label><br>
    <input type="email" id="email" name="email" placeholder="@" maxlength="30" required>
    <br><br>

    <!-- Plavec / Neplavec -->
    <label><b>Můj syn / Má dcera je:</b> <b style="color: red; font-size: 20px;">*</b></label><br>
    <input type="radio" id="plavec" name="plavec" value="PLAVEC" required> Plavec
    <input type="radio" id="neplavec" name="plavec" value="NEPLAVEC" required> Neplavec
    <br><br>

    <!-- Místo odjezdu -->
    <label><b>Pojede z odjezdového místa:</b> <b style="color: red; font-size: 20px;">*</b></label><br>
    <input type="radio" name="doprava" value="Ústí n.L." required> Ústí nad Labem
    <input type="radio" name="doprava" value="Praha" required> Praha
    <input type="radio" name="doprava" value="Votice" required> Votice
    <br><br>

    <!-- Platba -->
    <label><b>Platit budeme:</b></label><br>
    <input type="radio" name="platba" value="Převodem"> Převodem
    <input type="radio" name="platba" value="Fakturou"> Fakturou
    <small>(Pro zaměstnavatele k využití příspěvku FKSP.)</small>
    <br><br>

    <!-- Zdravotní potíže -->
    <label for="zdravi"><b>Zdravotní potíže / omezení:</b></label><br>
    <textarea id="zdravi" name="zdravi" rows="4" maxlength="200"></textarea>
    <br><br>

    <!-- Poznámky rodičů -->
    <label for="poznamka"><b>Poznámky rodičů:</b></label><br>
    <textarea id="poznamka" name="poznamka" rows="4" maxlength="200" placeholder="Zde můžete napsat další požadavky."></textarea>
    <br><br>

    <!-- Slevy -->
    <label><b>Žádám o slevu:</b></label><br>
    <input type="checkbox" name="vernostni" value="ANO"> Věrnostní
    <input type="checkbox" name="sourozenec" value="ANO"> Sourozenec
    <input type="checkbox" name="kamarad" value="ANO"> Kamarád
    <input type="text" name="kamaradjmeno" placeholder="Jméno kamaráda/ky" maxlength="30">
    <br><small><a href="sleva.php" target="_blank">Informace ke slevám</a></small>
    <br><br>

    <!-- Souhlas -->
    <input type="checkbox" name="podminky" value="ANO" required>
    <b style="color: red; font-size: 20px;">*</b> <span style="color: red;">Prohlašuji, že jsem vyplnil(a) veškeré údaje pravdivě a seznámil(a) se s <a href="podminky.php" target="_blank">informacemi pro rodiče</a>.</span>
    <br><br>

    <!-- Antispam -->
    <label for="pirat"><b>Ochrana proti SPAM robotům:</b></label><br>
    <span>Napište slovo "želva": </span><input type="text" id="pirat" name="pirat" style="font: 12px Arial" required>
    <br><br>

    <!-- Odeslat -->
    <input type="submit" name="poslat" value="Odeslat přihlášku">
</form>
    
</body>
</html>