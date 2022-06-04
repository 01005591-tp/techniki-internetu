<?php

namespace p1\core\domain\user\auth;

require_once "core/domain/failure.php";
require_once "core/domain/user/user-repository.php";
require_once "core/domain/user/auth/authenticate-user-command.php";
require_once "core/domain/user/auth/authenticate-user-result.php";
require_once "core/domain/user/auth/roles.php";
require_once "core/domain/user/auth/user-auth-repository.php";
require_once "core/function/either.php";
require_once "core/function/function.php";

use L;
use p1\core\domain\Failure;
use p1\core\domain\user\UserRepository;
use p1\core\function\Either;
use p1\core\function\Function2;
use p1\core\function\Supplier;

class AuthenticateUserCommandHandler {
  private UserRepository $userRepository;
  private UserAuthRepository $userAuthRepository;

  public function __construct(UserRepository     $userRepository,
                              UserAuthRepository $userAuthRepository) {
    $this->userRepository = $userRepository;
    $this->userAuthRepository = $userAuthRepository;
  }

  public function handle(AuthenticateUserCommand $command): Either {
    return $this->userRepository->findUserByEmail($command->email())
      ->fold(
        new UserNotFoundFailureSupplier(),
        new VerifyUserPasswordFunction($command->password())
      )
      ->mapRight(new CreateAuthenticateUserResultFunction($this->userAuthRepository));
  }
}

class UserNotFoundFailureSupplier implements Supplier {
  function supply(): Either {
    return Either::ofLeft(Failure::of(L::main_errors_login_request_email_or_password_invalid));
  }

}

class VerifyUserPasswordFunction implements Function2 {
  private string $password;

  public function __construct(string $password) {
    $this->password = $password;
  }

  function apply($value): Either {
    $user = $value;
    if (password_verify($this->password, $user->password())) {
      return Either::ofRight($user);
    } else {
      return Either::ofLeft(Failure::of(L::main_errors_login_request_email_or_password_invalid));
    }
  }
}

class CreateAuthenticateUserResultFunction implements Function2 {
  private UserAuthRepository $userAuthRepository;

  public function __construct(UserAuthRepository $userAuthRepository) {
    $this->userAuthRepository = $userAuthRepository;
  }

  function apply($value): AuthenticateUserResult {
    $user = $value;
    $userRoles = $this->userAuthRepository->findUserRoles($user->id());
    return new AuthenticateUserResult(
      $user->id(),
      $user->email(),
      $this->fillInUserRoleIfMissing($userRoles)
    );
  }

  private function fillInUserRoleIfMissing(array $userRoles): array {
    // every authenticated user has a USER role
    // no need to use storage and populate DB with this information
    if (!in_array(Roles::USER->name, $userRoles)) {
      $userRoles[] = Roles::USER->name;
    }
    return $userRoles;
  }
}