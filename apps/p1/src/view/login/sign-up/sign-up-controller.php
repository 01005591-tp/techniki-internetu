<?php

namespace p1\view\login\signup;

require_once "core/domain/user/create-user-command-handler.php";
require_once "core/domain/user/create-user-command.php";
require_once "core/function/function.php";
require_once "session/session-manager.php";
require_once "state.php";
require_once "view/alerts/alert-service.php";
require_once "view/login/sign-up/sign-up-request.php";
require_once "view/login/sign-up/sign-up-request-validator.php";
require_once "view/redirect-manager.php";

use p1\core\domain\user\CreateUserCommand;
use p1\core\domain\user\CreateUserCommandHandler;
use p1\core\function\Consumer;
use p1\core\function\Either;
use p1\core\function\Function2;
use p1\session\SessionManager;
use p1\state\State;
use p1\view\alerts\AlertService;
use p1\view\RedirectManager;

class SignUpController {
  private SignUpRequestValidator $signUpRequestValidator;
  private CreateUserCommandHandler $createUserCommandHandler;
  private State $state;
  private AlertService $alertService;
  private SessionManager $sessionManager;
  private RedirectManager $redirectManager;

  public function __construct(SignUpRequestValidator   $signUpRequestValidator,
                              CreateUserCommandHandler $createUserCommandHandler,
                              State                    $state,
                              AlertService             $alertService,
                              SessionManager           $sessionManager,
                              RedirectManager          $redirectManager) {
    $this->signUpRequestValidator = $signUpRequestValidator;
    $this->createUserCommandHandler = $createUserCommandHandler;
    $this->state = $state;
    $this->alertService = $alertService;
    $this->sessionManager = $sessionManager;
    $this->redirectManager = $redirectManager;
  }

  public function signIn(): void {
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
        ->peekLeft(new CreateUserCommandFailedConsumer($this->alertService))
        ->peekRight(new CreateUserCommandSuccessConsumer($this->state))
        ->peekRight(new RedirectToHomePageConsumer());
    } else if (!is_null($this->sessionManager->userContext())) {
      $this->redirectManager->redirectToMainPage()->run();
    } else {
      $this->state->remove(State::SIGN_UP_FORM_PROVIDED_EMAIL);
    }
  }

  public function currentEmailAddress(): string {
    $currentEmail = $this->state->get(State::SIGN_UP_FORM_PROVIDED_EMAIL);
    return (isset($currentEmail)) ? $currentEmail : '';
  }
}

class CreateCreateUserCommand implements Function2 {
  function apply($value): CreateUserCommand {
    $request = $value;
    return new CreateUserCommand($request->email(), $request->password());
  }
}

class HandleCreateUserCommand implements Function2 {
  private CreateUserCommandHandler $createUserCommandHandler;

  public function __construct(CreateUserCommandHandler $createUserCommandHandler) {
    $this->createUserCommandHandler = $createUserCommandHandler;
  }


  function apply($value): Either {
    $command = $value;
    return $this->createUserCommandHandler->handle($command);
  }
}

class CreateUserCommandFailedConsumer implements Consumer {
  private AlertService $alertService;

  public function __construct(AlertService $alertService) {
    $this->alertService = $alertService;
  }

  function consume($value): void {
    $failure = $value;
    $this->alertService->error($failure->message());
  }
}

class CreateUserCommandSuccessConsumer implements Consumer {
  private State $state;

  public function __construct(State $state) {
    $this->state = $state;
  }

  function consume($value): void {
    $this->state->remove(State::SIGN_UP_FORM_PROVIDED_EMAIL);
  }
}

class RedirectToHomePageConsumer implements Consumer {
  function consume($value): void {
    if (headers_sent()) {
      echo('<script type="text/javascript">window.location\'/\';</script>');
    } else {
      header("Location: /");
    }
    exit();
  }
}