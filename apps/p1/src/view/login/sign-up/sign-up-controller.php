<?php

namespace p1\view\login\signup;

require_once "core/domain/user/create-user-command-handler.php";
require_once "core/domain/user/create-user-command.php";
require_once "core/function/function.php";
require_once "view/login/sign-up/sign-up-request.php";
require_once "view/login/sign-up/sign-up-request-validator.php";

use p1\core\domain\user\CreateUserCommand;
use p1\core\domain\user\CreateUserCommandHandler;
use p1\core\function\Consumer;
use p1\core\function\Either;
use p1\core\function\Function2;

class SignUpController
{
    private SignUpRequestValidator $signUpRequestValidator;
    private CreateUserCommandHandler $createUserCommandHandler;

    public function __construct(SignUpRequestValidator   $signUpRequestValidator,
                                CreateUserCommandHandler $createUserCommandHandler)
    {
        $this->signUpRequestValidator = $signUpRequestValidator;
        $this->createUserCommandHandler = $createUserCommandHandler;
    }

    public function signIn(): void
    {
        if (isset($_POST['sign-up-create-user-btn'])) {
            $request = new SignUpRequest(
                $_POST['signUpEmailInput'],
                $_POST['signUpPasswordInput'],
                $_POST['signUpPasswordRepeatInput']
            );
            $validatedRequest = $this->signUpRequestValidator->validate($request);
            $validatedRequest->mapRight(new CreateCreateUserCommand())
                ->flatMapRight(new HandleCreateUserCommand($this->createUserCommandHandler))
                ->peekLeft(new CreateUserCommandFailedConsumer())
                ->peekRight(new CreateUserCommandSuccessConsumer())
                ->peekRight(new RedirectToHomePageConsumer());
        }
    }
}

class CreateCreateUserCommand implements Function2
{
    function apply($value): CreateUserCommand
    {
        $request = $value;
        return new CreateUserCommand($request->email(), $request->password());
    }
}

class HandleCreateUserCommand implements Function2
{
    private CreateUserCommandHandler $createUserCommandHandler;

    public function __construct(CreateUserCommandHandler $createUserCommandHandler)
    {
        $this->createUserCommandHandler = $createUserCommandHandler;
    }


    function apply($value): Either
    {
        $command = $value;
        return $this->createUserCommandHandler->handle($command);
    }
}

class CreateUserCommandFailedConsumer implements Consumer
{
    function consume($value): void
    {
        $failure = $value;
        echo "<p>" . $failure->message() . "</p>";
    }
}

class CreateUserCommandSuccessConsumer implements Consumer
{
    function consume($value): void
    {
        echo "<p>Success</p>";
    }
}

class RedirectToHomePageConsumer implements Consumer
{
    function consume($value): void
    {
        if (headers_sent()) {
            die('<script type="text/javascript">window.location\'/\';</script>');
        } else {
            header("Location: /");
            die();
        }
    }
}