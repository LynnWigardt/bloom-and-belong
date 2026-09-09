<?php

session_start();

require_once 'db.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);

    echo json_encode([
        'error' => 'Du måste vara inloggad.'
    ]);

    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare(
    'SELECT Groups.id, Groups.name
     FROM Groups
     JOIN GroupMembers ON Groups.id = GroupMembers.group_id
     WHERE GroupMembers.user_id = ?'
);

$stmt->execute([$user_id]);

$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($groups);