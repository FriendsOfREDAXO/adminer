<?php

// Adminer extension, the function is called automatically by adminer
// so don´t set "namespace FriendsOfRedaxo\Adminer;" for this file!

use FriendsOfRedaxo\Adminer\Adminer;

function adminer_object()
{
    return new Adminer();
}
