<?php

// Adminer extension, the function is called automatically by adminer
// so don´t set "namespace FriendsOfRedaxo\Adminer;" for this file!

use FriendsOfRedaxo\Adminer\Adminer;

function adminer_object()
{
    // adminer throws warning "A non-numeric value encountered" in PHP 7
    error_reporting(error_reporting() & ~E_WARNING & ~E_NOTICE);

    return new Adminer();
}
