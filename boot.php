<?php
if (true === rex::getProperty('live_mode',false))
    {
    return; 
    }

if (rex_request('page', 'string') === 'adminer' && rex_request('rex-api-call', 'string', '') !== '') {
    $params = $_GET;
    unset($params['rex-api-call'], $params['_csrf_token']);

    rex_response::sendRedirect(rex_url::backendController($params, false));
}

// Handle adminer calls with missing page parameter
if (rex::isBackend() && rex::getUser() && rex::getUser()->isAdmin() && isset($_GET['username']) && isset($_GET['db'])) {
    $page = rex_be_controller::getCurrentPage();
    if (!$page || $page === (string) (int) $page || $page === 'last') {
        rex_be_controller::setCurrentPage('adminer');
    }
}
