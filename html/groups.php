<?php

session_start();

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

        $stmt->execute([$_SESSION['user_id'], $new_group_id]);

        echo 'Gruppen har skapats!';

    }

    if ($group_id) {

        $stmt = $pdo->prepare(
            'INSERT INTO GroupApplications (user_id, group_id) VALUES (?, ?)'
        );

        $stmt->execute([$user_id, $group_id]);

        echo 'Din ansökan har skickats!';

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

    <title><?php echo $page_name; ?> - Bloom  Belong</title>
</head>

<body>

<h1><?php echo $page_name; ?></h1>

<h2>Skapa en ny grupp</h2>

<form method="POST">

<label for="name">Gruppnamn:</label>
<input type="text" id="name" name="name" required>
<br><br>

<button type="submit">Skapa grupp</button>

</form>

<?php foreach ($groups as $group): ?>

    <p>
     <?php echo htmlspecialchars($group['name']); ?>
     <?php if (in_array($group['id'], $member_groups)): ?>
    - Du är medlem

    <?php elseif (in_array($group['id'], $application_groups)): ?>
    - Ansökan skickad

    <?php else: ?>
    - Inte medlem

    <form method="POST">
        <input type="hidden" name="group_id" value="<?php echo $group['id']; ?>">
        <button type="submit">Ansök om medlemskap</button>
    </form>

<?php endif; ?>
    </p>
<?php endforeach; ?>

</body>
</html>