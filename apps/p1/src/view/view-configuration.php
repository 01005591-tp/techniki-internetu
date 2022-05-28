<?php

namespace p1\view;

require_once "state.php";
require_once "core/domain/user/create-user-command-handler.php";
require_once "core/domain/user/auth/authenticate-user-command-handler.php";
require_once "view/login/login-configuration.php";
require_once "view/login/login-controller.php";
require_once "view/login/sign-up/sign-up-controller.php";
require_once "view/navbar/navbar-configuration.php";
require_once "view/navbar/navbar-controller.php";

use p1\core\domain\user\auth\AuthenticateUserCommandHandler;
use p1\core\domain\user\CreateUserCommandHandler;
use p1\state\State;
use p1\view\login\LoginConfiguration;
use p1\view\login\LoginController;
use p1\view\login\signup\SignUpController;
use p1\view\navbar\NavbarConfiguration;
use p1\view\navbar\NavbarController;

class ViewConfiguration
{
    private LoginConfiguration $loginConfiguration;
    private NavbarConfiguration $navbarConfiguration;

    public function __construct(State                          $state,
                                CreateUserCommandHandler       $createUserCommandHandler,
                                AuthenticateUserCommandHandler $authenticateUserCommandHandler)
    {
        $this->loginConfiguration = new LoginConfiguration(
            $state,
            $createUserCommandHandler,
            $authenticateUserCommandHandler
        );
        $this->navbarConfiguration = new NavbarConfiguration($state);
    }

    public function navbarController(): NavbarController
    {
        return $this->navbarConfiguration->navbarController();
    }

    public function signUpController(): SignUpController
    {
        return $this->loginConfiguration->signUpController();
    }

    public function loginController(): LoginController
    {
        return $this->loginConfiguration->loginController();
    }
}