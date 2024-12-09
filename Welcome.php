<?php
session_start(); // Zahájí práci se session

// Zkontrolujte, zda je uživatel přihlášen
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username']; // Získá uživatelské jméno
} else {
    // Přesměrování na přihlašovací stránku, pokud není přihlášen
    header("Location: Index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="welcome.css">
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
        
        <?php if ($username): ?>
        <span>Vítejte, <?php echo htmlspecialchars($username); ?>!</span>
       
    <?php else: ?>
        <a href="#" onclick="openLoginForm()">Přihlášení</a>
    <?php endif; ?>

       
        </div>


    </header>
    



    
</body>
</html>


