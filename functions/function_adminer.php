<?php
// Für Namespace-Kompatibilität die Funktionsaufrufe einbinden
namespace Adminer;

// Diese Funktionen leiten vom Adminer-Namespace zum globalen Namespace weiter
function connection() {
    return \connection();
}

function adminer() {
    return \adminer();
}

function version() {
    return \version();
}

// Zurück zum globalen Namespace für adminer_object
namespace {
    // Adminer extension, the function is called automatically by adminer
    function adminer_object()
    {
        // adminer throws warning "A non-numeric value encountered" in PHP 7
        error_reporting(error_reporting() & ~E_WARNING & ~E_NOTICE);

        return new rex_adminer();
    }
}
