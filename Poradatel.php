<?php
session_start(); // Zahájí práci se session

// Kontrola přihlášení
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username']; // Uživatel je přihlášen
} else {
    $username = 'Guest'; // Výchozí hodnota pro nepřihlášené
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pořadatel</title>
    <link rel="stylesheet" href="poradatel.css">
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
                <li><a href="#">Akce</a></li>
                <li><a href="#">Dovednosti</a></li>
                <li><a href="#">Vzkazy</a></li>
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


    </div>

    </header>

     <!-- Formulář jako modální okno -->
     <div class="login-form-container" id="loginForm">
        <form>
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
        <form>
            <h2>Registrace</h2>
            <label for="new-username">Uživatelské jméno</label>
            <input type="text" id="new-username" name="new-username" placeholder="Zadejte uživatelské jméno" required>
            
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Zadejte email" required>
            
            <label for="new-password">Heslo</label>
            <div class="new-password-container">
                <input type="password" id="new-password" name="new-password" placeholder="Zadejte heslo" required>
                <span id="toggleNewPassword" class="toggle-password">&#128065;</span> <!-- Ikona oka pro nový heslo -->
            </div>
            
            <button type="submit">Registrovat se</button>
            <button type="button" onclick="closeRegisterForm()">Zavřít</button>
            <p>Již máte účet? <a href="#" onclick="openForm()">Přihlaste se zde</a></p>

        </form>
    </div>



                 <!-- Div s informacemi -->
    <div class="Info">
        <h2>Pořadatel</h2>
        <p>Chcete jet s námi na letní tábor, ale nevíte, kdo vlastně jsme? Pak jsi na správném místě. 
        Kontakty na vedoucí najdeš u jednotlivých námi pořádaných akcí.</p>
        <br>
        <p>Původně byly tábory pořádány Městským klubem Votice, po jeho převedení pod Město Votice jejich pořádání převzalo Město. Úplně první tábor byl v Jablonné, další tábor byl na Kačinách a pak následovaly Střížovice.<br></br>
        Poté, co tábořiště koupila soukromá osoba, přestaly ceny být pro tábor únosné a místo už jsme také měli okoukané. Vybrali jsme na čtrnáct dní (poprvé) tábor u stádleckého mostu, který však byl poničen záplavami, takže jsme na poslední chvíli a jen na týden sehnali Vestec u Slapské přehrady. Od roku 2003 již pořádáme tábory v délce 14 dní.</p>
    </div>

    <div class="Kontakty">
        <h2>Kontakty</h2>
        <p>Bezva Tábor z.s. <br>
        Smilkov 50 <br>
        257 89 Heřmaničky <br></br>
        Kontakt: Karel BUKY Bukovský karel@bezvatabor.cz <br>
        Kontakt: Aleš ALI Kovařík ali@bezvatabor.cz <br></br>

        E-mail - informace@bezvatabor.cz <br>
        WWW - http://www.bezvatabor.cz/os <br></br>

        do roku 2003 ve spolupráci s <br>
        Městským kulturním centrem <br>
        Komenského náměstí 177 <br>
        259 01 Votice <br></br>
        Kontakt: Jan Žaloudek

            </p>
     </div>
   











    <script>
        function openForm() {
            document.getElementById("loginForm").style.display = "flex";
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
            const newPasswordField = document.getElementById('new-password');
            const type = newPasswordField.type === 'password' ? 'text' : 'password';
            newPasswordField.type = type;

            this.textContent = type === 'password' ? '👁️' : '🙈';
        });


    </script>
</body>
</html>