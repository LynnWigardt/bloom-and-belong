<?php

session_start();

require_once 'db.php';

$page_name = 'Diskussion';

$user_id = $_SESSION['user_id'];

$discussion_id = $_GET['id'] ?? null;

if (!$discussion_id) {
    echo 'Ingen diskussion vald.';
    exit;
}

$stmt = $pdo->prepare(
    'SELECT Discussions.id, Discussions.subject, Discussions.created_at, Groups.name
     FROM Discussions
     JOIN Groups ON Discussions.group_id = Groups.id
     WHERE Discussions.id = ?'
);

$stmt->execute([$discussion_id]);

$discussion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$discussion) {
    echo 'Diskussionen kunde inte hittas';
    exit;
}

?>



<!DOCTYPE html>
<html lang="sv">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo htmlspecialchars($discussion['subject']); ?> - Bloom & Belong</title>
</head>

<body>
    <h1><?php echo htmlspecialchars($discussion['subject']); ?></h1>

    <p>
        Grupp:
        <?php echo htmlspecialchars($discussion['name']); ?>
    </p>
</body>
</html>