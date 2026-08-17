# [Adminer](https://github.com/vrana/adminer) als REDAXO-Addon

Datenbank-Verwaltung in REDAXO, ohne dass dafür Login-Daten eingegeben werden müssen.

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/adminer/assets/adminer.png)

## Features

- Stellt Adminer 6.0.1 zur Datenbankverwaltung und Entwicklung bereit
- Generiert `rex_sql_table`-Code für `install.php` und `uninstall.php`
- Markiert registrierte YForm-Tabellen in der Seitenleiste und verlinkt ihre Feldkonfiguration
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

Bei der Anzeige einer Tabellen-Struktur kann der PHP-Code für die entsprechende `rex_sql_table`-Definition durch Klick auf den Link "rex_sql_table code" angezeigt werden. Über den Umschalter kann zwischen dem Installationscode und dem `drop()`-Code für die `uninstall.php` gewechselt werden. Der Kopierbutton übernimmt jeweils den sichtbaren Code, und die Darstellung folgt Adminers globalem Dark-/Light-Modus. Tabellen aus dem YForm Table Manager sind in der linken Tabellenliste markiert. In ihrer Strukturansicht weist eine Warnung auf die Pflege über YForm hin und führt direkt zur YForm-Feldkonfiguration; die Bearbeitung in Adminer bleibt möglich.

## Kompatibilität

- REDAXO 5.x
- PHP 7.4+, PHP 8.x
- Basiert auf Adminer 6.0.1

## Vendor-Integrität und Security-Scanner

Das AddOn enthält den unveränderten offiziellen **Adminer 6.0.1 MySQL-Build**. Zum Prüfzeitpunkt `2026-08-17` ist dies die neueste stabile Adminer-Version. Quelle, Dateigröße und SHA-256-Prüfsumme sind in [`vendor-checksum.yml`](vendor-checksum.yml) dokumentiert. Der eingebettete Vendor wurde bytegenau mit dem offiziellen GitHub-Release-Asset verglichen.

Adminer kann von Security-Scannern aufgrund seiner Funktion als Datenbank-Verwaltungswerkzeug heuristisch als potenzielles Risiko gemeldet werden. Für eine gezielte Scanner-Ausnahme sollten ausschließlich der dokumentierte Pfad und die SHA-256-Prüfsumme freigegeben werden. Eine pauschale Ausnahme für PHP-Dateien wird nicht empfohlen.

Die wöchentlich laufende GitHub Action prüft auf neue offizielle Adminer-Releases. Bei einem Vendor-Update aktualisiert sie auch `vendor-checksum.yml`; bei jedem Lauf wird die vorhandene Prüfsumme validiert. Im REDAXO-Live-Mode ist das AddOn nicht verfügbar. Eine automatische Löschung des Vendors findet bewusst nicht statt, damit Entwicklungssysteme bei Hostern weiter unterstützt werden.

## Lizenz

MIT-Lizenz, siehe [LICENSE](LICENSE).

## Autor

Friends Of REDAXO  
https://github.com/FriendsOfREDAXO/adminer
