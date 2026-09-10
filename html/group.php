<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'db.php';

$page_name = 'Grupp';

$user_id = $_SESSION['user_id'];
$group_id = $_GET['id'] ?? null;

if (!$group_id) {
    echo 'Ingen grupp vald.';
    exit;
}


$stmt = $pdo->prepare(
    'SELECT Groups.id, Groups.name
     FROM Groups
     JOIN GroupMembers ON Groups.id = GroupMembers.group_id
     WHERE Groups.id = ?
     AND GroupMembers.user_id = ?'
);

$stmt->execute([$group_id, $user_id]);

$group = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$group) {
    echo 'Gruppen kunde inte hittas.';
    exit;
}


$stmt = $pdo->prepare(
    'SELECT id, subject, created_at
     FROM Discussions
     WHERE group_id = ?
     ORDER BY created_at DESC'
);

$stmt->execute([$group_id]);

$discussions = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="sv">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="style.css">

    <title><?php echo htmlspecialchars($group['name']); ?> - Bloom & Belong</title>
</head>

<body>

<nav>
    <a href="index.php">Hem</a>
    <a href="groups.php">Grupper</a>
    <a href="discussions.php">Diskussioner</a>
    <a href="applications.php">Medlemsansökningar</a>
    <a href="logout.php">Logga ut</a>
</nav>

<main>

    <h1><?php echo htmlspecialchars($group['name']); ?></h1>

    <h2>Diskussioner</h2>

    <?php if (empty($discussions)): ?>

        <p>Det finns inga diskussioner i den här gruppen ännu. Men skapa en vetja!</p>

    <?php else: ?>

        <?php foreach ($discussions as $discussion): ?>


    <div class="group-item">

    <h3>
        <a href="discussion.php?id=<?php echo $discussion['id']; ?>">
            <?php echo htmlspecialchars($discussion['subject']); ?>
        </a>
    </h3>

    <?php if (!empty($discussion['created_at'])): ?>
        <p class="group-status">
            Skapad <?php echo htmlspecialchars($discussion['created_at']); ?>
        </p>
    
        <?php endif; ?>

    </div>

<?php endforeach; ?>

<?php endif; ?>

</main>

</body>
</html>