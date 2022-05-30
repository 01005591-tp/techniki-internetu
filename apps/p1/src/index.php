<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/styles/custom.css"/>

    <title>Library</title>
    <link rel="icon" type="image/svg" href="assets/book-icon.svg"/>
</head>
<body>
<?php
require_once "configuration.php";
require_once "i18n/i18n-configuration.php";
require_once "routing.php";
require_once "session/session-manager.php";

use p1\configuration\Configuration;
use p1\i18n\I18nConfiguration;
use p1\routing\Router;
use p1\view\session\SessionManager;

I18nConfiguration::instance();
$sessionManager = SessionManager::instance();
$sessionManager->recoverSession();
$configuration = Configuration::instance();
Router::navigate();
?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2"
        crossorigin="anonymous"></script>
</body>
</html>