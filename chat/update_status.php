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

    // Status aktualisieren
    $stmt = $pdo->prepare("
        INSERT INTO user_status (user_id, last_activity, status) 
        VALUES (?, NOW(), 'online') 
        ON DUPLICATE KEY UPDATE last_activity = NOW(), status = 'online'
    ");
    $stmt->execute([$_SESSION['user_id']]);

    // Offline-Status für inaktive Benutzer setzen (5 Minuten Timeout)
    $stmt = $pdo->query("
        UPDATE user_status 
        SET status = 'offline' 
        WHERE last_activity < NOW() - INTERVAL 5 MINUTE
    ");

    echo json_encode(['success' => true]);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
