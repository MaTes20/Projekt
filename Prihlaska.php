<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'Database.php';
    require 'Functions.php';
    
    
    $conn = connectToDatabase();

    if (!$conn) {
        die('Chyba připojení k databázi.');
    }

    //echo "Připojení k databázi bylo úspěšné.<br>";
    
    
    
    
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
    
    
    
    
    

    
    function vrat_pocet_deti_akce($akce_fk) {
        
        
        //$akce = vrat_akci($_GET["akce_id"]);    
        //$vysledek = $conn->getRow("SELECT COUNT(*) AS pocetdetiakce FROM deti WHERE akce_id = $akce->akce_id ", array(),
        //  DB_FETCHMODE_OBJECT);
        //return $vysledek->pocetdetiakce;
        //$akce_id = 1;
        global $conn; 
        $pocet = $conn->query("SELECT akce_fk FROM deti WHERE akce_fk = $akce_fk");
        $pocet_deti = $pocet->num_rows;
        //echo $pocet_deti;
        //echo "</br>";
        return $pocet_deti;
        
      }

    function vrat_pocet_deti() {
        global $conn; 
        $pocet = $conn->query("SELECT dite_id FROM deti");
        $pocet_deti = $pocet->num_rows;
        //echo $pocet_deti;
        //echo "</br>";
        return $pocet_deti;
      }
      
      
    //function vrat_akci($id) {
        
      //  global $conn;
        //$vysledek = $conn->getRow("SELECT UNIX_TIMESTAMP(datum_uzaverky) AS datum_uzaverky, DATE_FORMAT(datum_od,'%e.%c.%Y %k:%i') AS datum_od,DATE_FORMAT(datum_do,'%e.%c.%Y %k:%i') AS datum_do, UNIX_TIMESTAMP(datum_od) AS datum_od_unix, UNIX_TIMESTAMP(datum_do) AS datum_do_unix, akce_id,popis,nazev,misto,tema,odjezd,prijezd,cosebou,dalsi_info FROM akce WHERE akce_id=$id", array(), DB_FETCHMODE_OBJECT);
        
       // echo $vysledek;
       // echo "</br>";
        //return $vysledek;
      //}
    

     // $akce = vrat_akci($_GET["akce_id"]);
     // $nazev = $akce->nazev;
     // $akce_fk = $akce->akce_fk;

    
    //$result = $conn->query("SELECT dite_id FROM deti");
    //$pocetdeti = $result->num_rows;
 
    $akce_fk = 2;
      
    $pocetdeti = vrat_pocet_deti();
    $dite_id = $pocetdeti + 1;

    $pocetdetiakce = vrat_pocet_deti_akce($akce_fk);
    $diteakce_id = $pocetdetiakce + 1;

    //$akce_fk = 2;
    $nazev = "BAT";
    //$diteakce_id = 1;


    $prihlaska = $conn->prepare("INSERT INTO deti (dite_id, akce_fk, nazev, diteakce_id, jmeno, prijmeni, adresa, narozeni, rodic1, telefonrod1, rodic2, telefonrod2, email, plavec, doprava, platba, vernostni, sourozenec, kamarad, kamaradjmeno, zdravi, poznamka) 
    VALUES ('$dite_id', '$akce_fk', '$nazev', '$diteakce_id', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");






    $data = [
      $jmeno =  $_POST['jmeno'],
      $prijmeni= $_POST['prijmeni'],
      $adresa = $_POST["ulice"].", ".$_POST["mesto"].", ".$_POST["psc"],
      $narozeni = $_POST['narozeni'],
      $rodic1 = $_POST["jmenorod1"]." ".$_POST["prijmenirod1"],
      $telefonrod1 = $_POST['telefonrod1'],
      $rodic2 = $_POST["jmenorod2"]." ".$_POST["prijmenirod2"],
      $telefonrod2 = $_POST['telefonrod2'],
      $email = $_POST['email'],
      $plavec = $_POST['plavec'],
      $doprava = $_POST['doprava'],
      $platba = $_POST['platba'],
      $vernostni = isset($_POST['vernostni']) ? 'ANO' : 'NE',
      $sourozenec = isset($_POST['sourozenec']) ? 'ANO' : 'NE',
      $kamarad = isset($_POST['kamarad']) ? 'ANO' : 'NE',
      $kamaradjmeno = $_POST['kamaradjmeno'],
      $zdravi = $_POST['zdravi'],
      $poznamka = $_POST['poznamka']
    ];
   // $stmt->bind_param("iisissssssssssssssssss", $dite_id, $akce_fk, $nazev, $diteakce_id, $jmeno, $prijmeni, $adresa, $narozeni, $rodic1, $telefonrod1, $rodic2, $telefonrod2, $email, $plavec, $doprava, $platba, $vernostni, $sourozenec, $kamarad, $kamaradjmeno, $zdravi, $poznamka);
    
    //echo $dite_id, $akce_fk, $nazev, $diteakce_id, $jmeno, $prijmeni, $adresa, $narozeni, $rodic1, $telefonrod1, $rodic2, $telefonrod2, $email, $plavec, $doprava, $platba, $vernostni, $sourozenec, $kamarad, $kamaradjmeno, $zdravi, $poznamka;

$prihlaska->execute($data);

   



    
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
                <li><a href="Vzkaz.php">Vzkazy</a></li>
                <li><a href="Fotoalbum.php">Fotoalbum</a></li>
            </ul>
            
        </nav>
        <div class="account">
       
        <!-- Profile section with hover effect -->
<div class="profile-dropdown">
    <div class="profile">
        <span><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; ?></span>
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
    
       
    
    
    <style>
  /* Chrome, Safari, Edge, Opera - vypnuti number sipek*/
  input::-webkit-outer-spin-button,
  input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }

  /* Firefox - vypnuti number sipek*/
  input[type=number] {
    -moz-appearance: textfield;
  }
  </style>
  
  


  <div class="title-section">
  <h1 class="title">Přihláška</h1><br>
  <div class="vycentrovany-obsah">
    <p><b>
	  Zde se můžeš na chystanou akci přihlásit.
     <br> 
      Stačí vyplnit tuto přihlášku a po jejím zpracování tě budeme kontaktovat emailem s dalšími informacemi, jak máš dále postupovat.
	</p></b>
  </div>
</div>


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
    <input type="number" id="psc" name="psc" placeholder="PSČ" maxlength="5" min="10000" max="99999" required>
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
    <input type="text" inputmode="numeric" id="telefonrod1" name="telefonrod1" placeholder="Telefon" pattern="\d*" maxlength="9" min="100000000" max="999999999" required>
    <br><small>Telefon vyplňujte bez předvolby (+420) a bez mezer.</small>
    <br><br>
    <label>Jméno a kontaktní telefon:</label><br>
    <input type="text" id="jmenorod2" name="jmenorod2" placeholder="Jméno" maxlength="20">
    <input type="text" id="prijmenirod2" name="prijmenirod2" placeholder="Příjmení" maxlength="25">
    <input type="text" inputmode="numeric" id="telefonrod2" name="telefonrod2" placeholder="Telefon" pattern="\d*" maxlength="9" min="100000000" max="999999999">
     
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
 
 
 <!-- Registrační formulář -->
    <div class="register-form-container" id="registerForm">
        <form action="Index.php" method="POST">
        <input type="hidden" name="action" value="register">

            <h2>Registrace</h2>
            <label for="username">Uživatelské jméno</label>
            <input type="text" id="username" name="username" placeholder="Zadejte uživatelské jméno" required>
            
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Zadejte email" required>
            
            <label for="password">Heslo</label>
            <div class="new-password-container">
                <input type="password" id="password" name="password" placeholder="Zadejte heslo" required>
                <span id="toggleNewPassword" class="toggle-password">&#128065;</span> <!-- Ikona oka pro nový heslo -->
            </div>
            
            <button type="submit">Registrovat se</button>
            <button type="button" onclick="closeRegisterForm()">Zavřít</button>
            <p>Již máte účet? <a href="#" onclick="openForm()">Přihlaste se zde</a></p>


            

        </form>
    </div>




    <script>
        function openForm() {
            document.getElementById("loginForm").style.display = "flex";
            console.log("kookt");
            closeRegisterForm();
        }
        function closeForm() {
            document.getElementById("loginForm").style.display = "none";
        }
        function openRegisterForm() {
            document.getElementById("registerForm").style.display = "flex";
            closeForm(); // Zavře přihlašovací formulář
        }
        
        function closeRegisterForm() {
            document.getElementById("registerForm").style.display = "none";
        }
         // Ujistíme se, že formulář je skrytý po načtení stránky
         window.onload = function() {
            document.getElementById("loginForm").style.display = "none";
            document.getElementById("registerForm").style.display = "none";
            document.getElementById("prihlaska").style.display = "none";
        };

        document.getElementById('togglePassword').addEventListener('click', function () {
    const passwordField = document.getElementById('password');
    const type = passwordField.type === 'password' ? 'text' : 'password';
    passwordField.type = type;

    this.textContent = type === 'password' ? '👁️' : '🙈';
});

// Zobrazení a skrytí hesla pro registrační formulář
document.getElementById('toggleNewPassword').addEventListener('click', function () {
            const newPasswordField = document.getElementById('new-password');
            const type = newPasswordField.type === 'password' ? 'text' : 'password';
            newPasswordField.type = type;

            this.textContent = type === 'password' ? '👁️' : '🙈';
        });



  // Detect the scroll event and change the header's background color
window.addEventListener('scroll', function() {
    const header = document.getElementById('header');
    if (window.scrollY > 0) {
        header.classList.add('scrolled'); // Add 'scrolled' class when user scrolls down
    } else {
        header.classList.remove('scrolled'); // Remove 'scrolled' class when at the top
    }
});



document.addEventListener("DOMContentLoaded", function () {
    const dropdownAkce = document.querySelector(".dropdown_akce");
    const buttonAkce = document.querySelector(".dropdown_akce .dropdown-button");

    // Toggle the dropdown menu on button click
    buttonAkce.addEventListener("click", function () {
        dropdownAkce.classList.toggle("active");
    });

    // Close dropdown if clicked outside
    document.addEventListener("click", function (e) {
        if (!dropdownAkce.contains(e.target)) {
            dropdownAkce.classList.remove("active");
        }
    });
});



    </script>



 
 
    
</body>
</html>
