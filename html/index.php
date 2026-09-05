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
    <title><?php echo $page_name; ?></title>
</head>
<body>

<nav>
        <a href="index.php">Hem</a>
        <a href="groups.php">Grupper</a>
    <?php if ($user): ?>
        <a href="logout.php">Logga ut</a>
    <?php else: ?>
        <a href="login.php">Logga in</a>
    <?php endif; ?>
</nav>

<h1><?php echo $page_name; ?></h1>

    <p>En trygg och mysig plats för kvinnor att mötas, dela intressen och hitta gemenskap</p>

<?php if ($user): ?>

    <p>Välkommen <?php echo htmlspecialchars($user['first_name']); ?>!</p>

    <?php endif; ?>


</body>
</html>