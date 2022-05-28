<?php

namespace p1\view\login;

require_once "state.php";
require_once "core/domain/user/create-user-command-handler.php";
require_once "core/domain/user/auth/authenticate-user-command-handler.php";
require_once "view/login/login-controller.php";
require_once "view/login/sign-up/sign-up-controller.php";
require_once "view/login/sign-up/sign-up-request-validator.php";
require_once "view/session/session-manager.php";

use p1\core\domain\user\auth\AuthenticateUserCommandHandler;
use p1\core\domain\user\CreateUserCommandHandler;
use p1\state\State;
use p1\view\login\signup\SignUpController;
use p1\view\login\signup\SignUpRequestValidator;
use p1\view\session\SessionManager;

class LoginConfiguration
{
    private SignUpController $signUpController;
    private LoginController $loginController;

    public function __construct(State                          $state,
                                CreateUserCommandHandler       $createUserCommandHandler,
                                AuthenticateUserCommandHandler $authenticateUserCommandHandler,
                                SessionManager                 $sessionManager)
    {
        $this->signUpController = new SignUpController(
            new SignUpRequestValidator(),
            $createUserCommandHandler,
            $state
        );
        $this->loginController = new LoginController(
            $authenticateUserCommandHandler,
            $state,
            $sessionManager
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