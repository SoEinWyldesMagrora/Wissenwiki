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
} catch(PDOException $e) {
    die("Datenbankverbindung fehlgeschlagen: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin-Bereich</title>
    <link rel="stylesheet" href="styles.css">
	<link rel="shortcut icon" href="https://dragonnetz.de/favicon.png" />
</head>
<body>
    <header>
        <div class="header-container">
            <h1>Admin-Bereich</h1>
           
        </div>
    </header>
    <div class="main-container">
        <nav class="sidebar">
            <ul>
                <li><a href="https://api.dragonnetz.de/admin/admin_wiki_entries.php">Wiki-Einträge-verwalten</a></li>
                <li><a href="https://api.dragonnetz.de/admin/admin_users.php">Benutzer verwalten</a></li>
                <li><a href="https://dragonnetz.de/index.php">Zurück zum Wiki</a></li>
                <li><a href="https://api.dragonnetz.de/account/logout.php">Abmelden</a></li>
            </ul>
        </nav>
        <main>
            <h2>Willkommen im Admin-Bereich</h2>
            <p>Wählen Sie eine Option aus dem Menü, um die Verwaltung zu beginnen.</p>
        </main>
    </div>
    <footer>
        <p>&copy; 2025 Nutzloses Notizbuch, Alle Rechte vorbehalten.</p>
    </footer>
</body>
</html>
