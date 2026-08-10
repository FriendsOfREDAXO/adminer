<?php

// Adminer extension, the function is called automatically by adminer
// so don´t set "namespace FriendsOfRedaxo\Adminer;" for this file!

use FriendsOfRedaxo\Adminer\Adminer;

function adminer_object()
{
    $currentErrorReporting = error_reporting();

    // Workaround for legacy PHP 7 warnings during Adminer bootstrap.
    if (PHP_VERSION_ID < 80000) {
        error_reporting($currentErrorReporting & ~E_WARNING & ~E_NOTICE);
    }

    try {
        return new Adminer();
    } finally {
        if (PHP_VERSION_ID < 80000) {
            error_reporting($currentErrorReporting);
        }
    }
}
