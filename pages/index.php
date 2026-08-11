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

// Adminer 6 verifies CSRF token submissions against Sec-Fetch-Site.
// In embedded backend contexts this header can be missing or "none"
// even for same-origin form posts, which causes false negatives.
if (rex_request::server('REQUEST_METHOD', 'string', '') === 'POST') {
    $secFetchSite = rex_request::server('HTTP_SEC_FETCH_SITE', 'string', '');
    if ($secFetchSite === '' || strtolower($secFetchSite) === 'none') {
        $_SERVER['HTTP_SEC_FETCH_SITE'] = 'same-origin';
    }

    $postedToken = rex_request::post('token', 'string', '');
    if ($postedToken !== '' && (!isset($_SESSION['token']) || (int) $_SESSION['token'] === 0)) {
        $tokenParts = explode(':', $postedToken, 2);
        if (
            count($tokenParts) === 2
            && ctype_digit($tokenParts[0])
            && ctype_digit($tokenParts[1])
        ) {
            $_SESSION['token'] = ((int) $tokenParts[0]) ^ ((int) $tokenParts[1]);
        }
    }
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

// Adminer adjusts session ini settings during bootstrap.
// Ensure the REDAXO session is closed immediately before include.
if (PHP_SESSION_ACTIVE === session_status()) {
    session_write_close();
}

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
