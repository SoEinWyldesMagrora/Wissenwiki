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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $stmt = $pdo->prepare("INSERT INTO wiki_eintraege (Titel, Inhalt, user_id, Erstellungsdatum, Letzte_Änderung, Version, Status_ID, Kategorie_ID) VALUES (?, ?, ?, NOW(), NOW(), 1, ?, ?)");

        $stmt->execute([
            $_POST['titel'],
            $_POST['inhalt'],
            $_SESSION['user_id'],
            $_POST['status_id'],
            $_POST['kategorie_id']
        ]);

        $success = "Wiki-Eintrag erfolgreich erstellt!";
    }
} catch (PDOException $e) {
    $error = "Fehler: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wiki - Neuer Eintrag</title>
    <link rel="stylesheet" href="styles.css">
	<link rel="shortcut icon" href="https://dragonnetz.de/favicon.png" />
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
                <li><a href="https://api.dragonnetz.de/admin/admin.php">Admin-Bereich</a></li>
                <li><a href="https://api.dragonnetz.de/account/logout.php">Abmelden</a></li>
            </ul>
        </aside>
        <main>
            <h1>Neuen Wiki-Eintrag erstellen</h1>
            <?php if (isset($success)): ?>
                <div class="success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="titel">Titel</label>
                    <input type="text" id="titel" name="titel" placeholder="Titel des Eintrags" required>
                </div>

                <div class="form-group">
                    <label for="inhalt">Inhalt</label>
                    <textarea id="inhalt" name="inhalt" placeholder="Inhalt schreiben..." required></textarea>
                </div>

                <div class="form-group">
                    <label for="kategorie_id">Kategorie</label>
                    <select id="kategorie_id" name="kategorie_id" required>
                        <?php
                        $stmt = $pdo->query("SELECT Kategorie_ID, Kategorie_Name FROM kategorien ORDER BY Kategorie_Name");
                        while ($kategorie = $stmt->fetch()) {
                            echo '<option value="' . $kategorie['Kategorie_ID'] . '">' . 
                                 htmlspecialchars($kategorie['Kategorie_Name']) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status_id">Status</label>
                    <select id="status_id" name="status_id" required>
                        <option value="1">Aktiv</option>
                        <option value="2">Entwurf</option>
                        <option value="3">Archiviert</option>
                    </select>
                </div>

                <button type="submit">Erstellen</button>
            </form>
        </main>
    </div>
    <footer>
        &copy; 2025 Mein Wiki. Alle Rechte vorbehalten.
    </footer>
</body>
</html>
