<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = 'localhost';
$dbname = 'mr_codespace_wiki';
$username = 'mr_codespace_wiki';
$password = '~8R5r8i2f';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $admin = isset($_POST['admin']) ? 1 : 0;
        
        $stmt = $pdo->prepare("INSERT INTO users (username, password, admin) VALUES (?, ?, ?)");
        $stmt->execute([$username, $password, $admin]);
        
        header("Location: login.php?registration=success");
        exit();
    } catch (PDOException $e) {
        $error = "Registrierung fehlgeschlagen: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrierung</title>
    <link rel="stylesheet" href="dash/styles.css">
	<link rel="shortcut icon" href="https://dragonnetz.de/favicon.png" />
    <style>
        /* Design für das Registrierungsformular */
        .register-link {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.9rem;
        }

        .register-link a {
            color: var(--link-color);
            text-decoration: none;
            font-weight: bold;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .form-footer {
            margin-top: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <h1>Nutzloses Notizbuch</h1>
        </div>
    </header>
    <div class="main-container">
        <aside class="sidebar">
            <ul>
                <li><a href="https://dragonnetz.de/index.php">Startseite</a></li>
                
                <li><a href="login.php">Anmelden</a></li>
            </ul>
        </aside>
        <main>
            <h1>Registrierung</h1>
            <?php if (isset($error)): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Benutzername</label>
                    <input type="text" id="username" name="username" placeholder="Benutzername" required>
                </div>
                <div class="form-group">
                    <label for="password">Passwort</label>
                    <input type="password" id="password" name="password" placeholder="Passwort" required>
                </div>
             <div class="form-group">
                    <label>
                        <input type="checkbox" name="admin"> Administrator
                    </label>
                </div>
                <button type="submit">Registrieren</button>
                <div class="form-footer">
                    <p class="register-link">
                        Bereits registriert? <a href="login.php">Hier anmelden</a>
                    </p>
                </div>
            </form>
        </main>
    </div>
    <footer>
        &copy; 2025 Mein Wiki. Alle Rechte vorbehalten.
    </footer>
</body>
</html>
