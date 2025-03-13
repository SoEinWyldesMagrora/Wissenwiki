<?php
session_start();
// Fehlerberichterstattung aktivieren (nur für Entwicklungszwecke)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Datenbankverbindung
$host = 'localhost';
$dbname = 'mr_codespace_wiki';
$username = 'mr_codespace_wiki';
$password = '~8R5r8i2f';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Datenbankverbindung fehlgeschlagen: " . $e->getMessage());
}

// Artikel-ID aus der URL holen
$article_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Artikel aus der Datenbank abrufen
$stmt = $pdo->prepare("SELECT * FROM wiki_eintraege WHERE ID = ?");
$stmt->execute([$article_id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    die("Artikel nicht gefunden.");
}

// Formular wurde abgeschickt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    
    // Artikel in der Datenbank aktualisieren
    $updateStmt = $pdo->prepare("UPDATE wiki_eintraege SET Titel = ?, Inhalt = ? WHERE ID = ?");
    $updateStmt->execute([$title, $content, $article_id]);
    
    // Zurück zur Artikelseite leiten
    header("Location: article.php?id=" . $article_id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artikel bearbeiten - <?= htmlspecialchars($article['Titel']) ?></title>
    <link rel="stylesheet" href="styles.css">
	<link rel="shortcut icon" href="https://dragonnetz.de/favicon.png" />
</head>
<body>
    <header>
        <h1>Artikel bearbeiten</h1>
    </header>
    <main>
        <form method="POST" action="">
            <div>
                <label for="title">Titel:</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($article['Titel']) ?>" required>
            </div>
            <div>
                <label for="content">Inhalt:</label>
                <textarea id="content" name="content" rows="20" required><?= htmlspecialchars($article['Inhalt']) ?></textarea>
            </div>
            <div>
                <button type="submit">Änderungen speichern</button>
                <a href="article.php?id=<?= $article_id ?>">Abbrechen</a>
            </div>
        </form>
    </main>
    <footer>
        <p>&copy; 2025 Nutzloses Notizbuch, Alle Rechte vorbehalten.</p>
    </footer>
</body>
</html>
