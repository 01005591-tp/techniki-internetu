<?php

namespace p1\view\login;

require_once "core/domain/user/create-user-command-handler.php";
require_once "view/login/sign-up/sign-up-controller.php";
require_once "view/login/sign-up/sign-up-request-validator.php";

use p1\core\domain\user\CreateUserCommandHandler;
use p1\view\login\signup\SignUpController;
use p1\view\login\signup\SignUpRequestValidator;

class LoginConfiguration
{
    private SignUpRequestValidator $signUpRequestValidator;
    private SignUpController $signUpController;

    public function __construct(CreateUserCommandHandler $createUserCommandHandler)
    {
        $this->signUpRequestValidator = new SignUpRequestValidator();
        $this->signUpController = new SignUpController(
            $this->signUpRequestValidator,
            $createUserCommandHandler
        );
    }

    public function signUpController(): SignUpController
    {
        return $this->signUpController;
    }
}