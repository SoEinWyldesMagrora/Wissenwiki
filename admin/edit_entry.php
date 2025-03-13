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

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $stmt = $pdo->prepare("UPDATE wiki_eintraege SET Titel = ?, Inhalt = ?, Kategorie_ID = ?, Status_ID = ?, Letzte_Änderung = NOW(), Version = Version + 1 WHERE ID = ?");
        $stmt->execute([
            $_POST['titel'],
            $_POST['inhalt'],
            $_POST['kategorie_id'],
            $_POST['status_id'],
            $_POST['id']
        ]);
        header("Location: admin_wiki_entries.php");
        exit();
    }

    $id = $_GET['id'] ?? 0;
    $stmt = $pdo->prepare("SELECT * FROM wiki_eintraege WHERE ID = ?");
    $stmt->execute([$id]);
    $entry = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$entry) {
        die("Eintrag nicht gefunden.");
    }

    $stmt = $pdo->query("SELECT * FROM kategorien ORDER BY Kategorie_Name");
    $kategorien = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->query("SELECT * FROM status ORDER BY Status_Name");
    $statusOptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Datenbankfehler: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wiki-Eintrag bearbeiten</title>
    <link rel="stylesheet" href="styles.css">
	<link rel="shortcut icon" href="https://dragonnetz.de/favicon.png" />
</head>
<body>
    <header>
        <div class="header-container">
            <h1>Wiki-Eintrag bearbeiten</h1>
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
            <form method="post" action="">
                <input type="hidden" name="id" value="<?= $entry['ID'] ?>">
                
                <div class="form-group">
                    <label for="titel">Titel:</label>
                    <input type="text" id="titel" name="titel" value="<?= htmlspecialchars($entry['Titel']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="inhalt">Inhalt:</label>
                    <textarea id="inhalt" name="inhalt" rows="10" required><?= htmlspecialchars($entry['Inhalt']) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="kategorie_id">Kategorie:</label>
                    <select id="kategorie_id" name="kategorie_id" required>
                        <?php foreach ($kategorien as $kategorie): ?>
                            <option value="<?= $kategorie['Kategorie_ID'] ?>" <?= $kategorie['Kategorie_ID'] == $entry['Kategorie_ID'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($kategorie['Kategorie_Name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status_id">Status:</label>
                    <select id="status_id" name="status_id" required>
                        <?php foreach ($statusOptions as $status): ?>
                            <option value="<?= $status['Status_ID'] ?>" <?= $status['Status_ID'] == $entry['Status_ID'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($status['Status_Name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
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
