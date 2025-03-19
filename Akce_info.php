<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Načtení souborů
require 'Database.php';
require 'Functions.php';
require_once 'vendor/autoload.php';
require_once 'config.php';

// Připojení k databázi
$conn = connectToDatabase();
$conn->set_charset("utf8mb4");

// Získání aktuálního uživatele
$currentUsername = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

// Zpracování registrace
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['register'])) {
    $message = registerUser($conn, $_POST['new_username'], $_POST['email'], $_POST['new_password']);
    echo $message;
    header("Location: Akce.php");
    exit();
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


// Kontrola, zda byl odeslán název akce
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['akce'])) {
    //$nazevAkce = $conn->real_escape_string($_POST['akce']);
    $zvolenaAkce = htmlspecialchars($conn->real_escape_string($_POST['akce']));
    // Načtení detailů vybrané akce
    //$sql = "SELECT * FROM akce WHERE nazev = ?";
    //$stmt = $conn->prepare($sql);
    //$stmt->bind_param("s", $nazevAkce);
    //$stmt->execute();
    //$result = $stmt->get_result();

    // Zpracování výsledků
    //if ($result->num_rows > 0) {
       // $akce = $result->fetch_assoc();
    //} else {
      //  echo "Akce nenalezena.";
      //  exit;
   // }
    //$stmt->close();
    
    $akce = mysqli_fetch_array($conn->query("SELECT * FROM akce WHERE nazev = '$zvolenaAkce'"));
    $nazevAkce = $akce['nazev']; 
    
    
}else {
    //echo "Žádná akce nebyla vybrána.";
    //exit;
    //$akce_id = ($_GET["akce_id"]);
    $akce_id = isset($_GET["akce_id"]) ? htmlspecialchars($_GET["akce_id"]) : "";
    
    $akce_id = substr($akce_id, 0, 2);
    
    //$akce_id = htmlspecialchars(substr($akce_id, 0, 2)); // Získá první dva znaky
    
    $akce = mysqli_fetch_array($conn->query("SELECT * FROM akce WHERE akce_id = $akce_id"));
    $nazevAkce = $akce['nazev'];

     
} 






$conn->close();


?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="akce_info.css">
    <script src="https://kit.fontawesome.com/ca23847823.js" crossorigin="anonymous"></script>


    <title>BezvaTábor - <?php echo $nazevAkce; ?></title>
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


    <div class="container">
    <!-- Zobrazení informací o vybrané akci -->
    <?php if (isset($akce)): ?>
        <h1 class="event-title"><?php echo html_entity_decode($akce['nazev']); ?></h1>
        
        <div class="event-details">
            <p><strong>Místo:</strong> <?php echo htmlspecialchars($akce['misto']); ?></p>
            <p><strong>Téma:</strong> <?php echo html_entity_decode($akce['tema']); ?></p>
            <p><strong>Datum konání:</strong> od <?php echo date('j.n.Y',strtotime($akce['datum_od'])); ?> do <?php echo date('j.n.Y',strtotime($akce['datum_do'])); ?></p>
            <p><strong>Popis:</strong> <?php echo html_entity_decode($akce['popis']); ?></p>

        </div>

        <div class="event-packing">
            <h3>Co s sebou:</h3>
            <p><?php echo html_entity_decode($akce['cosebou']); ?></p>
        </div>

        <div class="event-info">
            <h3>Další informace:</h3>
            <p><?php echo html_entity_decode($akce['dalsi_info']); ?></p>
        </div>

        <a href="Akce.php" class="btn-back">Zpět na seznam akcí</a>
    <?php endif; ?>
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
        <form action="Akce_info.php" method="POST">
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
        <form action="Akce_info.php" method="POST">
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
   function onSignIn(googleUser) {
  var profile = googleUser.getBasicProfile();
  console.log('ID: ' + profile.getId()); // Do not send to your backend! Use an ID token instead.
  console.log('Name: ' + profile.getName());
  console.log('Image URL: ' + profile.getImageUrl());
  console.log('Email: ' + profile.getEmail()); // This is null if the 'email' scope is not present.
}
</script>

    <script>
         document.addEventListener('DOMContentLoaded', () => {
    console.log("Fetching messages on page load...");
    fetchMessages(); // Fetch messages as soon as the page loads
    setInterval(fetchMessages, 3000); // Continue fetching every 3 seconds
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



async function fetchMessages() {
    console.log("Fetching messages...");
    try {
        const response = await fetch('chat_backend.php');
        if (!response.ok) {
            console.error('Error fetching messages:', response.statusText);
            return;
        }

        const messages = await response.json();
        const chatMessages = document.getElementById('chat-messages');
        chatMessages.innerHTML = ''; // Clear existing messages

        messages.reverse().forEach(msg => {
            const messageDiv = document.createElement('div');
            messageDiv.innerHTML = `<span class="user">${msg.user_name || 'Anonym'}:</span> ${msg.message}`;
            chatMessages.appendChild(messageDiv);
        });
    } catch (error) {
        console.error('Error processing messages:', error);
    }
}





async function sendMessage() {
    const message = document.getElementById('message').value;

    if (!message) {
        alert('Zpráva nemůže být prázdná.');
        return;
    }

    try {
        const response = await fetch('chat_backend.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ message: message })
        });

        const result = await response.json();
        if (result.status === 'success') {
            document.getElementById('message').value = ''; // Clear the input field
            fetchMessages(); // Refresh the chat
        } else {
            alert('Chyba: ' + result.message);
        }
    } catch (error) {
        console.error('Chyba při odesílání zprávy:', error);
        alert('Zprávu se nepodařilo odeslat.');
    }
}


function toggleChat() {
    const chatContainer = document.getElementById('chat-container');
    chatContainer.classList.toggle('open');
}


    </script>
</body>
</html>
