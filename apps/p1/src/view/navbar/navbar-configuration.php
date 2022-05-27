<?php

namespace p1\view\navbar;

use p1\state\State;

class NavbarConfiguration
{
    private NavbarController $navbarController;

    public function __construct(State $state)
    {
        $this->navbarController = new NavbarController($state);
    }

    public function navbarController(): NavbarController
    {
        return $this->navbarController;
    }

}