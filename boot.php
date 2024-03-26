<?php
if (true === rex::getProperty('live_mode',false))
    {
    return; 
    }
// Handle adminer calls with missing page parameter
if (rex::isBackend() && rex::getUser() && rex::getUser()->isAdmin() && isset($_GET['username']) && isset($_GET['db'])) {
    $page = rex_be_controller::getCurrentPage();
    if (!$page || $page === (string) (int) $page || $page === 'last') {
        rex_be_controller::setCurrentPage('adminer');
    }
}
