## Automatisch (empfohlen)

Der Workflow `.github/workflows/update-adminer-vendor.yml` prüft jeden Montag automatisch auf neue [Adminer-Releases](https://github.com/vrana/adminer/releases) und öffnet bei einer neuen Version einen Pull Request.

Manuell auslösen:  
**GitHub → Actions → "Update Adminer vendor" → Run workflow**

## Manuell

* Aktuelle Datei `adminer-X.Y.Z-mysql.php` von https://github.com/vrana/adminer/releases herunterladen.
* Die bestehende `vendor/adminer.php` ersetzen.
* In `package.yml` den Wert `vendor:` auf die neue Version aktualisieren (z. B. `'5.4.2 adminer'`).
* Eintrag im `CHANGELOG.md` ergänzen.
