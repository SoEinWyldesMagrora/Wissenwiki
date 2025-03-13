<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['admin'] != 1) {
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

    $stmt = $pdo->query("SELECT * FROM users ORDER BY username");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Datenbankfehler: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $id = $_POST['user_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE user_ID = ?");
        $stmt->execute([$id]);
        header("Location: admin_users.php");
        exit();
    } catch(PDOException $e) {
        $error = "Fehler beim Löschen des Benutzers: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="de">
	 <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th:nth-child(1), td:nth-child(1) {
            width: 40%; /* Breite der Benutzernamen-Spalte auf 50% setzen */
        }
        th:nth-child(2), td:nth-child(2) {
            width: 20%;
        }
        th:nth-child(3), td:nth-child(3) {
            width: 30%;
        }
    </style>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benutzer verwalten</title>
    <link rel="stylesheet" href="styles.css">
	<link rel="shortcut icon" href="https://dragonnetz.de/favicon.png" />
</head>
<body>
    <header>
        <div class="header-container">
            <h1>Benutzer verwalten</h1>
            <form class="search-form">
                <input type="text" id="search-input" placeholder="Benutzer durchsuchen...">
                <button type="submit">🔍</button>
            </form>
        </div>
    </header>
    <div class="main-container">
        <nav class="sidebar">
            <ul>
                <li><a href="https://api.dragonnetz.de/admin/admin_wiki_entries.php">Wiki-Einträge verwalten</a></li>
                <li><a href="https://api.dragonnetz.de/admin/admin_users.php">Benutzer verwalten</a></li>
                <li><a href="https://dragonnetz.de/index.php">Zurück zum Wiki</a></li>
                <li><a href="https://api.dragonnetz.de/account/logout.php">Abmelden</a></li>
            </ul>
        </nav>
        <main>
            <?php if (isset($error)): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            
            <table>
                <tr>
                    
                    <th>Benutzername</th>
                    <th>Admin</th>
                    <th>Aktionen</th>
                </tr>
                <?php foreach ($users as $user): ?>
                <tr>
                    
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= $user['admin'] ? 'Ja' : 'Nein' ?></td>
                    <td>
                        <a href="edit_user.php?id=<?= $user['user_ID'] ?>" class="btn">Bearbeiten</a>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?= $user['user_ID'] ?>">
                            <button type="submit" name="delete_user" class="btn btn-danger" onclick="return confirm('Wirklich löschen?')">Löschen</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </main>
    </div>
    <footer>
        <p>&copy; 2025 Nutzloses Notizbuch, Alle Rechte vorbehalten.</p>
    </footer>
</body>
</html>
