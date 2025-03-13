<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['admin'] != 1) {
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

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $stmt = $pdo->prepare("UPDATE users SET username = ?, admin = ? WHERE user_ID = ?");
        $admin = isset($_POST['admin']) ? 1 : 0;
        $stmt->execute([
            $_POST['username'],
            $admin,
            $_POST['id']
        ]);

        if (!empty($_POST['new_password'])) {
            $hashed_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_ID = ?");
            $stmt->execute([$hashed_password, $_POST['id']]);
        }

        header("Location: admin_users.php");
        exit();
    }

    $id = $_GET['id'] ?? 0;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_ID = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("Benutzer nicht gefunden.");
    }

} catch(PDOException $e) {
    die("Datenbankfehler: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benutzer bearbeiten</title>
    <link rel="stylesheet" href="styles.css">
	<link rel="shortcut icon" href="https://dragonnetz.de/favicon.png" />
</head>
<body>
    <header>
        <div class="header-container">
            <h1>Benutzer bearbeiten</h1>
        </div>
    </header>
    <div class="main-container">
        <nav class="sidebar">
            <ul>
                <li><a href="admin.php">Admin-Übersicht</a></li>
                <li><a href="admin_users.php">Zurück zur Benutzerübersicht</a></li>
            </ul>
        </nav>
        <main>
            <form method="post" action="">
                <input type="hidden" name="id" value="<?= $user['user_ID'] ?>">
                
                <div class="form-group">
                    <label for="username">Benutzername:</label>
                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="new_password">Neues Passwort (leer lassen, um nicht zu ändern):</label>
                    <input type="password" id="new_password" name="new_password">
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="admin" <?= $user['admin'] ? 'checked' : '' ?>>
                        Administrator-Rechte
                    </label>
                </div>

                <button type="submit" class="btn">Änderungen speichern</button>
            </form>
        </main>
    </div>
    <footer>
        <p>&copy; 2025 Nutzloses Notizbuch, Alle Rechte vorbehalten.</p>
    </footer>
</body>
</html>
