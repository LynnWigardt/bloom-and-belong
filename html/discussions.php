<?php

session_start();

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

    if (empty($subject) || empty($group_id)) {

        echo 'Du måste fylla i alla fält';

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

        echo 'Diskussionen har skapats';
    }
}

?>


<!DOCTYPE html>
<html lang="sv">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo $page_name; ?> - Bloom & Belong</title>
</head>
<body>

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

</body>
</html>