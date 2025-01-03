<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'Database.php';
    require 'Functions.php';

    $conn = connectToDatabase();

    if (!$conn) {
        die('Chyba připojení k databázi.');
    }

    echo "Připojení k databázi bylo úspěšné.<br>";

    $data = [
        $_POST['jmeno'] ?? null,
        $_POST['prijmeni'] ?? null,
        $_POST['ulice'] ?? null,
        $_POST['mesto'] ?? null,
        $_POST['psc'] ?? null,
        $_POST['narozeni'] ?? null,
        $_POST['jmenorod1'] ?? null,
        $_POST['prijmenirod1'] ?? null,
        $_POST['telefonrod1'] ?? null,
        $_POST['jmenorod2'] ?? null,
        $_POST['prijmenirod2'] ?? null,
        $_POST['telefonrod2'] ?? null,
        $_POST['email'] ?? null,
        $_POST['plavec'] ?? null,
        $_POST['doprava'] ?? null,
        $_POST['platba'] ?? null,
        isset($_POST['vernostni']) ? 'ANO' : 'NE',
        isset($_POST['sourozenec']) ? 'ANO' : 'NE',
        isset($_POST['kamarad']) ? 'ANO' : 'NE',
        $_POST['kamaradjmeno'] ?? null,
        $_POST['zdravi'] ?? null,
        $_POST['poznamka'] ?? null
    ];

    $sql = "INSERT INTO deti (jmeno, prijmeni, ulice, mesto, psc, narozeni, jmenorod1, prijmenirod1, telefonrod1, jmenorod2, prijmenirod2, telefonrod2, email, plavec, doprava, platba, vernostni, sourozenec, kamarad, kamaradjmeno, zdravi, poznamka) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die('Chyba při přípravě dotazu: ' . $conn->error);
    }

    // Sestavení typu parametrů
    $paramTypes = str_repeat('s', count($data)); // vše jsou řetězce

    // Předání parametrů
    $stmt->bind_param($paramTypes, ...$data);

    try {
        $stmt->execute();
        echo 'Přihláška byla úspěšně odeslána.';
    } catch (Exception $e) {
        echo 'Chyba při ukládání dat: ' . $e->getMessage();
    }

    $stmt->close();
    $conn->close();
}
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

<form name="prihlaska"  id="prihlaska" method="POST"  action="Prihlaska.php">
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