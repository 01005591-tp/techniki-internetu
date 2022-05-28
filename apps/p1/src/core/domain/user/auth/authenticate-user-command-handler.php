<?php

namespace p1\core\domain\user\auth;

require_once "core/domain/failure.php";
require_once "core/domain/user/user-repository.php";
require_once "core/domain/user/auth/authenticate-user-command.php";
require_once "core/function/either.php";
require_once "core/function/function.php";

use L;
use p1\core\domain\Failure;
use p1\core\domain\user\UserRepository;
use p1\core\function\Either;
use p1\core\function\Function2;
use p1\core\function\Supplier;

class AuthenticateUserCommandHandler
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handle(AuthenticateUserCommand $command): Either
    {
        return $this->userRepository->findUserByEmail($command->email())
            ->fold(
                new UserNotFoundFailureSupplier(),
                new VerifyUserPasswordFunction($command->password())
            );
    }
}

class UserNotFoundFailureSupplier implements Supplier
{
    function supply(): Either
    {
        return Either::ofLeft(Failure::of(L::main_errors_login_request_email_or_password_invalid));
    }

}

class VerifyUserPasswordFunction implements Function2
{
    private string $password;

    public function __construct(string $password)
    {
        $this->password = $password;
    }

    function apply($value): Either
    {
        $user = $value;
        if (password_verify($this->password, $user->password())) {
            return Either::ofRight(null);
        } else {
            return Either::ofLeft(Failure::of(L::main_errors_login_request_email_or_password_invalid));
        }
    }
}