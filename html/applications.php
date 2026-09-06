<?php
session_start();

require_once 'db.php';

$page_name = 'Medlemsansökningar';

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $application_id = $_POST['application_id'];
    $stmt = $pdo->prepare(
    'SELECT user_id, group_id FROM GroupApplications WHERE id = ?'
    );

    $stmt->execute([$application_id]);
    $application = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($application) {
        $stmt = $pdo->prepare(
        'INSERT INTO GroupMembers (user_id, group_id) VALUES (?, ?)'
        );

        $stmt->execute([
            $application['user_id'],
            $application['group_id']
        ]);

        $stmt = $pdo->prepare(
        'DELETE FROM GroupApplications WHERE id = ?'
        );

        $stmt->execute([$application_id]);

        echo 'Ansökan har godkänts!';
    }
}

$stmt = $pdo->prepare(
    'SELECT GroupApplications.id, GroupApplications.user_id, GroupApplications.group_id,
            Users.first_name, Users.last_name, Groups.name
     FROM GroupApplications
     JOIN Users ON GroupApplications.user_id = Users.id
     JOIN Groups ON GroupApplications.group_id = Groups.id
     JOIN GroupMembers ON GroupApplications.group_id = GroupMembers.group_id
     WHERE GroupMembers.user_id = ?'
);

$stmt->execute([$user_id]);

$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

        <?php foreach ($applications as $application): ?>

    <p>
        <?php echo htmlspecialchars($application['first_name']); ?>
        <?php echo htmlspecialchars($application['last_name']); ?>
        har ansökt om medlemskap i gruppen
        <?php echo htmlspecialchars($application['name']); ?>
    </p>

    <form method="POST">
    <input type="hidden" name="application_id" value="<?php echo $application['id']; ?>">
    <button type="submit">Godkänn ansökan</button>
    </form>

<?php endforeach; ?>

        </body>
</html>
