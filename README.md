
# Wissenswiki

Willkommen beim Wissenswiki! Dieses Wiki enthält nützliche Informationen, Tutorials und technische Details zu verschiedenen Themen. Es ist darauf ausgelegt, leicht in deine eigenen Projekte integriert zu werden. Du kannst es nutzen, um deine eigenen Wissensdatenbanken zu erstellen und zu verwalten.

## Funktionen

- **Einfache Verwaltung** von Artikeln und Seiten
- **Benutzerfreundliche Oberfläche** zur Bearbeitung und Anzeige von Inhalten
- **SQL-basierte Speicherung** für eine zuverlässige und strukturierte Datenhaltung
- **Suchfunktion**, um schnell den gewünschten Artikel zu finden
- **Mehrsprachige Unterstützung** (kann bei Bedarf hinzugefügt werden)

## Installation

Hier erklären wir dir, wie du das Wissenswiki auf deinem eigenen Server installieren kannst. Die Installation erfordert die Verwendung einer SQL-Datenbank und einige grundlegende Web-Technologien.

### Voraussetzungen

- Webserver (z.B. Apache oder Nginx)
- PHP 7.4 oder höher
- MySQL oder MariaDB
- Zugang zu einer SQL-Datenbank

### Schritt 1: Dateien herunterladen

Lade die Dateien für das Wissenswiki von [unserem GitHub-Repository](https://github.com/dein-wissenswiki-repository) herunter oder klone das Repository:

```bash
git clone https://github.com/dein-wissenswiki-repository.git
```

### Schritt 2: Datenbank einrichten

Erstelle eine neue MySQL- oder MariaDB-Datenbank, um die Daten des Wikis zu speichern. Dies kannst du über phpMyAdmin oder direkt über die MySQL-CLI tun.

```sql
CREATE DATABASE wissenswiki;
```

### Schritt 3: SQL-Datenbank importieren

Im Repository findest du eine Datei `install.sql`, die alle notwendigen Tabellen für das Wiki enthält. Lade diese SQL-Datei in deine Datenbank hoch.

Mit phpMyAdmin:
1. Gehe zu phpMyAdmin und wähle die erstellte Datenbank aus.
2. Klicke auf „Importieren“ und lade die Datei `install.sql` hoch.

Mit der MySQL-CLI:
```bash
mysql -u benutzername -p wissenswiki < install.sql
```

### Schritt 4: Konfiguration der Datenbankverbindung

Öffne die Datei `config.php` im Hauptverzeichnis des Wikis und passe die Datenbank-Verbindungsdetails an:

```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'wissenswiki');
define('DB_USER', 'dein_benutzername');
define('DB_PASS', 'dein_passwort');
?>
```

### Schritt 5: Webserver konfigurieren

Stelle sicher, dass dein Webserver auf das Verzeichnis zeigt, in dem das Wiki gespeichert ist. Wenn du Apache verwendest, kannst du dies in der `httpd.conf` oder der entsprechenden Konfigurationsdatei tun:

```apache
DocumentRoot /pfad/zum/wissenswiki
<Directory /pfad/zum/wissenswiki>
    AllowOverride All
    Require all granted
</Directory>
```

### Schritt 6: Wiki verwenden

Nachdem du die oben genannten Schritte abgeschlossen hast, kannst du das Wissenswiki durch Öffnen der URL in deinem Browser erreichen:

```
http://dein-server.de/wissenswiki
```

Melde dich mit den Standard-Admin-Zugangsdaten an (dies können die in der Datenbank hinterlegten Standardbenutzer sein).

## Benutzerverwaltung

Du kannst Benutzer erstellen, verwalten und ihnen bestimmte Rechte zuweisen, um zu steuern, wer Artikel erstellen oder bearbeiten kann. Gehe dazu ins Admin-Panel und klicke auf „Benutzer verwalten“.

## Funktionen und Nutzung

- **Artikel erstellen:** Gehe zum Dashboard und klicke auf "Neuen Artikel erstellen".
- **Artikel bearbeiten:** Wähle einen Artikel aus und klicke auf „Bearbeiten“.
- **Artikel durchsuchen:** Nutze die Suchleiste oben, um nach spezifischen Themen zu suchen.

## Häufige Probleme

### Problem: Die Seite lädt nicht

- Überprüfe, ob dein Webserver ordnungsgemäß konfiguriert ist.
- Stelle sicher, dass die `config.php` korrekt auf deine Datenbank zeigt.

### Problem: Fehler beim Laden der Datenbank

- Prüfe, ob der MySQL-Dienst läuft.
- Vergewissere dich, dass die Datenbank- und Benutzerdaten in der `config.php` korrekt sind.

## Unterstützung

Wenn du Hilfe bei der Installation oder Verwendung des Wikis benötigst, kannst du dich über [unsere Support-Seite](https://deine-support-seite.de) an uns wenden.

## Lizenz

Dieses Projekt ist unter der [GNU General Public License v3.0](LICENSE) lizenziert.

Wir hoffen, dass du das Wissenswiki erfolgreich in dein Projekt integrieren kannst. Bei weiteren Fragen oder Anregungen stehen wir jederzeit zur Verfügung!
