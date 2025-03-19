<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

    require 'Database.php';
    require 'Functions.php';
    require_once 'vendor/autoload.php';
    require_once 'config.php';
    
// Funkce pro zápis do logu
function zapisLog($zprava) {
    $soubor = 'prihlaska.log'; // Název logovacího souboru
    $cas = date('Y-m-d H:i:s'); // Aktuální čas
    $logZaznam = "[$cas] $zprava" . PHP_EOL; // Formát záznamu

// Zápis do souboru (přidání na konec)
    file_put_contents($soubor, $logZaznam, FILE_APPEND | LOCK_EX);
    
}


// vyčtení čísla akce z řádku
$akce_fk = htmlspecialchars($_GET["akce_id"]);
//echo $akce_fk . ";";
    
    // Připojení k databázi
$conn = connectToDatabase();
$conn->set_charset("utf8mb4");

    if (!$conn) {
        die('Chyba připojení k databázi.');
    }

// Generování slova pro zápis do DB
$gencislo = rand(1,8);

switch ($gencislo) {
  case 1: $zapis = "želva"; break;
  case 2: $zapis = "čmelák"; break;
  case 3: $zapis = "včela"; break;
  case 4: $zapis = "hruška"; break;
  case 5: $zapis = "švestka"; break;
  case 6: $zapis = "řeřicha"; break;
  case 7: $zapis = "žralok"; break;
  case 8: $zapis = "čutora"; break;
}


// Získání aktuálního uživatele
$currentUsername = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

// Zpracování registrace
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['register'])) {
    $message = registerUser($conn, $_POST['new_username'], $_POST['email'], $_POST['new_password']);
    echo $message;
    header("Location: Akce.php");
    exit();
}
$existujiciJmena = [];
$query = "SELECT uzivatelske_jmeno FROM ucet";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $existujiciJmena[] = $row['uzivatelske_jmeno'];
}
// Zpracování přihlášení
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
    $result = loginUser($conn, $_POST['username'], $_POST['password']);
    if ($result === true) {
        header("Location: Akce.php"); // Přesměrování na aktuální stránku
        exit();
    } else {
        echo $result; // Zobrazení chyby
    }
}

// authenticate code from Google OAuth Flow
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token['access_token']);

    // get profile info
    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();

    $google_id = $google_account_info->id;
    $email = $google_account_info->email;
    $name = $google_account_info->name;
    $profile_picture = $google_account_info->picture;

    // Check if user exists in the database
    $query = "SELECT * FROM google_login WHERE google_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $google_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User exists, fetch their username
        $user = $result->fetch_assoc();
        $_SESSION['username'] = $user['username'];
        $_SESSION['profile_picture'] = $user['profile_picture']; // Uložení obrázku do session

    } else {
        // User does not exist, insert new record
        $insert_query = "INSERT INTO google_login (google_id, email, username, profile_picture) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ssss", $google_id, $email, $name, $profile_picture);
        $stmt->execute();

        // Set session username for the new user
        $_SESSION['username'] = $name;
        $_SESSION['profile_picture'] = $profile_picture; // Uložení obrázku do session

    }
    header("Location: Index.php");
    exit();
}



    
    
function vrat_pocet_deti_akce($akce_fk) {
        global $conn; 
        $pocet = $conn->query("SELECT akce_fk FROM deti WHERE akce_fk = $akce_fk");
        $pocet_deti = $pocet->num_rows;
        //echo $pocet_deti . ";";
        return $pocet_deti;
        
      }


function vrat_pocet_deti() {
        global $conn; 
        $pocet = $conn->query("SELECT dite_id FROM deti");
        $pocet_deti = $pocet->num_rows;
        //echo $pocet_deti . ";";
        return $pocet_deti;
      }    
    
   
    $akce = mysqli_fetch_array($conn->query("SELECT akce_id, nazev, tema FROM akce WHERE akce_id = $akce_fk"));
    $nazev = $akce['nazev'];
    $tema = $akce['tema'];
     
   
          
    $pocetdeti = vrat_pocet_deti();
    $dite_id = $pocetdeti + 1;

    $pocetdetiakce = vrat_pocet_deti_akce($akce_fk);
    $diteakce_id = $pocetdetiakce + 1;

     
     
   if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $prihlaska = $conn->prepare("INSERT INTO deti (dite_id, akce_fk, nazev, diteakce_id, jmeno, prijmeni, adresa, narozeni, rodic1, telefonrod1, rodic2, telefonrod2, email, plavec, doprava, platba, vernostni, sourozenec, kamarad, kamaradjmeno, zdravi, poznamka) 
    VALUES ('$dite_id', '$akce_fk', '$nazev', '$diteakce_id', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");


    if(empty($_POST['platba'])) 
        { $_POST['platba']="";}



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



        $prihlaska->execute($data);
        
        zapisLog("data zapsána");
        zapisLog($jmeno . " " . $prijmeni . "," . $rodic1 . "," . $telefonrod1 . "," . $email );
        
        
        $_SESSION['submitted_email'] = $_POST['email']; // Uložení emailu do session

        header("Location: Prihlaska_poslana.php"); // Přesměrování na aktuální stránku
        exit();
   
    

    
    $conn->close();
 
}




?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=, initial-scale=1.0">
    <link rel="stylesheet" href="prihlaska.css">
    <script src="https://kit.fontawesome.com/ca23847823.js" crossorigin="anonymous"></script>

    <title>BezvaTábor - Přihláška na <?php echo $nazev; ?></title>
</head>
<body>
    
   
    <!-- Hlavička s navigací -->
    <header>
        
    <div class="title">
    <img src="images/nadpis/nadpis.png" alt="">
</div>



    <div class="menu-toggle">&#9776;</div>

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
        <img src="<?= isset($_SESSION['profile_picture']) && $_SESSION['username'] !== 'Guest' 
            ? htmlspecialchars($_SESSION['profile_picture']) 
            : 'images/default_profile.png' ?>" 
            alt="Profile Picture" 
            class="profile-pic">
        <span><?= htmlspecialchars($currentUsername) ?></span>
    </div>


    <!-- Dropdown menu for login/logout -->
    <div class="dropdown">
       
        <div class="dropdown-content">
            <?php if (isset($_SESSION['username'])): ?>
                <a href="Profil.php">Profil</a>
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
    
      
    <div class="sidebar">
    <div class="sidebar-logo">
        <img src="images/logoBAT.png" alt="Logo Bezvatábor">
    </div>
        <ul>
                <li><a href="Index.php" target="_self">Úvod</a></li>
                <li><a href="Poradatel.php" target="_self">Pořadatel</a></li>
                <li><a href="Akce.php">Akce</a></li>
                <li><a href="Dovednosti.php">Dovednosti</a></li>
                <li><a href="Vzkaz.php">Vzkazy</a></li>
                <li><a href="Fotoalbum.php">Fotoalbum</a></li>
                <li><a href="#"><?php if ($currentUsername == 'admin')  { echo ' Administrace ';} ?> </a></li>
        </ul>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
    console.log('JavaScript načten');

    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');

    if (!menuToggle || !sidebar) {
        console.error('Chyba: Některé prvky nenalezeny');
        return;
    }

    menuToggle.addEventListener('click', function() {
        console.log('Kliknuto na menu');
        sidebar.classList.toggle('active');
    });

   
    // Kliknutí mimo sidebar zavře menu
    document.addEventListener('click', function(event) {
        if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
            sidebar.classList.remove('active');
        }
    });
});

</script>

    
  
 
<div class="logo-container">
    <div class="logo-background">
        <img src="images/logoBAT.png" alt="Logo BAT">
    </div>
</div>

       
    
    
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
  <h1 class="title">Přihláška na <?php echo $nazev; ?></h1><br>
  <h2> " <?php echo $tema; ?> "</h2><br><br>
  <div class="vycentrovany-obsah">
    <p><b>
	  Zde se můžeš na chystanou akci přihlásit.
     <br> 
      Stačí vyplnit tuto přihlášku a po jejím zpracování tě budeme kontaktovat emailem s dalšími informacemi, jak máš dále postupovat.
	</p></b>
  </div>
</div>




<form name="prihlaska" id="prihlaska" method="POST" onsubmit="handleFormSubmit(event)" action="Prihlaska.php?akce_id=<?php echo $akce_fk; ?>" >

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
    <br>
    <!-- Sekce rodiče -->
    <p><b style="color: red;">Další část přihlášky nech vyplnit rodiče.</b></p>
    <br>
   
    <!-- Spojení na rodiče -->
    <label><b>Spojení na rodiče / zákonné zástupce:</b></label><br>
    <label>Jméno a kontaktní telefon: <b style="color: red; font-size: 20px;">*</b></label><br>
    <input type="text" id="jmenorod1" name="jmenorod1" placeholder="Jméno" maxlength="20" required>
    <input type="text" id="prijmenirod1" name="prijmenirod1" placeholder="Příjmení" maxlength="25" required>
    <input type="text" inputmode="numeric" id="telefonrod1" name="telefonrod1" placeholder="Telefon" pattern="\d*" maxlength="9" min="100000000" max="999999999" required>
    <br><small>Telefon vyplňujte bez předvolby (+420) a bez mezer.</small>
    <br><br><br>
    <p><a style="color: grey;">Doporučujeme vyplnit i druhý kontakt, z důvodu případné nedostupnosti prvního kontaktu.</a></p>
    
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
    <br><br><br>

    <!-- Místo odjezdu -->
    <label><b>Pojede z odjezdového místa:</b> <b style="color: red; font-size: 20px;">*</b></label><br>
    <input type="radio" name="doprava" value="Ústí n.L." required> Ústí nad Labem
    <input type="radio" name="doprava" value="Praha" required> Praha
    <input type="radio" name="doprava" value="Votice" required> Votice
    <br><br><br>

    <!-- Platba -->
    <label><b>Platit budeme:</b></label><br>
    <input type="radio" name="platba" value="Převodem"> Převodem
    <input type="radio" name="platba" value="Fakturou"> Fakturou
    <small>(Pro zaměstnavatele k využití příspěvku FKSP.)</small>
    <br><br>

    <small><i>   - Po dohodě s námi lze platbu za tábor rozdělit i na více plateb.</small><br>
    <small>   - Podrobnosti k vytvoření faktury si domluvíme emailem.</small></i>
    <br><br><br>

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
    <input type="checkbox" name="kamarad" value="ANO"> Kamarád <br><br>
    <input type="text" name="kamaradjmeno" placeholder="Při slevě Kamarád vyplňte jméno kamaráda/ky" maxlength="30">
    <br><a href="Sleva.php" target="_blank">Informace ke slevám</a>
    <br><br><br>

    <!-- Souhlas -->
    <label><b>Potvrzení údajů:</b><b style="color: red; font-size: 20px;">*</b></label><br>
    <input type="checkbox" name="podminky" value="ANO" required> Prohlašuji, že jsem řádně a pravdivě vyplnil(a) veškeré údaje.<br><br>
    <input type="checkbox" name="souhlas" value="ANO" required> Seznámil(a) jsem se s <a href="Podminky.php" target="_blank">informacemi pro rodiče</a>.
    <br><br><br>

    <!-- Antispam -->
    <label for="pirat"><b>Ochrana proti SPAM robotům:</b></label><br>
    <input type="text" id="pirat" name="pirat" placeholder="Napište slovo <?php echo $zapis; ?>" required>
    <br><br>


    <!-- Odeslat -->
    <input type="submit" name="poslat" value="Odeslat přihlášku">
</form>
 


<!-- Loading screen -->
<div id="loadingScreen" class="loading-screen">
    <div class="loading-content">
        <div class="spinner"></div>
        <p>Odesílání přihlášky...</p>
    </div>
</div>
 
 

     
<footer class="footer">
        <div class="social-icons">
            <a href="https://www.facebook.com/profile.php?id=100044762723817&locale=cs_CZ" target="_blank"><i class="fab fa-facebook-f"></i></a>
            <a href="https://www.instagram.com/bat_bezvatabor/" target="_blank"><i class="fab fa-instagram"></i></a>
            <a href="mailto:informace@bezvatabor.cz" target="_blank"><i class="fa-regular fa-envelope"></i></a>
        </div>
        <p class="footer-text">© 2025 Bezva Tábor | Všechna práva vyhrazena</p>
        <p class="footer-text">Vytvořil | Matěj Kovařík</p>


    </footer>


 
 <!-- prihlasovaci formular -->
 <div class="login-form-container" id="loginForm">
    <form action="Prihlaska.php" method="POST">
        <input type="hidden" name="login" value="true">

        <h2>Přihlášení</h2>
        
        <label for="username">Uživatelské jméno</label>
        <input type="text" id="username" name="username" placeholder="Zadejte uživatelské jméno" required>
        
        <label for="password">Heslo</label>
        <div class="password-container">
            <input type="password" id="password" name="password" placeholder="Zadejte heslo" required>
            <span id="togglePassword" class="toggle-password">&#128065;</span> <!-- Ikona oka -->
        </div>
        
        <button type="submit">Přihlásit se</button>
        <button type="button" onclick="closeForm()">Zavřít</button>
        <p class="register-link">
               <p>Nemáte účet? <a href="#" onclick="openRegisterForm()">Registrovat se</a></p>
               <p>Nebo se přihlaste pomocí Google:</p>
               <a href="<?= htmlspecialchars($client->createAuthUrl()); ?>">Login with Google</a>
        </p>
    </form>
</div>


<!-- Registrační formulář -->
    <div class="register-form-container" id="registerForm">
        <form action="Prihlaska.php" method="POST" onsubmit="return kontrolaUsername();">
        <input type="hidden" name="register" value="true">
        
            <h2>Registrace</h2>
            <label for="new_username">Uživatelské jméno</label>
            <input type="text" id="new_username" name="new_username" placeholder="Zadejte uživatelské jméno" required>
            
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Zadejte email" required>
            
            <label for="new_password">Heslo</label>
            <div class="new-password-container">
                <input type="password" id="new_password" name="new_password" placeholder="Zadejte heslo" required>
                <span id="toggleNewPassword" class="toggle-password">&#128065;</span> <!-- Ikona oka pro nový heslo -->
            </div>
            
            <button type="submit">Registrovat se</button>
            <button type="button" onclick="closeRegisterForm()">Zavřít</button>
            <p>Již máte účet? <a href="#" onclick="openForm()">Přihlaste se zde</a></p>

        </form>
    </div>

    <script>
function kontrolaUsername() {
    let username = document.getElementById("new_username").value;

    if (username === "") {
        alert("Uživatelské jméno nesmí být prázdné.");
        return false;
    }

    let existujiciJmena = <?php echo json_encode($existujiciJmena); ?>;
    
    if (existujiciJmena.includes(username)) {
        alert("Toto uživatelské jméno je již obsazené!");
        return false;
    }

    return true;
}
</script>

 
 
 

    <script>

    document.addEventListener('DOMContentLoaded', () => {
    console.log("Fetching messages on page load...");
    fetchMessages(); // Fetch messages as soon as the page loads
    setInterval(fetchMessages, 3000); // Continue fetching every 3 seconds
});



function handleFormSubmit(event) {
    event.preventDefault(); // Prevent immediate form submission
    const isValid = kontrola(); // Validate the form using kontrola()
    
    if (!isValid) return; // Stop if validation fails
    
    // Show loading screen
    showLoadingScreen();

    // Submit the form after the delay
    setTimeout(() => {
        document.getElementById('prihlaska').submit();
    }, 2000);
}



function onSignIn(googleUser) {
  var profile = googleUser.getBasicProfile();
  console.log('ID: ' + profile.getId()); // Do not send to your backend! Use an ID token instead.
  console.log('Name: ' + profile.getName());
  console.log('Image URL: ' + profile.getImageUrl());
  console.log('Email: ' + profile.getEmail()); // This is null if the 'email' scope is not present.
}

function showLoadingScreen() {
    document.getElementById('loadingScreen').style.display = 'flex'; // Show the loading screen
}
   
    function kontrola() {
	           
               if (document.prihlaska.pirat.value != '<?php echo($zapis); ?>')  {
                 alert('Špatné Heslo! Heslo je <?php echo($zapis); ?>');
                 return false;
               }
               
               return true;
             }


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
            
        };

        document.getElementById('togglePassword').addEventListener('click', function () {
    const passwordField = document.getElementById('password');
    const type = passwordField.type === 'password' ? 'text' : 'password';
    passwordField.type = type;

    this.textContent = type === 'password' ? '👁️' : '🙈';
});

// Zobrazení a skrytí hesla pro registrační formulář
document.getElementById('toggleNewPassword').addEventListener('click', function () {
            const newPasswordField = document.getElementById('new_password');
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
