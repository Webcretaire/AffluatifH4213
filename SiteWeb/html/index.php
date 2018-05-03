<?php
require_once '../vendor/autoload.php';

session_start();
date_default_timezone_set('Europe/Paris');
setlocale(LC_TIME, 'fr_FR', 'French', 'fr_utf8', 'fr_FR.UTF8', 'fr_FR.UTF-8');

if (!(
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
)) { // Do not log AJAX requests
    if (!isset($_SESSION['UriStack'])) {
        $_SESSION['UriStack'] = [$_SERVER['REQUEST_URI']];
    } else {
        array_unshift($_SESSION['UriStack'], $_SERVER['REQUEST_URI']);
    }
}

$_SESSION['UriStack'] = array_slice($_SESSION['UriStack'], 0, 10);


$ravenClient = new \Raven_Client('https://aba5fa14c2054d999c30138b0c80c8f8:20bd27705e9d4d48af859f3177196fb0@sentry.io/1187502');
$ravenClient->install();
$ravenClient->setEnvironment('developpement');

try {
    \Affluatif\Services\Functions::last(12, 'minutes', 'Y-m-d H:m:s');

    (new \Affluatif\Processing\AuthProcessing())->connexionAuto();
    $router = new \DiggyRouter\Router();
    $router->loadRoutes('../config/routing.yml');
    if(!$router->handleRequest()) {
        header('HTTP/1.0 404 Not Found');
        (new \Affluatif\View\Erreur())->render();
    }
} catch (Exception $ex) {
    $ravenClient->captureException($ex);
}