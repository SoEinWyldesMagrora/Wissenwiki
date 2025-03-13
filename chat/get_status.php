<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    exit(json_encode(['error' => 'Nicht eingeloggt']));
}

$host = 'localhost';
$dbname = 'mr_codespace_wiki';
$username = 'mr_codespace_wiki';
$password = '~8R5r8i2f';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT user_id, status FROM user_status");
    $statuses = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    echo json_encode($statuses);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
