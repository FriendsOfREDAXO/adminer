<?php

/** @var rex_addon $this */

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

// deactive `throw_always_exception` debug option, because adminer is throwing some notices
$debug = rex::getDebugFlags();
$debug['throw_always_exception'] = false;
rex::setProperty('debug', $debug);

rex_response::cleanOutputBuffers();

// add page param to all adminer urls
ob_start(function ($output) {
    return preg_replace('#(?<==(?:"|\'))index\.php\?(?=username=&amp;db=|file=[^&]*&amp;version=)#', 'index.php?page=adminer&amp;', $output);
});

include __DIR__ .'/../vendor/adminer.php';

// make sure the output buffer callback is called
while (ob_get_level()) {
    ob_end_flush();
}

exit;
