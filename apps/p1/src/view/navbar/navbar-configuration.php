<?php

namespace p1\view\navbar;

require_once "session/session-manager.php";
require_once "view/navbar/navbar-controller.php";

use p1\state\State;
use p1\view\session\SessionManager;

class NavbarConfiguration
{
    private NavbarController $navbarController;

    public function __construct(State          $state,
                                SessionManager $sessionManager)
    {
        $this->navbarController = new NavbarController(
            $state,
            $sessionManager
        );
    }

    public function navbarController(): NavbarController
    {
        return $this->navbarController;
    }

}