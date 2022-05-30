<?php

use p1\configuration\Configuration;

require_once "configuration.php";

$alertPrinter = Configuration::instance()->viewConfiguration()->alertPrinter();
$alertPrinter->displayAlerts();

