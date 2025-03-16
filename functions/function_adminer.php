<?php

// Adminer extension, the function is called automatically by adminer
function adminer_object()
{
    // adminer throws warning "A non-numeric value encountered" in PHP 7
    error_reporting(error_reporting() & ~E_WARNING & ~E_NOTICE);

    return new rex_adminer();
}

// Für Namespace-Kompatibilität die Funktionsaufrufe einbinden
namespace Adminer;

// Die Klasse rex_adminer wird im globalen Namespace definiert
// Kein use-Statement nötig, da wir mit vollqualifizierten Namen arbeiten

function connection() {
    return \connection();
}

function adminer() {
    return \adminer();
}

function version() {
    return \version();
}