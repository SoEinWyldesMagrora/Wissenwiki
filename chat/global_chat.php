<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$host = 'localhost';
$dbname = 'mr_codespace_wiki';
$username = 'mr_codespace_wiki';
$password = '~8R5r8i2f';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Datenbankfehler: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Globaler Chat</title>
    <link rel="stylesheet" href="chat_styles.css">
</head>
<body>
    <div class="chat-container">
        <div class="channels-list">
            <div class="group-header">
                <h3>Gruppen</h3>
                <button onclick="showCreateGroupModal()">+</button>
            </div>
            <select id="group-selector" onchange="changeGroup(this.value)">
                <option value="0">Global Chat</option>
                <?php
                $stmt = $pdo->query("SELECT * FROM chat_groups ORDER BY created_at DESC");
                while($group = $stmt->fetch()) {
                    echo "<option value='{$group['id']}'>{$group['group_name']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="chat-area">
            <div class="chat-header">
                <h3 id="current-chat-name">Global Chat</h3>
            </div>
            
            <div class="messages" id="messages"></div>
            
            <div class="input-area">
                <form id="message-form">
                    <input type="text" id="message-input" placeholder="Nachricht schreiben..." autocomplete="off">
                    <button type="submit">Senden</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal für Gruppenerstellung -->
    <div id="create-group-modal" class="modal">
        <div class="modal-content">
            <h3>Neue Gruppe erstellen</h3>
            <form id="create-group-form">
                <input type="text" id="group-name" placeholder="Gruppenname" required>
                <button type="submit">Erstellen</button>
                <button type="button" onclick="hideCreateGroupModal()">Abbrechen</button>
            </form>
        </div>
    </div>

    <script>
        let currentGroup = 0;

        function changeGroup(groupId) {
            currentGroup = groupId;
            loadMessages();
            document.getElementById('current-chat-name').textContent = 
                groupId == 0 ? 'Global Chat' : document.getElementById('group-selector').selectedOptions[0].text;
        }

        function loadMessages() {
            fetch(`get_messages.php?group_id=${currentGroup}`)
                .then(response => response.json())
                .then(messages => {
                    const messagesContainer = document.getElementById('messages');
                    messagesContainer.innerHTML = '';
                    messages.forEach(msg => {
                        const messageDiv = document.createElement('div');
                        messageDiv.className = 'message';
                        messageDiv.innerHTML = `
                            <strong>${msg.username}</strong>
                            <div class="message-content">${msg.message}</div>
                            <small>${new Date(msg.timestamp).toLocaleTimeString()}</small>
                        `;
                        messagesContainer.appendChild(messageDiv);
                    });
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                });
        }

        document.getElementById('message-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const message = document.getElementById('message-input').value;
            if (!message.trim()) return;

            fetch('send_message.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `group_id=${currentGroup}&message=${encodeURIComponent(message)}`
            }).then(() => {
                document.getElementById('message-input').value = '';
                loadMessages();
            });
        });

        // Automatische Aktualisierung
        setInterval(loadMessages, 3000);

        // Initial laden
        loadMessages();
    </script>
</body>
</html>
