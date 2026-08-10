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

// Adminer expects an entry in the password/session map for embedded usage.
// Without it, the wrapper may fall back to the login form even though the
// REDAXO backend user is already authenticated.
$_SESSION['pwds']['server'][''][$_GET['username']] = '';

// adminer uses `page` parameter for pagination (int), but redaxo initially sets `page=adminer`
// so we remove the page parameter if it is not a numeric string
if (isset($_GET['page']) && $_GET['page'] !== (string) (int) $_GET['page']) {
    if ($_GET['page'] !== 'last') {
        unset($_GET['page']);
    }
}

// Adminer adjusts session settings during bootstrap. If REDAXO already has an
// active session, this can trigger a warning from the bundled Adminer file.
// Closing the current session before including Adminer avoids the warning.
if (PHP_SESSION_ACTIVE === session_status()) {
    session_write_close();
}

// deactive `throw_always_exception` debug option, because adminer is throwing some notices
if (method_exists(rex::class, 'getDebugFlags')) {
    $debug = rex::getDebugFlags();
    $debug['throw_always_exception'] = false;
    rex::setProperty('debug', $debug);
}

$adminerErrorReporting = error_reporting();

// CSP für die Adminer-Seite anpassen, um inline-scripts zu erlauben
if (method_exists('rex_response', 'setHeader')) {
    rex_response::setHeader('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';");
}

rex_response::cleanOutputBuffers();

// add page param to all adminer urls
ob_start(function ($output) {
    return preg_replace('#(?<==(?:"|\'))index\.php\?(?=username=&amp;db=|file=[^&]*&amp;version=)#', 'index.php?page=adminer&amp;', $output);
});

include __DIR__ .'/../vendor/adminer.php';
error_reporting($adminerErrorReporting);

// make sure the output buffer callback is called
while (ob_get_level()) {
    ob_end_flush();
}
exit;
