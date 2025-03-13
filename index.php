

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

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Datenbankverbindung fehlgeschlagen: " . $e->getMessage());
}

// Hilfsfunktion: Text kürzen
function limitContent($content, $limit = 450) {
    if (strlen($content) > $limit) {
        $shortenedContent = substr($content, 0, $limit);
        $shortenedContent = substr($shortenedContent, 0, strrpos($shortenedContent, ' '));
        return htmlspecialchars($shortenedContent) . '...';
    }
    return htmlspecialchars($content);
}

// Kategorien abrufen
$categoriesStmt = $pdo->query("SELECT Kategorie_ID as id, Kategorie_Name as name FROM kategorien");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

// Suchparameter: Suchbegriff und Kategorie
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';

// Suchbedingungen und Parameter
$searchCondition = '';
$params = [];
if (!empty($searchTerm) || !empty($selectedCategory)) {
    $conditions = [];
    if (!empty($searchTerm)) {
        $conditions[] = "(w.Titel LIKE :searchTerm OR w.Inhalt LIKE :searchTerm)";
        $params[':searchTerm'] = "%$searchTerm%";
    }
    if (!empty($selectedCategory)) {
        $conditions[] = "w.Kategorie_ID = :categoryId";
        $params[':categoryId'] = $selectedCategory;
    }
    $searchCondition = "WHERE " . implode(" AND ", $conditions);
}

// Paginierung: Anzahl Einträge pro Seite und aktueller Seitenindex
$entriesPerPage = 5;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$startIndex = ($currentPage - 1) * $entriesPerPage;

// SQL-Abfrage inklusive Suchbedingung und Paginierung
$sql = "
    SELECT w.*, k.Kategorie_Name, u.username, s.Status_Name
    FROM wiki_eintraege w
    LEFT JOIN kategorien k ON w.Kategorie_ID = k.Kategorie_ID
    LEFT JOIN users u ON w.user_ID = u.user_ID
    LEFT JOIN status s ON w.Status_ID = s.Status_ID
    $searchCondition
    ORDER BY w.Erstellungsdatum DESC
    LIMIT :startIndex, :entriesPerPage
";

$stmt = $pdo->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_STR);
}
$stmt->bindParam(':startIndex', $startIndex, PDO::PARAM_INT);
$stmt->bindParam(':entriesPerPage', $entriesPerPage, PDO::PARAM_INT);
$stmt->execute();
$entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ermittlung der Gesamtanzahl für die Paginierung
$totalEntriesStmt = $pdo->prepare("SELECT COUNT(*) FROM wiki_eintraege w $searchCondition");
foreach ($params as $key => $value) {
    $totalEntriesStmt->bindValue($key, $value, PDO::PARAM_STR);
}
$totalEntriesStmt->execute();
$totalEntries = $totalEntriesStmt->fetchColumn();
$totalPages = ceil($totalEntries / $entriesPerPage);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mein Wiki</title>
	<link rel="stylesheet" href="styles2.css">
	<link rel="shortcut icon" href="https://dragonnetz.de/favicon.png" />
</head>
<body>
    <!-- Header inkl. Suchleiste -->
    <header>
        <div class="header-container">
            <h1>Nutzloses Notizbuch</h1>
            <form class="search-form" method="GET">
                <input type="text" name="search" placeholder="Wiki durchsuchen..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                <select name="category">
                    <option value="">Alle Kategorien</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" <?php echo ($selectedCategory == $category['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Suchen</button>
            </form>
        </div>
    </header>
    <div class="main-container">
        <!-- Sidebar Navigation -->
        <nav class="sidebar">
            <ul>
                <li><a href="https://dragonnetz.de/index.php">Startseite</a></li>
                <li><a href="https://api.dragonnetz.de/admin/admin.php">Admin</a></li>
                <li><a href="https://api.dragonnetz.de/account/register.php">Registrieren</a></li>
                <li><a href="https://api.dragonnetz.de/account/login.php">Anmelden</a></li>
              
                <li><a href="https://api.dragonnetz.de/dash/create.php">Neue Artikel anlegen</a></li>
                <li><a href="https://api.dragonnetz.de/account/index.php">Account</a></li>
                <li><a href="https://dragonnetz.de/chat/v2/">Chatting</a></li>
            </ul>
        </nav>
        <!-- Hauptinhalt -->
        <main>
            <section id="wiki-content">
                <?php if (empty($entries)): ?>
                    <p>Keine Wiki-Einträge gefunden.</p>
                <?php else: ?>
                    <?php foreach ($entries as $entry): ?>
                        <article class="wiki-article">
                            <h2>
                                <a href="https://api.dragonnetz.de/article.php?id=<?= $entry['ID'] ?>" style="color: inherit; text-decoration: none;">
                                    <?= htmlspecialchars($entry['Titel'] ?? 'Kein Titel'); ?>
                                </a>
                            </h2>
                            <p><?= nl2br(limitContent($entry['Inhalt'] ?? 'Kein Inhalt')); ?></p>
                            <div class="entry-meta">
                                <span>Erstellt: <?= isset($entry['Erstellungsdatum']) ? date('d.m.Y', strtotime($entry['Erstellungsdatum'])) : 'Unbekannt' ?></span> |
                                <span>Version: <?= htmlspecialchars($entry['Version'] ?? 'Unbekannt'); ?></span> |
                                <span>Autor: <?= htmlspecialchars($entry['username'] ?? 'Unbekannt'); ?></span> |
                                <span>Kategorie: <?= htmlspecialchars($entry['Kategorie_Name'] ?? 'Unbekannt'); ?></span> |
                                <span>Status: <?= htmlspecialchars($entry['Status_Name'] ?? 'Unbekannt'); ?></span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                    
                    <!-- Paginierung -->
                    <div class="pagination">
                        <div class="pagination-left">
                            <?php if ($currentPage > 1): ?>
                                <a href="?page=<?= $currentPage - 1; ?>&search=<?= urlencode($searchTerm) ?>&category=<?= urlencode($selectedCategory) ?>">&laquo; Vorherige</a>
                            <?php endif; ?>
                        </div>
                        <div class="pagination-center">
                            <?php 
                              $range = 2;
                              for ($i = max(1, $currentPage - $range); $i <= min($totalPages, $currentPage + $range); $i++): 
                            ?>
                                <?php if ($i == $currentPage): ?>
                                    <span class="active"><?= $i; ?></span>
                                <?php else: ?>
                                    <a href="?page=<?= $i; ?>&search=<?= urlencode($searchTerm) ?>&category=<?= urlencode($selectedCategory) ?>"><?= $i; ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                        <div class="pagination-right">
                            <?php if ($currentPage < $totalPages): ?>
                                <a href="?page=<?= $currentPage + 1; ?>&search=<?= urlencode($searchTerm) ?>&category=<?= urlencode($selectedCategory) ?>">Nächste &raquo;</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
    <footer>
        <p>&copy; 2025 Nutzloses Notizbuch, Alle Rechte vorbehalten.</p>
    </footer>
</body>
</html>
