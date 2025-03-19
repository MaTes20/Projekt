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

if (!$conn) {
        die('Chyba připojení k databázi.');
    }

// Získání aktuálního uživatele
$currentUsername = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';



// Zpracování registrace
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['register'])) {
    $message = registerUser($conn, $_POST['new_username'], $_POST['email'], $_POST['new_password']);
    echo $message;
    header("Location: Fotoalbum.php");
    exit();
}

// Zpracování přihlášení
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
    $result = loginUser($conn, $_POST['username'], $_POST['password']);
    if ($result === true) {
        header("Location: Fotoalbum.php"); // Přesměrování na aktuální stránku
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

if (isset($_GET['akce_id']) && is_numeric($_GET['akce_id'])) {
    $akce_id = intval($_GET['akce_id']);
    $directory = "images/fotoalbum/$akce_id/";
    $nazev = $conn->query("SELECT nazev FROM akce WHERE akce_id = $akce_id");
    $nazev_akce = $nazev->fetch_assoc()['nazev'];
} else {
    echo "<p class='gallery-empty'>Nebyla vybrána žádná akce.</p>";
}



//$conn->close();
?>



<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BezvaTábor - Fotoalbum <?php echo $nazev_akce;?></title>
    <link rel="stylesheet" href="fotoalbum_akce.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
   
    <script src="https://apis.google.com/js/platform.js" async defer></script>
    <meta name="google-signin-client_id" content="667754488994-72mh4kcvnfqkh24bs7p4b472mi03d9pf.apps.googleusercontent.com">

    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<script src="https://kit.fontawesome.com/ca23847823.js" crossorigin="anonymous"></script>

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






<?php if (isset($_SESSION['username']) && $_SESSION['username'] !== 'Guest'): ?>
    <button id="chat-toggle" onclick="toggleChat()">Chat</button>
<?php endif; ?>
    <div id="chat-container">
    <div id="chat-messages"></div>
    <div id="chat-input-container">
        <input type="text" id="message" placeholder="Napište zprávu...">
        <button onclick="sendMessage()">Odeslat</button>
    </div>
</div>

      
   

<div class="gallery-container">
    <h2 class="gallery-title">Fotoalbum <?php echo $nazev_akce;?> </h2>
    <div class="gallery-grid">
        <?php        
            if (is_dir($directory)) {
                $images = glob($directory . '*.{jpg,JPG,jpeg,png,gif}', GLOB_BRACE);
                if ($images) {
                    foreach ($images as $index => $image) {
                        echo "<div class='gallery-item'>
                                <img src='$image' alt='Gallery Image' class='gallery-thumbnail'  onclick='openModal($index)'>
                              </div>";
                    }
                    echo "<script>const images = " . json_encode($images) . ";</script>";

                } else {
                    echo "<p class='gallery-empty'>V této akci zatím nejsou žádné fotografie.</p>";
                }
            } else {
                echo "<p class='gallery-empty'>Galerie pro tuto akci neexistuje.</p>";
            }
        
        ?>
    </div>
</div>

<!-- Modal -->
<div id="galleryModal" class="modal">
    <span class="close" onclick="closeModal()">&times;</span>
    <img id="modalImage" class="modal-content" alt="Large Image">
    <span class="arrow left" onclick="changeImage(-1)">&#10094;</span>
    <span class="arrow right" onclick="changeImage(1)">&#10095;</span>
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
    <form action="Fotoalbum_akce.php" method="POST">
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
        <form action="Fotoalbum_akce.php" method="POST">
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

  let currentIndex = 0;

function openModal(index) {
    currentIndex = index;
    const modal = document.getElementById('galleryModal');
    modal.style.display = 'flex';
    updateModalImage();
}

function closeModal() {
    const modal = document.getElementById('galleryModal');
    modal.style.display = 'none';
}

function changeImage(direction) {
    currentIndex += direction;
    if (currentIndex < 0) currentIndex = images.length - 1;
    if (currentIndex >= images.length) currentIndex = 0;
    updateModalImage();
}

function updateModalImage() {
    const modalImage = document.getElementById('modalImage');
    const caption = document.getElementById('caption');
    modalImage.src = images[currentIndex]; // Ověřte správnou cestu
    caption.textContent = `Obrázek ${currentIndex + 1} z ${images.length}`;
}











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
        chatMessages.scrollTop = chatMessages.scrollHeight;
    } catch (error) {
        console.error('Error processing messages:', error);
    }
}


document.addEventListener("DOMContentLoaded", function() {
    let username = "<?php echo $_SESSION['username'] ?? 'Guest'; ?>";
    
    if (username === "Guest") {
        document.getElementById("chat-container").style.display = "none";
    }
});



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


function scrollToBottom() {
    const chatMessages = document.getElementById('chat-messages');
    chatMessages.scrollTop = chatMessages.scrollHeight;

}





    </script>



   
   
</body>
</html>
