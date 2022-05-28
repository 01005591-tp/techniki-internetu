<?php

namespace p1\view\login\signup;

require_once "state.php";
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
use p1\state\State;

class SignUpController
{
    private SignUpRequestValidator $signUpRequestValidator;
    private CreateUserCommandHandler $createUserCommandHandler;
    private State $state;

    public function __construct(SignUpRequestValidator   $signUpRequestValidator,
                                CreateUserCommandHandler $createUserCommandHandler,
                                State                    $state)
    {
        $this->signUpRequestValidator = $signUpRequestValidator;
        $this->createUserCommandHandler = $createUserCommandHandler;
        $this->state = $state;
    }

    public function signIn(): void
    {
        if (isset($_POST['sign-up-create-user-btn'])) {
            $request = new SignUpRequest(
                $_POST['signUpEmailInput'],
                $_POST['signUpPasswordInput'],
                $_POST['signUpPasswordRepeatInput']
            );
            $this->state->put(State::SIGN_UP_FORM_PROVIDED_EMAIL, $request->email());
            $this->signUpRequestValidator->validate($request)
                ->mapRight(new CreateCreateUserCommand())
                ->flatMapRight(new HandleCreateUserCommand($this->createUserCommandHandler))
                ->peekLeft(new CreateUserCommandFailedConsumer())
                ->peekRight(new CreateUserCommandSuccessConsumer($this->state))
                ->peekRight(new RedirectToHomePageConsumer());
        } else {
            $this->state->remove(State::SIGN_UP_FORM_PROVIDED_EMAIL);
        }
    }

    public function currentEmailAddress(): string
    {
        $currentEmail = $this->state->get(State::SIGN_UP_FORM_PROVIDED_EMAIL);
        return (isset($currentEmail)) ? $currentEmail : '';
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
        // TODO: implement error message printer
        echo '<script type="text/javascript">alert(\'' . $failure->message() . '\');</script>';
    }
}

class CreateUserCommandSuccessConsumer implements Consumer
{
    private State $state;

    public function __construct(State $state)
    {
        $this->state = $state;
    }

    function consume($value): void
    {
        $this->state->remove(State::SIGN_UP_FORM_PROVIDED_EMAIL);
    }
}

class RedirectToHomePageConsumer implements Consumer
{
    function consume($value): void
    {
        if (headers_sent()) {
            echo('<script type="text/javascript">window.location\'/\';</script>');
        } else {
            header("Location: /");
        }
        exit();
    }
}