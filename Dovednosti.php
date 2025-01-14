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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="dovednosti.css">

    <title>Document</title>
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
                <li><a href="Fotoalbum.php">Fotoalbum</a></li>
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

    <div id="sidebar">
    <ul>
        <li><a href="#morseovka">Morseovka</a></li>
        <li><a href="#polsky_klic">Velký polský klíč</a></li>
        <li><a href="#amb_uzel">Ambulanční uzel</a></li>
        <li><a href="#skotovy_uzel">Škotový uzel</a></li>
        <li><a href="#rybarsky_uzel">Rybářský uzel</a></li>
        <li><a href="#draci_smycka">Dračí smyčka</a></li>
        <li><a href="#lodni_smycka">Lodní smyčka</a></li>
        <li><a href="#zkracovacka">Zkracovačka</a></li>
    </ul>
</div>





<section class="skills-section">
    <h2 class="section-title">Dovednosti</h2>
    <p class="section-subtitle">Tyto dovednosti se naučíte na našem táboře.</p>
    
    <div id="morseovka" class="skill">
        <h3 class="skill-title">Morseovka</h3>
        <img src="/images/morseovka.gif" alt="Morseovka" class="large-image">
    </div>
    
    <div id="polsky_klic" class="skill">
        <h3 class="skill-title">Velký polský klíč</h3>
        <img src="/dovednosti/velpolkl1.gif" alt="Velký polský klíč krok 1" class="large-image">
        <img src="/dovednosti/m1.gif" alt="Velký polský klíč krok 2" class="large-image">
    </div>

    <div id="uzle" class="skill">
        <h3 class="skill-title">Uzle</h3>
        
        <div id="amb_uzel" class="knot">
            <h4 class="knot-title">Ambulanční uzel</h4>
            <img src="/images/ambulantní-spojka1.jpg" alt="Ambulanční uzel" class="large-image">
            <p class="knot-description">Používá se k upevňování obvazů, dlah, atd., protože je plochá (netlačí) a částečně se sama tahem povoluje.</p>
        </div>

        <div id="skotovy_uzel" class="knot">
            <h4 class="knot-title">Škotový uzel</h4>
            <img src="/images/škotová-spojka1.jpg" alt="Škotový uzel" class="large-image">
            <p class="knot-description">Dříve se používala na lodích k vázání tzv. škotové plachty (odtud název), jeden z nejpevnějších uzlů, lze s ním vázat i nestejně silná lana (lano – šátek,…), v tahu se sama ještě více dotahuje.</p>
        </div>

        <div id="rybarsky_uzel" class="knot">
            <h4 class="knot-title">Rybářský uzel</h4>
            <img src="/images/rybářská-spojka1.jpg" alt="Rybářský uzel" class="large-image">
            <p class="knot-description">Je časově náročná, ale zato pevná a neprokluzuje. Hodí se k vázání hladkých lan, vlasců, strun, atd. Jen se na ni neptejte žádného rybáře – nezná ji.</p>
        </div>

        <div id="draci_smycka" class="knot">
            <h4 class="knot-title">Dračí smyčka</h4>
            <img src="/images/dračí-smyčka1.jpg" alt="Dračí smyčka" class="large-image">
            <p class="knot-description">Pevné oko, v podstatě obrácená škotová spojka. Dokáže udržet člověka na skále stejně dobře jako omotat kmen stromu stahovaného z lesa.</p>
        </div>

        <div id="lodni_smycka" class="knot">
            <h4 class="knot-title">Lodní smyčka</h4>
            <img src="/images/lodní-smyčka1.jpg" alt="Lodní smyčka" class="large-image">
            <p class="knot-description">Používají ji nejenom námořníci, ale i např. horolezci k upevnění lana na slaňování, skautíci pro stavbu lanovek všeho druhu. Zkrátka všude, kde je potřeba pevně přivázat lano ke kolíku, stromu nebo čemukoliv jinému.</p>
        </div>

        <div id="zkracovacka" class="knot">
            <h4 class="knot-title">Zkracovačka</h4>
            <img src="/images/zkracovačka1.jpg" alt="Zkracovačka" class="large-image">
            <p class="knot-description">Ideální uzel nejenom na zkracování příliš dlouhého lana, ale i na výrobu pout na lotry i jiné zlosyny všeho druhu.</p>
        </div>
    </div>
</section>


 <!-- Formulář jako modální okno -->
    <div class="login-form-container" id="loginForm">
        <form action="Index.php" method="POST">
        <input type="hidden" name="action" value="login">

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
            </p>
        </form>
    </div>

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
      document.querySelectorAll('#sidebar a').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();

        const target = document.querySelector(this.getAttribute('href'));

        // Získání pozice cílového prvku
        const offset = target.getBoundingClientRect().top + window.scrollY;

        // Posun o vlastní odsazení (např. kvůli fixnímu headeru)
        const headerHeight = document.querySelector('header').offsetHeight || 0;

        // Plynulé scrollování
        window.scrollTo({
            top: offset - headerHeight - 10, // Posun o header a malý odstup
            behavior: 'smooth'
        });
    });
});

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