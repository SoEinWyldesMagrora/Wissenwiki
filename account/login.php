<?php
session_start();

// Prüfen, ob der Benutzer bereits eingeloggt ist
if (isset($_SESSION['user_id'])) {
    header("Location: https://dragonnetz.de/index.php");
    exit();
}

// Datenbankverbindung herstellen
$host = 'localhost';
$dbname = 'mr_codespace_wiki';
$username = 'mr_codespace_wiki';
$password = '~8R5r8i2f';

// Speichern der vorherigen URL vor Anzeige des Login-Formulars
if (isset($_SERVER['HTTP_REFERER'])) {
    $_SESSION['redirect_url'] = $_SERVER['HTTP_REFERER'];
}

// Wenn das Formular gesendet wurde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Benutzer überprüfen
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$_POST['username']]);
        $user = $stmt->fetch();

        if ($user && password_verify($_POST['password'], $user['password'])) {
            $_SESSION['user_id'] = $user['user_ID'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['admin'] = $user['admin'];
            
            // Weiterleitung zur vorherigen Seite oder zur Startseite
            $redirect_url = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : "https://dragonnetz.de/index.php";
            unset($_SESSION['redirect_url']); 
			if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}// Entfernen der URL nach der Nutzung
            header("Location: " . $redirect_url);
            exit();
        } else {
            $error = "Ungültige Anmeldedaten";
        }
    } catch (PDOException $e) {
        $error = "Anmeldefehler: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wiki - Login</title>
    <link rel="stylesheet" href="dash/styles.css">
	<link rel="shortcut icon" href="https://dragonnetz.de/favicon.png" />
    <style>
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

        form .form-footer {
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
      <nav class="sidebar">
            <ul>
                <li><a href="https://dragonnetz.de/index.php">Startseite</a></li>
             
                <li><a href="https://api.dragonnetz.de/account/register.php">Registrieren</a></li>
                
             
				
            </ul>
        </nav>
        <main>
            <h1>Login</h1>
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
                <button type="submit">Anmelden</button>
                <div class="form-footer">
                    <p class="register-link">
                        Noch kein Konto? <a href="register.php">Hier registrieren</a>
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


