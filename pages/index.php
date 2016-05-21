<?php

function adminer_object()
{
    return new rex_adminer();
}

$_GET['username'] = '';
$_GET['db'] = rex::getProperty('db')[1]['name'];
$GLOBALS['rg'] = &$_SESSION['translations'];

rex_response::cleanOutputBuffers();

ob_start(function ($output) {
    return preg_replace('#(?<==(?:"|\'))index\.php\?(?=username=&amp;db=|file=[^&]*&amp;version=)#', 'index.php?page=adminer&amp;', $output);
});

include __DIR__ .'/../vendor/adminer.php';

while (ob_get_level()) {
    ob_end_flush();
}

exit;
