<?php

session_start();

require_once 'db.php';

$page_name = "Bloom & Belong";

$user = null;

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare(
        'SELECT * FROM Users WHERE id = ?'
    );
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
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
    <title><?php echo $page_name; ?></title>
</head>

<body>

<header>
    <img src="header3.png" alt="Bloom & Belong">
</header>

<nav>
    <a href="index.php">Hem</a>
    <a href="groups.php">Grupper</a>
    <a href="discussions.php">Diskussioner</a>

    <?php if ($user): ?>
        <a href="applications.php">Medlemsansökningar</a>
    <?php endif; ?>

    <?php if ($user): ?>
        <a href="logout.php">Logga ut</a>
    <?php else: ?>
        <a href="login.php">Logga in</a>
    <?php endif; ?>
</nav>

<main>

<h1><?php echo $page_name; ?></h1>

    <p>En trygg och mysig plats för människor att mötas, dela intressen och hitta gemenskap</p>

<?php if ($user): ?>

    <p>Välkommen <?php echo htmlspecialchars($user['first_name']); ?>!</p>

    <?php endif; ?>
    
</main>

<footer></footer>

</body>
</html>