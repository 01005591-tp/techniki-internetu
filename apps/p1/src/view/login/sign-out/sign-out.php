<?php

use p1\configuration\Configuration;

require_once "configuration.php";

$signOutController = Configuration::instance()->viewConfiguration()->signOutController();
$signOutController->logout();