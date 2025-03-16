<?php
// Adminer extension, the function is called automatically by adminer
function adminer_object()
{
    // adminer throws warning "A non-numeric value encountered" in PHP 7
    error_reporting(error_reporting() & ~E_WARNING & ~E_NOTICE);
    
    // Lade die Namespace-Funktionen
    require_once __DIR__.'/adminer_namespace_functions.php';
    
    return new rex_adminer();
}
