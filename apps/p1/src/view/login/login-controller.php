<?php

namespace p1\view\login;

require_once "core/domain/failure.php";
require_once "core/function/either.php";
require_once "core/function/function.php";
require_once "core/domain/user/auth/authenticate-user-command.php";
require_once "core/domain/user/auth/authenticate-user-command-handler.php";
require_once "session/session-manager.php";
require_once "state.php";
require_once "view/alerts/alert-service.php";
require_once "view/redirect-manager.php";
require_once "view/login/login-request.php";

use L;
use p1\core\domain\Failure;
use p1\core\domain\user\auth\AuthenticateUserCommand;
use p1\core\domain\user\auth\AuthenticateUserCommandHandler;
use p1\core\function\Consumer;
use p1\core\function\Either;
use p1\core\function\Function2;
use p1\state\State;
use p1\view\alerts\AlertService;
use p1\view\RedirectManager;
use p1\view\RedirectToMainPageRunnable;
use p1\view\session\SessionManager;
use p1\view\session\UserContext;

class LoginController
{
    private AuthenticateUserCommandHandler $authenticateUserCommandHandler;
    private State $state;
    private SessionManager $sessionManager;
    private RedirectManager $redirectManager;
    private AlertService $alertService;

    public function __construct(AuthenticateUserCommandHandler $authenticateUserCommandHandler,
                                State           $state,
                                SessionManager  $sessionManager,
                                RedirectManager $redirectManager,
                                AlertService    $alertService)
    {
        $this->authenticateUserCommandHandler = $authenticateUserCommandHandler;
        $this->state = $state;
        $this->sessionManager = $sessionManager;
        $this->redirectManager = $redirectManager;
        $this->alertService = $alertService;
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
                ->peekLeft(new AuthenticateUserFailedConsumer($this->alertService))
                ->peekRight(new AuthenticateUserSuccessConsumer(
                    $this->state,
                    $this->sessionManager,
                    $this->redirectManager->redirectToMainPage(),
                    $this->alertService
                ));
        } else if (!is_null($this->sessionManager->userContext())) {
            $this->redirectManager->redirectToMainPage()->run();
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
    private AlertService $alertService;

    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    function consume($value): void
    {
        $failure = $value;
        $this->alertService->error($failure->message());
    }
}

class AuthenticateUserSuccessConsumer implements Consumer
{
    private State $state;
    private SessionManager $sessionManager;
    private RedirectToMainPageRunnable $redirectToMainPageRunnable;
    private AlertService $alertService;

    public function __construct(State                      $state,
                                SessionManager             $sessionManager,
                                RedirectToMainPageRunnable $redirectToMainPageRunnable,
                                AlertService               $alertService)
    {
        $this->state = $state;
        $this->sessionManager = $sessionManager;
        $this->redirectToMainPageRunnable = $redirectToMainPageRunnable;
        $this->alertService = $alertService;
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
        // TODO: ALERTS - fix alert printing after redirect
        $this->alertService->success(L::main_sign_in_log_in_logged_in_successfully_msg);
        $this->redirectToMainPageRunnable->run();
    }
}

