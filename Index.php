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

$conn->close();
?>


<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BezvaTábor</title>
    <link rel="stylesheet" href="index.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
   
    
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
    
                
    
    <div class="UvodPanel">
    <h2>Vítejte na stránkách bezva tábora!</h2>
    <p>Ahoj holky a kluci! Vítáme vás na internetových stránkách vašeho oblíbeného bezva tábora. Najdete tu nejen zajímavé informace pro vás, ale i pro vaše rodiče.</p>
    <p>Doufáme, že se vám budou hodit a těšíme se, že se s vámi na některé z námi pořádaných akcí brzy uvidíme!</p> 
</div>

    <br></br>

    <div class="Aktualita">
        
    </div>

 <!-- prihlasovaci formular -->
 <div class="login-form-container" id="loginForm">
    <form action="Index.php" method="POST">
        <input type="hidden" name="login" value="true">

        <h2>Přihlášení</h2>
        
        <label for="username">Uživatelské jméno</label>
        <input type="text" id="username" name="username" placeholder="Zadejte uživatelské jméno" required>
        
        <label for="password">Heslo</label>
        <div class="password-container">
            <input type="password" id="password" name="password" placeholder="Zadejte heslo" required>
        </div>
        
        <button type="submit">Přihlásit se</button>
        <button type="button" onclick="closeForm()">Zavřít</button>
        <p>Nemáte účet? <a href="#" onclick="openRegisterForm()">Registrovat se</a></p>
    </form>
</div>


    <!-- Registrační formulář -->
    <div class="register-form-container" id="registerForm">
    <form action="Index.php" method="POST">
        <input type="hidden" name="register" value="true">

        <h2>Registrace</h2>
        <label for="new_username">Uživatelské jméno</label>
        <input type="text" id="new_username" name="new_username" placeholder="Zadejte uživatelské jméno" required>
        
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Zadejte email" required>
        
        <label for="new_password">Heslo</label>
        <div class="new-password-container">
            <input type="password" id="new_password" name="new_password" placeholder="Zadejte heslo" required>
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


    </script>



   
   
</body>
</html>


