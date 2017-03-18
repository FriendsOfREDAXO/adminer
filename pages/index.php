<?php

// Adminer extension, the function is called automatically by adminer
function adminer_object()
{
    // adminer throws warning "A non-numeric value encountered" in PHP 7
    error_reporting(error_reporting() & ~E_WARNING & ~E_NOTICE);

    return new rex_adminer();
}

// auto login and db selection
$_GET['username'] = '';
$_GET['db'] = rex::getProperty('db')[1]['name'];

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
