<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


    require 'Database.php';
    require 'Functions.php';
    require_once 'vendor/autoload.php';
    require_once 'config.php';
    



// Připojení k databázi
$conn = connectToDatabase();
$conn->set_charset("utf8mb4");

    if (!$conn) {
        die('Chyba připojení k databázi.');
    }




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




    
    // nejvyšší číslo akce  
    $akce = mysqli_fetch_array($conn->query("SELECT akce_fk FROM aktualita ORDER BY akce_fk DESC LIMIT 1"));
    $cislo_akce = $akce ['akce_fk'];
    
    // nejvyšší číslo aktuality u dané akce
    $akce = mysqli_fetch_array($conn->query("SELECT aktualita_id FROM aktualita WHERE akce_fk = $cislo_akce ORDER BY aktualita_id DESC LIMIT 1"));
    $cislo_aktualita = $akce ['aktualita_id'];
    
    
      
   if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $nova_aktualita = $conn->prepare("INSERT INTO aktualita (aktualita_id, akce_fk, nadpis, text, datum) 
    VALUES (?, ?, ?, ?, ?)");


   

    $data = [
      $aktualita_id =  $_POST['cislo_aktuality'],
      $akce_fk = $_POST['cislo_akce'],
      $nadpis = $_POST['nadpis'],
      $text = $_POST['text'],
      $datum = $_POST["datum"],
      
    ];
   

    
        $nova_aktualita->execute($data);
    
    
    

    header("Location: Index.php"); // Přesměrování na aktuální stránku
    exit();

    
    $conn->close();
}



?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=, initial-scale=1.0">
    <link rel="stylesheet" href="nova_aktualita.css">

    <title>BezvaTábor - admin - aktuality</title>
</head>
<body>
    
   
    <!-- Hlavička s navigací -->
    <header>
        
    <div class="title">
    <img src="images/nadpis/nadpis.png" alt="">
</div>


        
        <nav>
            <ul>
                <li><a href="Index.php" target="_self">Úvod</a></li>
                <li><a href="Poradatel.php" target="_self">Pořadatel</a></li>
                <li><a href="Akce.php">Akce</a></li>
                <li><a href="Dovednosti.php">Dovednosti</a></li>
                <li><a href="Vzkaz.php">Vzkazy</a></li>
                <li><a href="Fotoalbum.php">Fotoalbum</a></li>
                
                <?php if ($currentUsername == 'admin'): ?>
        <li class="dropdown">
            <a href="#">Administrace</a>
            <ul class="dropdown-menu">
                <li><a href="Nova_aktualita.php">Vytvořit aktualitu</a></li>
                <li><a href="Vytvorit_akci.php">Vytvořit akci</a></li>
            </ul>
          </li>
        <?php endif; ?>

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
  <h1 class="title">Nová aktualita</h1><br>
   <div class="vycentrovany-obsah">
    <p><b>
	  Zde můžeš zapsat novou aktualitu.
    </p></b>
  </div>
</div>


<form name="nova_aktualita" id="nova_aktualita" method="POST" onsubmit="handleFormSubmit(event)"  >

    <p><i>Pole označené <b style="color: red; font-size: 20px;">*</b> jsou povinné. Bez jejich vyplnění nelze přihlášku odeslat.</i></p>
    <hr>

    <!-- Číslo aktuality -->
    <label for="cislo_aktuality"><b>Číslo aktuality:</b> <b style="color: red; font-size: 20px;">*</b></label>
    <input type="number" id="cislo_aktuality" name="cislo_aktuality" placeholder="Poslední číslo aktuality je <?php echo $cislo_aktualita; ?>" maxlength="3" required>
    <br><br>
    
    <!-- Číslo akce -->
    <label for="cislo_akce"><b>Číslo akce:</b> <b style="color: red; font-size: 20px;">*</b></label>
    <input type="number" id="cislo_akce" name="cislo_akce" placeholder="Poslední číslo akce je <?php echo $cislo_akce; ?>" maxlength="3" required>
    <br><br>
    
    
    <!-- Nadpis -->
    <label for="nadpis"><b>Nadpis:</b> <b style="color: red; font-size: 20px;">*</b></label>
    <input type="text" id="nadpis" name="nadpis" placeholder="Nadpis aktuality" maxlength="30" required>
    <br><br>

    <!-- Text aktuality -->
    <label><b>Text:</b> <b style="color: red; font-size: 20px;">*</b></label>
    <textarea id="text" name="text" rows="10" maxlength="2000" placeholder="Text aktuality" required></textarea>
    <br><br>

    <!-- Datum aktuality -->
    <label for="datum"><b>Datum aktuality:</b> <b style="color: red; font-size: 20px;">*</b></label>
    <input type="date" id="datum" name="datum" >
    <br><br>

 

    <!-- Odeslat -->
    <input type="submit" name="poslat" value="Odeslat aktualitu">
</form>
 


 
 <!-- prihlasovaci formular -->
 <div class="login-form-container" id="loginForm">
    <form action="Nova_aktualita.php" method="POST">
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
        <form action="Nova_aktualita.php" method="POST">
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

         document.addEventListener('DOMContentLoaded', () => {
    console.log("Fetching messages on page load...");
    fetchMessages(); // Fetch messages as soon as the page loads
    setInterval(fetchMessages, 3000); // Continue fetching every 3 seconds
});






function onSignIn(googleUser) {
  var profile = googleUser.getBasicProfile();
  console.log('ID: ' + profile.getId()); // Do not send to your backend! Use an ID token instead.
  console.log('Name: ' + profile.getName());
  console.log('Image URL: ' + profile.getImageUrl());
  console.log('Email: ' + profile.getEmail()); // This is null if the 'email' scope is not present.
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
