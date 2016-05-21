<?php

// Handle adminer redirects with missing page parameter
if (
    rex::isBackend() && rex::getUser() && rex::getUser()->isAdmin() &&
    !rex_be_controller::getCurrentPage() &&
    isset($_GET['username']) && isset($_GET['db'])
) {
    rex_be_controller::setCurrentPage('adminer');
}
