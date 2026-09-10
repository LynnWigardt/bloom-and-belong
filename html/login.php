<?php

session_start();

require_once 'db.php';

$page_name = 'Logga in';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $pdo->prepare(
    'SELECT * FROM Users WHERE email = ?'

);
$stmt->execute([$email]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password_hash'])) {

$_SESSION['user_id'] = $user['id'];

    header('Location: index.php');
    exit;

} else {

    echo 'Fel e-postadress eller lösenord.';

       }  
}
?>




<!DOCTYPE html>
<html lang="sv">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&display=swap" rel="stylesheet">

<title><?php echo $page_name; ?> - Bloom & Belong</title>
    </head>

    <body>

    <header>
    <img src="header3.png" alt="Bloom & Belong">
    </header>

    <nav>
    <a href="index.php">Hem</a>
    <a href="groups.php">Grupper</a>
    <a href="discussions.php">Diskussioner</a>
    <a href="login.php">Logga in</a>
    </nav>

    <main>

    <h1><?php echo $page_name; ?></h1>

    <form method="POST">

    <label for="email">E-mail:</label>
    <input type="email" id="email" name="email" required>
    <br><br>

    <label for="password">Lösenord:</label>
    <input type="password" id="password" name="password" required>
    <br><br>

    <button type="submit">Logga in</button>
    
</form>

<p>
    Har du inget konto än?
    <a href="register.php">Bli medlem</a>
</p>

</main>

<footer></footer>

    </body>
    </html>