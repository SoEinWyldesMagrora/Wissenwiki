<?php
// Fehlerberichterstattung aktivieren
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Session starten
session_start();

// Datenbankverbindung
$host = 'localhost';
$dbname = 'mr_codespace_wiki';
$username = 'mr_codespace_wiki';
$password = '~8R5r8i2f';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Datenbankverbindung fehlgeschlagen: " . $e->getMessage());
}

// Login-Check Funktion
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Formatierungsfunktion (unverändert)
function formatContent($content) {
    $lines = explode("\n", $content);
    $formattedContent = '';
    $inList = false;
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) {
            if ($inList) {
                $formattedContent .= "</ul>\n";
                $inList = false;
            }
            continue;
        }
        
        if (preg_match('/^(#{1,6})\s+(.+)$/', $line, $matches)) {
            $level = strlen($matches[1]);
            $formattedContent .= "<h$level>" . htmlspecialchars($matches[2]) . "</h$level>\n";
        }
        elseif (preg_match('/^[-*]\s+(.+)$/', $line, $matches)) {
            if (!$inList) {
                $formattedContent .= "<ul>\n";
                $inList = true;
            }
            $formattedContent .= "<li>" . htmlspecialchars($matches[1]) . "</li>\n";
        }
        else {
            if ($inList) {
                $formattedContent .= "</ul>\n";
                $inList = false;
            }
            $formattedContent .= '<p>' . htmlspecialchars($line) . '</p>' . "\n";
        }
    }
    
    if ($inList) {
        $formattedContent .= "</ul>\n";
    }
    
    return $formattedContent;
}

// Artikel-ID aus der URL abrufen
$articleId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Artikel abrufen
try {
    $stmt = $pdo->prepare("
        SELECT w.*, k.Kategorie_Name, u.username as author, s.Status_Name
        FROM wiki_eintraege w
        LEFT JOIN kategorien k ON w.Kategorie_ID = k.Kategorie_ID
        LEFT JOIN users u ON w.user_ID = u.user_ID 
        LEFT JOIN status s ON w.Status_ID = s.Status_ID
        WHERE w.ID = ?
    ");
    $stmt->execute([$articleId]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$article) {
        die("Artikel nicht gefunden.");
    }
} catch(PDOException $e) {
    die("Fehler beim Abrufen des Artikels: " . $e->getMessage());
}

// Kommentare abrufen
$stmt = $pdo->prepare("SELECT c.*, u.username 
FROM comments c
LEFT JOIN users u ON c.user_id = u.user_ID
WHERE c.article_id = ?");
$stmt->execute([$articleId]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Reaktionen zählen
$stmt = $pdo->prepare("
    SELECT 
        SUM(CASE WHEN reaction = 'like' THEN 1 ELSE 0 END) as likes,
        SUM(CASE WHEN reaction = 'dislike' THEN 1 ELSE 0 END) as dislikes
    FROM reactions
    WHERE article_id = ?
");
$stmt->execute([$articleId]);
$reactions = $stmt->fetch(PDO::FETCH_ASSOC);

// Kommentar hinzufügen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    if (!isLoggedIn()) {
        header("Location: https://api.dragonnetz.de/account/login.php");
        exit();
    }
    $comment = $_POST['comment'];
    $stmt = $pdo->prepare("INSERT INTO comments (user_id, article_id, content, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$_SESSION['user_id'], $articleId, $comment]);
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

// Like/Dislike verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reaction'])) {
    if (!isLoggedIn()) {
        header("Location: https://api.dragonnetz.de/account/login.php");
        exit();
    }
    $reaction = $_POST['reaction'];
    $stmt = $pdo->prepare("
        INSERT INTO reactions (user_id, article_id, reaction)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE reaction = ?
    ");
    $stmt->execute([$_SESSION['user_id'], $articleId, $reaction, $reaction]);
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mein Wiki</title>
    <link rel="stylesheet" href="styles.css">
	<link rel="shortcut icon" href="https://dragonnetz.de/favicon.png" />
    <style>
        
    /* Bestehende Styles beibehalten */
    
    /* Neue Styles für Kommentare und Reaktionen */
   .reactions {
    margin-top: 2em;
    background-color: #f0f0f0;
    padding: 1em;
    border-radius: 5px;
    max-width: 260px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.reactions button {
    margin-right: 1em;
    padding: 0.5em 1em;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.reactions button:last-child {
    margin-right: 0;
}

    .reactions button:hover {
        background-color: #004999;
    }
    
    .comments-section {
        margin-top: 2em;
        background-color: #f9f9f9;
        padding: 1em;
        border-radius: 5px;
    }
    
    .comments-section h3 {
        margin-bottom: 1em;
        color: #333;
    }
    
  .comment-form {
    margin-bottom: 2em;
    background-color: #f0f0f0;
    padding: 1.5em;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.comment-form textarea {
    width: 100%;
    padding: 0.8em;
    margin-bottom: 1em;
    border: 1px solid #ddd;
    border-radius: 4px;
    resize: vertical;
    min-height: 100px;
    font-family: inherit;
    font-size: 1em;
}

.comment-form button {
    padding: 0.8em 1.5em;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.1s;
    font-size: 1em;
    font-weight: bold;
}

.comment-form button:hover {
    background-color: #004999;
}

.comment-form button:active {
    transform: scale(0.98);
}

    .comment {
        background-color: white;
        border: 1px solid #ddd;
        border-radius: 3px;
        padding: 1em;
        margin-bottom: 1em;
    }
    
    .comment p {
        margin-bottom: 0.5em;
    }
    
    .comment-meta {
        font-size: 0.9em;
        color: #666;
    }


		    .article-content {
            max-width: 800px;
            margin: 2em auto;
            line-height: 1.6;
        }
        .paragraph {
            margin-bottom: 1.5em;
            text-align: justify;
            text-indent: 1.5em;
        }
        .entry-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1em;
            margin: 2em 0;
            padding: 1em;
            background: #f5f5f5;
            border-radius: 5px;
        }
        .entry-meta span {
            padding: 1em;
        }
		
		 /* FIXED HEADER */
        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            background-color: var(--header-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 2rem;
        }

        /* CONTENT ADJUSTMENT */
        .main-container {
            margin-top: 80px; /* An Header-Höhe anpassen */
            height: calc(100vh - 80px); /* Viewport-Höhe minus Header */
            overflow: hidden; /* Scrollen im Container verhindern */
        }

        /* FIXED SIDEBAR */
        .sidebar {
            position: fixed;
            top: 80px; /* Unter Header positionieren */
            left: 0;
            width: 250px;
            height: calc(100% - 80px);
            overflow-y: auto; /* Scrollen nur in Sidebar */
        }

        /* MAIN CONTENT AREA */
        main {
            margin-left: 250px;
            height: 100%;
            overflow-y: auto; /* Scrollen nur im Hauptinhalt */
            padding: 20px;
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
                <li><a href="https://api.dragonnetz.de/account/login.php">Anmelden</a></li>
              
                <li><a href="https://api.dragonnetz.de/dash/create.php">Neue Artikel anlegen</a></li>
            </ul>
        </nav>
        <main>
            <article class="full-article">
                <h2><?= htmlspecialchars($article['Titel']) ?></h2>
                <div class="article-content">
                    <?= formatContent($article['Inhalt']) ?>
                </div>
                <div class="entry-meta">
                    <span class="date">Erstellt am: <?= date('d.m.Y', strtotime($article['Erstellungsdatum'])) ?></span>
                    <span class="date">Letzte Änderung: <?= date('d.m.Y', strtotime($article['Letzte_Änderung'])) ?></span>
                    <span class="version">Version: <?= htmlspecialchars($article['Version']) ?></span>
                    <span class="status">Status: <?= htmlspecialchars($article['Status_Name']) ?></span>
                    <span class="category">Kategorie: <?= htmlspecialchars($article['Kategorie_Name']) ?></span>
                    <span class="author">Autor: <?= htmlspecialchars($article['author'] ?? 'Unbekannt') ?></span>
                </div>
<div class="reactions">
            <?php if (isLoggedIn()): ?>
                <form method="POST">
                    <button type="submit" name="reaction" value="like">👍 Like (<?= $reactions['likes'] ?>)</button>
                    <button type="submit" name="reaction" value="dislike">👎 Dislike (<?= $reactions['dislikes'] ?>)</button>
                </form>
            <?php else: ?>
                <p>Bitte <a href="https://api.dragonnetz.de/account/login.php">melden Sie sich an</a>, um zu liken oder zu disliken.</p>
            <?php endif; ?>
        </div>

        <div class="comments-section">
            <h3>Kommentare</h3>
            <?php if (isLoggedIn()): ?>
                <form method="POST" class="comment-form">
                    <textarea name="comment" required placeholder="Schreiben Sie hier Ihren Kommentar..."></textarea>
                    <button type="submit">Kommentar hinzufügen</button>
                </form>
            <?php else: ?>
                <p>Bitte <a href="https://api.dragonnetz.de/account/login.php">melden Sie sich an</a>, um zu kommentieren.</p>
            <?php endif; ?>
            
            <?php foreach ($comments as $comment): ?>
                <div class="comment">
                    <p><?= htmlspecialchars($comment['content']) ?></p>
                    <p class="comment-meta">Von: <?= htmlspecialchars($comment['username'] ?? 'Unbekannt') ?> am <?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    
            </article>
        </main>
    </div>
    <footer>
        <p>&copy; 2025 Nutzloses Notizbuch, Alle Rechte vorbehalten.</p>
    </footer>
    <script src="script.js"></script>
</body>
</html>
