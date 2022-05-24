<?php

namespace p1\view\startpage;
require_once "config.php";

use p1\config\Config;

class StartPageController
{
    function loadComponents(): void
    {
        require_once Config::instance()->rootDir() . '/view/navbar/navbar.php';
    }
}