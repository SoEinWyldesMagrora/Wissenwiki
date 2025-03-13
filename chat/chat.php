<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: https://api.dragonnetz.de/account/login.php");
    exit();
}

$host = 'localhost';
$dbname = 'mr_codespace_wiki';
$username = 'mr_codespace_wiki';
$password = '~8R5r8i2f';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("SELECT user_ID, username FROM users WHERE user_ID != ?");
    $stmt->execute([$_SESSION['user_id']]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Datenbankfehler: " . $e->getMessage());
}

// Aktuellen Benutzer laden
$stmt = $pdo->prepare("SELECT username FROM users WHERE user_ID = ?");
$stmt->execute([$_SESSION['user_id']]);
$currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NN - Chatting</title>
    <link rel="stylesheet" href="chat_styles.css">
	<link rel="shortcut icon" href="https://dragonnetz.de/favicon.png" />
</head>
<body>
    <div class="chat-container">
        <div class="channels-list">
            <div class="user-info">
                <span class="user-status status-online"></span>
                <span><?= htmlspecialchars($currentUser['username']) ?></span>
            </div>
            <div class="channel-header">Private Nachrichten</div>
            <div id="users-list">
                <?php foreach($users as $user): ?>
                    <div class="user-item" data-user-id="<?= $user['user_ID'] ?>">
                        <span class="user-status status-online"></span>
                        <?= htmlspecialchars($user['username']) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="chat-area">
            <div class="chat-header">
                <h3 id="current-chat">Wähle einen Chat</h3>
            </div>
            
            <div class="messages" id="messages"></div>
            
            <div class="input-area">
                <input type="text" id="message-input" placeholder="Schreibe eine Nachricht..." disabled>
            </div>
        </div>
    </div>

    <script>
        const currentUserId = <?= $_SESSION['user_id'] ?>;
        let currentChatPartner = null;
    </script>
    <script src="chat.js"></script>
</body>
</html>
