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

    $user_id = $_SESSION['user_id'];
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $new_username = $_POST['username'];
        $new_email = $_POST['email'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Überprüfen, ob der neue Benutzername bereits existiert
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND user_ID != ?");
        $stmt->execute([$new_username, $user_id]);
        if ($stmt->rowCount() > 0) {
            $error = "Dieser Benutzername ist bereits vergeben.";
        } else {
            // Aktualisiere Benutzername und E-Mail
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE user_ID = ?");
            $stmt->execute([$new_username, $new_email, $user_id]);

            // Wenn ein neues Passwort eingegeben wurde
            if (!empty($new_password)) {
                if ($new_password === $confirm_password) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_ID = ?");
                    $stmt->execute([$hashed_password, $user_id]);
                } else {
                    $error = "Die Passwörter stimmen nicht überein.";
                }
            }

            if (!isset($error)) {
                $success = "Profil erfolgreich aktualisiert.";
                $_SESSION['username'] = $new_username;
            }
        }
    }

    // Lade aktuelle Benutzerdaten
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_ID = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Datenbankfehler: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil bearbeiten</title>
    <link rel="stylesheet" href="dash/styles.css">
	<link rel="shortcut icon" href="https://dragonnetz.de/favicon.png" />
</head>
<body>
    <header>
        <div class="header-container">
            <h1>Profil bearbeiten</h1>
        </div>
    </header>
    <div class="main-container">
        <nav class="sidebar">
            <ul>
                <li><a href="https://dragonnetz.de/index.php">Zurück zum Wiki</a></li>
                <li><a href="logout.php">Abmelden</a></li>
            </ul>
        </nav>
        <main>
            <?php if (isset($error)): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <p class="success"><?= htmlspecialchars($success) ?></p>
            <?php endif; ?>

            <form method="post" action="">
                <div class="form-group">
                    <label for="username">Benutzername:</label>
                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">E-Mail:</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="new_password">Neues Passwort (leer lassen, um nicht zu ändern):</label>
                    <input type="password" id="new_password" name="new_password">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Passwort bestätigen:</label>
                    <input type="password" id="confirm_password" name="confirm_password">
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
