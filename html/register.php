<?php

require_once 'db.php';

$page_name = 'Skapa konto';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {

        echo 'Alla fält måste fyllas i.';

    } else {

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare(
            'INSERT INTO Users (first_name, last_name, email, password_hash) VALUES (?, ?, ?, ?)'
        );

        try {
            $stmt->execute([
                $first_name,
                $last_name,
                $email,
                $hashed_password
            ]);

            echo 'Ditt konto har skapats. Välkommen till Bloom & Belong!';

        } catch (PDOException $e) {

            if ($e->getCode() == 23000) {
                die('E-postadressen är redan registrerad.');
            } else {
                die('Fel vid skapande av konto: ' . $e->getMessage());
            }
        }
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

    <h1>Skapa konto</h1>

    <p>Välkommen till Bloom & Belong! Skapa ett konto för att kunna hänga med oss!</p>

    <form method="POST">
        <label for="first_name">Förnamn:</label>
        <input type="text" id="first_name" name="first_name" required>
        <br><br>

        <label for="last_name">Efternamn:</label>
        <input type="text" id="last_name" name="last_name" required>
        <br><br>

        <label for="email">E-post:</label>
        <input type="email" id="email" name="email" required>
        <br><br>

        <label for="password">Lösenord:</label>
        <input type="password" id="password" name="password" required>
        <br><br>

        <button type="submit">Skapa konto</button>
    </form>

    </body>
</html>