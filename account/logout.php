<?php
// Starten der Session
session_start();

// Löschen aller Session-Variablen
$_SESSION = array();

// Löschen des Session-Cookies
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Zerstören der Session
session_destroy();

// Weiterleitung zur Login-Seite
header("Location: login.php");
exit();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abgemeldet</title>
    <link rel="stylesheet" href="styles.css">
	<link rel="shortcut icon" href="https://dragonnetz.de/favicon.png" />
</head>
<body>
    <div class="container">
        <h1>Abgemeldet</h1>
        <p>Sie wurden erfolgreich abgemeldet.</p>
        <p><a href="login.php">Zurück zur Anmeldeseite</a></p>
    </div>
</body>
</html>
