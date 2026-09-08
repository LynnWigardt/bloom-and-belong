<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

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
     JOIN GroupMembers ON Discussions.group_id = GroupMembers.group_id
     WHERE Discussions.id = ?
     AND GroupMembers.user_id = ?'
);

$stmt->execute([$discussion_id, $user_id]);

$discussion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$discussion) {
    echo 'Diskussionen kunde inte hittas';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'] ?? null;
    if (empty($content)) {
        echo 'Du måste skriva ett inlägg.';
    } else {
        $stmt = $pdo->prepare(
        'INSERT INTO Posts (user_id, discussion_id, content, created_at)
        VALUES (?, ?, ?, NOW())'
        );

        $stmt->execute([
            $user_id,
            $discussion_id,
            $content
        ]);

        echo 'Inlägget har publicerats!';
    }
    }

    $stmt = $pdo->prepare(
    'SELECT Posts.content, Posts.created_at, Users.first_name, Users.last_name
     FROM Posts
     JOIN Users ON Posts.user_id = Users.id
     WHERE Posts.discussion_id = ?
     ORDER BY Posts.created_at ASC'
);

$stmt->execute([$discussion_id]);

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>



<!DOCTYPE html>
<html lang="sv">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">

    <title><?php echo htmlspecialchars($discussion['subject']); ?> - Bloom & Belong</title>
</head>

<body>

    <nav>
        <a href="index.php">Hem</a>
        <a href="groups.php">Grupper</a>
        <a href="discussions.php">Diskussioner</a>
        <a href="applications.php">Medlemsansökningar</a>
        <a href="logout.php">Logga ut</a>
    </nav>

    <h1><?php echo htmlspecialchars($discussion['subject']); ?></h1>

    <p>
        Grupp:
        <?php echo htmlspecialchars($discussion['name']); ?>
    </p>

    <h2>Skriv ett inlägg</h2>

    <form method="POST">

    <textarea name="content" rows="5" cols="40" required></textarea>
    <br>
    <button type="submit">Publicera inlägg</button>
    </form>

<h2>Inlägg</h2>
<?php foreach ($posts as $post): ?>

<p>
    <strong>
        <?php echo htmlspecialchars($post['first_name']); ?>
        <?php echo htmlspecialchars($post['last_name']); ?>
    </strong>
    <br>
    Skrevs:
        <?php echo htmlspecialchars($post['created_at']); ?>
    <br>
        <?php echo htmlspecialchars($post['content']); ?>
</p>

<?php endforeach; ?>

</body>
</html>