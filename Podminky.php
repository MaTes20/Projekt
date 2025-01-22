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

$conn->close();
?>



<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BezvaTábor</title>
    <link rel="stylesheet" href="podminky.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
   
    <script src="https://apis.google.com/js/platform.js" async defer></script>
    <meta name="google-signin-client_id" content="667754488994-72mh4kcvnfqkh24bs7p4b472mi03d9pf.apps.googleusercontent.com">

    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>


    <!-- Hlavička s navigací -->
    <header>

        
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
        <img src="<?= isset($_SESSION['username']) && $_SESSION['username'] !== 'Guest' 
                      ? htmlspecialchars($_SESSION['profile_picture']) 
                      : 'images/default-profile.png' ?>" 
             alt="Profile Picture" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px;">
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
        <img src="/images/logoBAT.png" alt="Logo BAT">
    </div>
</div>




    <button id="chat-toggle" onclick="toggleChat()">Chat</button>
    <div id="chat-container">
    <div id="chat-messages"></div>
    <div id="chat-input-container">
        <input type="text" id="message" placeholder="Napište zprávu...">
        <button onclick="sendMessage()">Odeslat</button>
    </div>
</div>

      



<div class="container-info">
    <h1 class="main-title">Informace pro rodiče</h1>

    <section class="info-section">
        <p>
            Prohlašuji tímto a potvrzuji správnost uvedených údajů a jsem připraven na vyzvání zodpovědné osoby
            provozovatele správnost údajů doložit.
        </p>

        <p>
            Dávám tímto výslovně souhlas s tím, aby organizátor se sídlem Smilkov 50, okr. Benešov, jako správce zpracoval
            v souladu se zákonem č. 101/2000 Sb. o ochraně osobních údajů mé osobní údaje, přičemž beru na vědomí, že takto
            určený zpracovatel(é), již nepodléhá(jí) mému dalšímu souhlasu. Správce může používat mé osobní údaje ke své
            činnosti v souladu se zákonem a v nutném rozsahu pro svou činnost. Souhlas uděluji na dobu neurčitou do
            odvolání tohoto souhlasu. Beru na vědomí, že svůj souhlas mohu kdykoliv odvolat a správce mé údaje do jednoho
            roku zlikviduje.
        </p>

        <p>
            Svým podpisem na přihlášce dávám souhlas s fotografováním a pořizováním multimediálních záznamů mého dítěte
            při táborových činnostech a s jejich následným použitím při prezentaci a propagaci sdružení Bezva tábor např.
            v kronikách, tisku, na webových stránkách, vzpomínkovém videu z tábora atd.
        </p>

        <p>
            Jsou mi známy dispozice provozu tábora, a uvedl(a) jsem v přihlášce veškeré závažné informace týkající se
            zdravotního stavu účastníka tábora. Případné změny, zjištěné po odevzdání této přihlášky, oznámím nejpozději
            před zahájením tábora.
        </p>

        <p>
            Současně prohlašuji, že vzhledem k charakteru tábora je telefonické spojení uvedené v přihlášce pro dobu
            konání tábora platné a že v případě závažných zdravotních problémů účastníka nebo jeho závažných kázeňských
            přestupků zajistím odvoz účastníka z tábora nejpozději do 24 hodin. Dále se tímto zavazuji uhradit veškeré
            případné škody na majetku provozovatele tábora, případně dalších účastníků tábora, které mé dítě úmyslně
            způsobilo.
        </p>
    </section>
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
        <p>Nebo se přihlaste pomocí Google:</p>
        <a href="<?= htmlspecialchars($client->createAuthUrl()); ?>">Login with Google</a>

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
