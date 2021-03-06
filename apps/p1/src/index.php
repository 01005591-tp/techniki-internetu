<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <!-- Custom styles -->
    <link rel="stylesheet" href="/assets/styles/custom.css"/>
    <!-- JQuery 3.6.0 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <!-- JQuery-UI 1.13.1 -->
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"
            integrity="sha256-eTyxS0rkjpLEo16uXTS0uVCS4815lc40K2iVpWDvdSY=" crossorigin="anonymous"></script>
    <!-- JQuery Themes 1.12.1 -->
    <link href="https://code.jquery.com/ui/1.13.1/themes/smoothness/jquery-ui.css" rel="stylesheet"
          crossorigin="anonymous">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2"
            crossorigin="anonymous"></script>

    <!-- Custom JS -->
    <script src="/assets/scripts/custom.js"></script>

    <title>Library</title>
    <link rel="icon" type="image/svg" href="/assets/book-icon.svg"/>
</head>
<body>
<?php
require_once "configuration.php";
require_once "i18n/i18n-configuration.php";
require_once "routing.php";
require_once "session/session-manager.php";
require_once "view/routing/routing-configuration.php";

use p1\configuration\Configuration;
use p1\i18n\I18nConfiguration;
use p1\routing\Router;
use p1\session\SessionManager;
use p1\view\routing\RoutingConfiguration;

I18nConfiguration::instance();
$sessionManager = SessionManager::instance();
$sessionManager->recoverSession();
$configuration = Configuration::instance();
$routingConfiguration = new RoutingConfiguration($sessionManager);
$router = new Router($sessionManager, $routingConfiguration);
$router->navigate();
?>

<!-- FontAwesome solid free version -->
<script src="https://use.fontawesome.com/releases/v6.1.1/js/solid.js"
        integrity="sha384-KPytPVc+hwHwX9HXl4tA7SWJ0Sob6StzjVRoxC4Q4U0JgXujpuVrkBxR0Hsf8A25"
        crossorigin="anonymous"></script>
<script src="https://use.fontawesome.com/releases/v6.1.1/js/fontawesome.js"
        integrity="sha384-9zErGp+biBilRrlpD1l3ExnaqXc8QLITlNpGtb4OL6W1JChl0wwmDNs4U/0UA8L8"
        crossorigin="anonymous"></script>

<script>
    const popoverTriggers = document.querySelectorAll('[data-bs-toggle="popover"]');
    const popovers = [...popoverTriggers].map(it => new bootstrap.Popover(it));
</script>
</body>
</html>