<?php

namespace p1\view\login;

require_once "state.php";
require_once "core/domain/user/create-user-command-handler.php";
require_once "core/domain/user/auth/authenticate-user-command-handler.php";
require_once "view/login/login-controller.php";
require_once "view/login/sign-up/sign-up-controller.php";
require_once "view/login/sign-up/sign-up-request-validator.php";

use p1\core\domain\user\auth\AuthenticateUserCommandHandler;
use p1\core\domain\user\CreateUserCommandHandler;
use p1\state\State;
use p1\view\login\signup\SignUpController;
use p1\view\login\signup\SignUpRequestValidator;

class LoginConfiguration
{
    private SignUpController $signUpController;
    private LoginController $loginController;

    public function __construct(State                          $state,
                                CreateUserCommandHandler       $createUserCommandHandler,
                                AuthenticateUserCommandHandler $authenticateUserCommandHandler)
    {
        $this->signUpController = new SignUpController(
            new SignUpRequestValidator(),
            $createUserCommandHandler,
            $state
        );
        $this->loginController = new LoginController(
            $authenticateUserCommandHandler,
            $state
        );
    }

    public function signUpController(): SignUpController
    {
        return $this->signUpController;
    }

    public function loginController(): LoginController
    {
        return $this->loginController;
    }
}