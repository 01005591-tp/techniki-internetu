<?php

namespace p1\view\login;

require_once "state.php";
require_once "core/domain/failure.php";
require_once "core/function/either.php";
require_once "core/function/function.php";
require_once "core/domain/user/auth/authenticate-user-command.php";
require_once "core/domain/user/auth/authenticate-user-command-handler.php";
require_once "view/login/login-request.php";
require_once "view/session/session-manager.php";

use L;
use p1\core\domain\Failure;
use p1\core\domain\user\auth\AuthenticateUserCommand;
use p1\core\domain\user\auth\AuthenticateUserCommandHandler;
use p1\core\function\Consumer;
use p1\core\function\Either;
use p1\core\function\Function2;
use p1\state\State;
use p1\view\session\SessionManager;
use p1\view\session\UserContext;

class LoginController
{
    private AuthenticateUserCommandHandler $authenticateUserCommandHandler;
    private State $state;
    private SessionManager $sessionManager;

    public function __construct(AuthenticateUserCommandHandler $authenticateUserCommandHandler,
                                State                          $state,
                                SessionManager                 $sessionManager)
    {
        $this->authenticateUserCommandHandler = $authenticateUserCommandHandler;
        $this->state = $state;
        $this->sessionManager = $sessionManager;
    }

    public function login(): void
    {
        if (isset($_POST['login-login-btn'])) {
            $request = new LoginRequest(
                $_POST['loginEmailInput'],
                $_POST['loginPasswordInput']
            );
            $this->state->put(State::LOGIN_FORM_PROVIDED_EMAIL, $request->email());
            $this->verifyEmailAddress($request)
                ->mapRight(new CreateAuthenticateUserCommandFunction())
                ->flatMapRight(new HandleAuthenticateUserCommandFunction(
                    $this->authenticateUserCommandHandler
                ))
                ->peekLeft(new AuthenticateUserFailedConsumer())
                ->peekRight(new AuthenticateUserSuccessConsumer(
                    $this->state,
                    $this->sessionManager
                ));
        } else {
            $this->state->remove(State::LOGIN_FORM_PROVIDED_EMAIL);
        }
    }

    public function currentEmailAddress(): string
    {
        $currentEmail = $this->state->get(State::LOGIN_FORM_PROVIDED_EMAIL);
        return (isset($currentEmail)) ? $currentEmail : '';
    }

    private function verifyEmailAddress(LoginRequest $request): EIther
    {
        $email = $request->email();
        if (!isset($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return Either::ofLeft(Failure::of(L::main_errors_sign_in_request_invalid_email));
        }
        return Either::ofRight($request);
    }
}

class CreateAuthenticateUserCommandFunction implements Function2
{
    function apply($value): AuthenticateUserCommand
    {
        $request = $value;
        return new AuthenticateUserCommand($request->email(), $request->password());
    }
}

class HandleAuthenticateUserCommandFunction implements Function2
{
    private AuthenticateUserCommandHandler $authenticateUserCommandHandler;

    public function __construct(AuthenticateUserCommandHandler $authenticateUserCommandHandler)
    {
        $this->authenticateUserCommandHandler = $authenticateUserCommandHandler;
    }

    function apply($value): Either
    {
        $command = $value;
        return $this->authenticateUserCommandHandler->handle($command);
    }
}

class AuthenticateUserFailedConsumer implements Consumer
{
    function consume($value): void
    {
        $failure = $value;
        // TODO: implement error message printer
        echo '<script type="text/javascript">alert(\'' . $failure->message() . '\');</script>';
    }
}

class AuthenticateUserSuccessConsumer implements Consumer
{
    private State $state;
    private SessionManager $sessionManager;

    public function __construct(State          $state,
                                SessionManager $sessionManager)
    {
        $this->state = $state;
        $this->sessionManager = $sessionManager;
    }

    function consume($value): void
    {
        $authResult = $value;
        $this->state->remove(State::LOGIN_FORM_PROVIDED_EMAIL);
        $this->sessionManager->sessionStart(new UserContext(
            $authResult->userId(),
            $authResult->userEmail(),
            // TODO: add language support
            'en',
            $authResult->userRoles()
        ));
        if (headers_sent()) {
            echo('<script type="text/javascript">window.location\'/\';</script>');
        } else {
            header("Location: /");
        }
        exit();
    }

}