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
        <title><?php echo $page_name; ?> Bloom & Belong</title>
    </head>

    <body>

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

    </body>
    </html>