<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'db.php';

$page_name = 'Grupper';

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare(
'SELECT group_id FROM GroupMembers WHERE user_id = ?'
);
$stmt->execute([$user_id]);
$member_groups = $stmt->fetchAll(PDO::FETCH_COLUMN);

$stmt = $pdo->prepare(
'SELECT group_id FROM GroupApplications WHERE user_id = ?'
);

$stmt ->execute([$user_id]);

$application_groups = $stmt->fetchAll(PDO::FETCH_COLUMN);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? null;
    $group_id = $_POST['group_id'] ?? null;
if ($name) {
        $stmt = $pdo->prepare(
        'INSERT INTO Groups (name) VALUES (?)'
        );

        $stmt->execute([$name]);

        $new_group_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare(
        'INSERT INTO GroupMembers (user_id, group_id) VALUES (?, ?)'
        );

        $stmt->execute([
            $user_id,
            $new_group_id
        ]);

        header('Location: groups.php');
        exit;
    }

    if ($group_id) {
        $stmt = $pdo->prepare(
        'INSERT INTO GroupApplications (user_id, group_id)
        VALUES (?, ?)'
        );

        $stmt->execute([
            $user_id,
            $group_id
        ]);

        header('Location: groups.php');
        exit;
    }
}


$groups = $pdo->query(
    'SELECT * FROM Groups'
)->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title><?php echo $page_name; ?> - Bloom  Belong</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

<header>
    <img src="header3.png" alt="Bloom & Belong">
</header>

<nav>
    <a href="index.php">Hem</a>
    <a href="groups.php">Grupper</a>
    <a href="discussions.php">Diskussioner</a>
    <a href="applications.php">Medlemsansökningar</a>
    <a href="logout.php">Logga ut</a>
</nav>

<main>

<h1><?php echo $page_name; ?></h1>

<h2>Skapa en ny grupp</h2>

<form method="POST">

<label for="name">Gruppnamn:</label>
<input type="text" id="name" name="name" required>
<br><br>

<button type="submit">Skapa grupp</button>

</form>

<?php foreach ($groups as $group): ?>

    <div class="group-item">

    <h3>
    <a href="group.php?id=<?php echo $group['id']; ?>">
    <?php echo htmlspecialchars($group['name']); ?></a>
    </h3>

    <?php if (in_array($group['id'], $member_groups)): ?>
        <p class="group-status">Du är medlem</p>

    <?php elseif (in_array($group['id'], $application_groups)): ?>
        <p class="group-status">Ansökan skickad</p>

    <?php else: ?>
        <p class="group-status">Inte medlem</p>

        <form method="POST" class="application-form">
            <input type="hidden" name="group_id" value="<?php echo $group['id']; ?>">
            <button type="submit">Ansök om medlemskap</button>
        </form>

        <?php endif; ?>

    </div>

<?php endforeach; ?>

</main>

<footer></footer>

</body>
</html>