<?php
/** @var rex_addon $this */
if (true === rex::getProperty('live_mode',false))
    {
    exit;
    }
require_once __DIR__.'/../functions/function_adminer.php';

$databases = [];

foreach (rex::getProperty('db') as $id => $db) {
    if (empty($db['host']) || empty($db['name'])) {
        continue;
    }

    // multiple databases with same name are not supported
    if (isset($databases[$db['name']])) {
        continue;
    }

    $db['id'] = $id;
    $databases[$db['name']] = $db;
}

$database = rex_get('db', 'string');
$database = isset($databases[$database]) ? $databases[$database] : reset($databases);

$this->setProperty('databases', $databases);
$this->setProperty('database', $database);

// auto login and db selection
$_GET['username'] = '';
$_GET['db'] = $database['name'];

// adminer uses `page` parameter for pagination (int), but redaxo initially sets `page=adminer`
// so we remove the page parameter if it is not a numeric string
if (isset($_GET['page']) && $_GET['page'] !== (string) (int) $_GET['page']) {
    if ($_GET['page'] !== 'last') {
        unset($_GET['page']);
    }
}

// workaround against https://github.com/vrana/adminer/commit/15900301eeef0cf22e51f57ed0b7d55b3e822feb
$_SESSION['pwds']['server'][''][$_GET["username"]] = '';

// deactive `throw_always_exception` debug option, because adminer is throwing some notices
if (method_exists(rex::class, 'getDebugFlags')) {
    $debug = rex::getDebugFlags();
    $debug['throw_always_exception'] = false;
    rex::setProperty('debug', $debug);
}

// CSP für die Adminer-Seite anpassen, um inline-scripts zu erlauben
if (method_exists('rex_response', 'setHeader')) {
    rex_response::setHeader('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';");
}

rex_response::cleanOutputBuffers();

// Pfad zur benutzerdefinierten CSS-Datei im assets-Ordner
$customCssUrl = $this->getAssetsUrl('adminer.css');

// add page param to all adminer urls and replace the CSS link
ob_start(function ($output) use ($customCssUrl) {
    // Standard URL-Anpassung
    $output = preg_replace('#(?<==(?:"|\'))index\.php\?(?=username=&amp;db=|file=[^&]*&amp;version=)#', 'index.php?page=adminer&amp;', $output);
    
    // CSS-Link ersetzen, aber nur wenn unsere benutzerdefinierte CSS-Datei existiert
    if (file_exists(rex_path::base(str_replace(rex_url::base(), '', $customCssUrl)))) {
        $output = preg_replace('#<link rel="stylesheet" href="adminer\.css\?v=([^"]*)">#', '<link rel="stylesheet" href="' . $customCssUrl . '">', $output);
    }
    
    return $output;
});

include __DIR__ .'/../vendor/adminer.php';

// make sure the output buffer callback is called
while (ob_get_level()) {
    ob_end_flush();
}
exit;
