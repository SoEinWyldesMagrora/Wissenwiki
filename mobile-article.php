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
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mein Wiki</title>
    <link rel="stylesheet" href="mobile-styles.css">
    <link rel="shortcut icon" href="https://dragonnetz.de/favicon.png" />
    <style>
        /* Root-Variablen */
        :root {
            --primary-color: #0066cc;
            --secondary-color: #eceff1;
            --background-color: #f8f9fa;
            --text-color: #212529;
            --muted-color: #6c757d;
            --border-color: #e0e0e0;
        }

        /* Gemeinsame Styles */
		
		.header-link {
    text-decoration: none;
    color: inherit;
    display: block;
    text-align: center;
}

.header-link h1 {
    margin: 0;
    padding: 0.5rem 0;
    font-size: 1.5rem;
    cursor: pointer;
} 
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background-color: var(--secondary-color);
            color: #000000;
            width: 100vw;
            position: relative;
            left: 50%;
            right: 50%;
            margin-left: -50vw;
            margin-right: -50vw;
            padding: 0.5rem 0;
        }

        .header-content {
            max-width: 100%;
            padding: 0 15px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
        }

        h1 {
            font-size: 1.5rem;
            flex-grow: 1;
            text-align: center;
            margin: 0 40px;
        }

        .container {
            width: 100%;
            max-width: 100%;
            padding: 0 15px;
            margin: 0 auto;
            flex: 1;
        }

        main {
            padding: 1rem 0;
        }

        .wiki-article {
            background-color: white;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .wiki-article h2 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .article-content {
            max-width: 800px;
            margin: 2em auto;
            line-height: 1.6;
        }

        .entry-meta {
            font-size: 0.8rem;
            color: var(--muted-color);
            margin-top: 0.5rem;
        }

        footer {
            background-color: var(--secondary-color);
            color: var(--text-color);
            text-align: center;
            width: 100vw;
            position: relative;
            left: 50%;
            right: 50%;
            margin-left: -50vw;
            margin-right: -50vw;
            padding: 1rem 0;
            margin-top: auto;
        }

        .footer-content {
            max-width: 100%;
            padding: 0 15px;
            margin: 0 auto;
        }

        @media (max-width: 767px) {
            header {
                padding: 1rem 0;
            }

            h1 {
                font-size: 1.2rem;
                margin: 0 35px;
            }

            footer {
                padding: 1rem 0;
            }
        }
    </style>
</head>
<body>
   
       <header>
    <a href="https://mobile.dragonnetz.de/index.php" class="header-link">
        <h1>Nutzloses Notizbuch</h1>
    </a>
</header>

 

    <div class="container">
        <main>
            <article class="wiki-article">
    <h2><?= htmlspecialchars($article['Titel']) ?></h2>
    <div class="article-content">
        <?= nl2br(htmlspecialchars($article['Inhalt'])) ?>
    </div>
    <div class="entry-meta">
        <span class="date">Erstellt am: <?= date('d.m.Y', strtotime($article['Erstellungsdatum'])) ?></span>
        <span class="date">Letzte Änderung: <?= date('d.m.Y', strtotime($article['Letzte_Änderung'])) ?></span>
        <span class="version">Version: <?= htmlspecialchars($article['Version']) ?></span>
        <span class="status">Status: <?= htmlspecialchars($article['Status_Name']) ?></span>
        <span class="category">Kategorie: <?= htmlspecialchars($article['Kategorie_Name']) ?></span>
        <span class="author">Autor: <?= htmlspecialchars($article['author'] ?? 'Unbekannt') ?></span>
    </div>
</article>

        </main>
    </div>

    <footer>
        <div class="footer-content">
            <p>&copy; 2025 Nutzloses Notizbuch, Alle Rechte vorbehalten.</p>
        </div>
    </footer>
</body>
</html>
