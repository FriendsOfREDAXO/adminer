# [Adminer](https://github.com/vrana/adminer) als REDAXO-Addon

Datenbank-Verwaltung in REDAXO, ohne dass dafür Login-Daten eingegeben werden müssen.

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/adminer/assets/adminer.png)

## Features

- Stellt Adminer 5.0.5 zur Datenbankverwaltung und Entwicklung bereit
- Generiert "rex_sql_table Code" aus bestehenden Tabellen zur weiteren Verwendung z.B. in AddOn-Installationen
- Unterstützt mehrere Datenbanken aus der REDAXO-Konfiguration

## Installation

1. Im REDAXO-Backend unter "Installer" das Addon "adminer" suchen und installieren
2. Oder alternativ: ZIP-Datei im Addon-Verzeichnis `/redaxo/src/addons/` entpacken und die entpackte Datei in `adminer` umbenennen
3. In REDAXO unter "AddOns" das Addon aktivieren

## Verwendung

Nach der Installation ist Adminer über das Hauptmenü im REDAXO-Backend unter "Adminer" erreichbar. Die Login-Daten werden automatisch aus der REDAXO-Konfiguration übernommen.

### Mehrere Datenbanken verwalten

Adminer kann alle in der `config.yml` konfigurierten Datenbanken verwalten. Zwischen den Datenbanken kann über die Dropdown-Auswahl gewechselt werden.

### rex_sql_table Code generieren

Bei der Anzeige einer Tabellen-Struktur kann der PHP-Code für die entsprechende `rex_sql_table`-Definition durch Klick auf den Link "rex_sql_table code" angezeigt werden. Dieser Code kann direkt in die `install.php` eines Addons übernommen werden.

## Kompatibilität

- REDAXO 5.x
- PHP 7.4+, PHP 8.x
- Basiert auf Adminer 5.0.5

## Lizenz

MIT-Lizenz, siehe [LICENSE](LICENSE).

## Autor

Friends Of REDAXO  
https://github.com/FriendsOfREDAXO/adminer
