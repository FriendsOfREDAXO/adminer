<?php

// Adminer extension, the function is called automatically by adminer
function adminer_object()
{
    return new rex_adminer();
}

// auto login and db selection
$_GET['username'] = '';
$_GET['db'] = rex::getProperty('db')[1]['name'];

// becaause adminer is not included in global scope this var must be made global
$GLOBALS['rg'] = &$_SESSION['translations'];

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
