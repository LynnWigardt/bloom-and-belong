<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'db.php';

$page_name = 'Diskussioner';

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare(
    'SELECT Discussions.id, Discussions.subject, Discussions.created_at, Groups.name
     FROM Discussions
     JOIN Groups ON Discussions.group_id = Groups.id
     JOIN GroupMembers ON Discussions.group_id = GroupMembers.group_id
     WHERE GroupMembers.user_id = ?
     ORDER BY Discussions.created_at DESC'
);

$stmt->execute([$user_id]);

$discussions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare(
    'SELECT Groups.id, Groups.name
    FROM Groups
    JOIN GroupMembers ON Groups.id = GroupMembers.group_id
    WHERE GroupMembers.user_id = ?'
);

$stmt->execute([$user_id]);

$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $subject = $_POST['subject'] ?? null;
    $group_id = $_POST['group_id'] ?? null;
    $content = $_POST['content'] ?? null;
    if (empty($subject) || empty($group_id) || empty($content)) {
        echo 'Du måste fylla i alla fält';
    } else {
        $stmt = $pdo->prepare(
            'SELECT group_id
             FROM GroupMembers
             WHERE group_id = ?
             AND user_id = ?'
        );

        $stmt->execute([
            $group_id,
            $user_id
        ]);

        $is_member = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$is_member) {
            echo 'Du måste vara medlem i gruppen för att kunna skapa en diskussion.';
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO Discussions (subject, group_id, user_id)
                 VALUES (?, ?, ?)'
            );

            $stmt->execute([
                $subject,
                $group_id,
                $user_id
            ]);

            $discussion_id = $pdo->lastInsertId();

            $stmt = $pdo->prepare(
                'INSERT INTO Posts (user_id, discussion_id, content, created_at)
                 VALUES (?, ?, ?, NOW())'
            );

            $stmt->execute([
                $user_id,
                $discussion_id,
                $content
            ]);

            header('Location: discussions.php');
            exit;
        }
    }
}

?>


<!DOCTYPE html>
<html lang="sv">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="/style.css">
    <title><?php echo $page_name; ?> - Bloom & Belong</title>
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
    <h1><?php echo $page_name; ?></h1>

    <h2>Skapa en diskussion</h2>
    <form method="POST">

    <label for="subject">Rubrik:</label>
    <input type="text" id="subject" name="subject" required>

    <label for="group_id">Grupp:</label>
    <select id="group_id" name="group_id" required>

    <?php foreach ($groups as $group): ?>
    
        <option value="<?php echo $group['id']; ?>">
        <?php echo htmlspecialchars($group['name']); ?>
        </option>

<?php endforeach; ?>

    </select>
    <label for="content">Inlägg:</label>
    <textarea id="content" name="content" rows="5" placeholder="Skriv ditt inlägg här..." required></textarea>

    <button type="submit">Skapa diskussion</button>

    </form>


    <?php foreach ($discussions as $discussion): ?>
        
        <h2>
            <a href="discussion.php?id=<?php echo $discussion['id']; ?>">
                <?php echo htmlspecialchars($discussion['subject']); ?>
            </a>
        </h2>

        <p>
            I gruppen
            <?php echo htmlspecialchars($discussion['name']); ?>
        </p>

        <?php endforeach; ?>
    </main>
</body>
</html>