<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'Database.php';

$conn = connectToDatabase();

// Kontrola, zda byl odeslán název akce
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['akce'])) {
    $nazevAkce = $conn->real_escape_string($_POST['akce']);

    // Načtení detailů vybrané akce
    $sql = "SELECT * FROM akce WHERE nazev = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nazevAkce);
    $stmt->execute();
    $result = $stmt->get_result();

    // Zpracování výsledků
    if ($result->num_rows > 0) {
        $akce = $result->fetch_assoc();
    } else {
        echo "Akce nenalezena.";
        exit;
    }
    $stmt->close();
}else {
    echo "Žádná akce nebyla vybrána.";
    exit;
     
} 


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="akce_info.css">

    <title>Akce_info</title>
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
                <li><a href="#">Dovednosti</a></li>
                <li><a href="#">Vzkazy</a></li>
                <li><a href="#">Fotoalbum</a></li>
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

    <div class="container">
    <!-- Zobrazení informací o vybrané akci -->
    <?php if (isset($akce)): ?>
        <h1 class="event-title"><?php echo htmlspecialchars($akce['nazev']); ?></h1>
        
        <div class="event-details">
            <p><strong>Místo:</strong> <?php echo htmlspecialchars($akce['misto']); ?></p>
            <p><strong>Téma:</strong> <?php echo htmlspecialchars($akce['tema']); ?></p>
            <p><strong>Datum uzávěrky:</strong> <?php echo htmlspecialchars($akce['datum_uzaverky']); ?></p>
            
            <p><strong>Popis:</strong> <?php echo html_entity_decode($akce['popis']); ?></p>

            <p><strong>Datum konání:</strong> od <?php echo htmlspecialchars($akce['datum_od']); ?> do <?php echo htmlspecialchars($akce['datum_do']); ?></p>
        </div>

        <div class="event-packing">
            <h3>Co s sebou:</h3>
            <p><?php echo html_entity_decode($akce['cosebou']); ?></p>
        </div>

        <div class="event-info">
            <h3>Další informace:</h3>
            <p><?php echo html_entity_decode($akce['dalsi_info']); ?></p>
        </div>

        <a href="akce.php" class="btn-back">Zpět na seznam akcí</a>
    <?php endif; ?>
</div>


       
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