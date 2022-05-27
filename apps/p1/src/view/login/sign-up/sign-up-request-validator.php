<?php

namespace p1\view\login\signup;

require_once "core/domain/failure.php";
require_once "core/function/option.php";
require_once "core/function/either.php";

use L;
use p1\core\domain\Failure;
use p1\core\function\Either;
use p1\core\function\Function2;

class SignUpRequestValidator
{
    public function validate(SignUpRequest $request): Either
    {
        return $this->verifyEmailAddress($request)
            ->flatMapRight(new VerifyPasswordSizeFunction())
            ->flatMapRight(new VerifyPasswordCharactersFunction())
            ->flatMapRight(new VerifyPasswordsMatchFunction());
    }

    protected function verifyEmailAddress(SignUpRequest $request): Either
    {
        $email = $request->email();
        if (!isset($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return Either::ofLeft(Failure::of(L::main_errors_sign_in_request_invalid_email));
        }
        return Either::ofRight($request);
    }
}

class VerifyPasswordSizeFunction implements Function2
{
    function apply($value): Either
    {
        $request = $value;
        $password = $request->password();
        if (!isset($password) || strlen($password) < 6) {
            return Either::ofLeft(Failure::of(L::main_errors_sign_in_request_password_too_short));
        }
        return Either::ofRight($request);
    }
}

class VerifyPasswordCharactersFunction implements Function2
{
    private const PASSWORD_REGEX = '/^(?=.*[a-z]+)(?=.*[A-Z])(?=.*[0-9])(?=.*[,\.<>?!@#$%^&*()\-_=+\[\]\{\};\'\\:"|`~€§])[a-zA-Z0-9,\.<>?!@#$%^&*()\-_=+\[\]\{\};\'\\:"|`~€§]+$/';

    function apply($value): Either
    {
        $request = $value;
        $password = $request->password();
        if (!preg_match(self::PASSWORD_REGEX, $password)) {
            return Either::ofLeft(Failure::of(L::main_errors_sign_in_request_password_invalid_chars));
        }
        return Either::ofRight($request);
    }
}

class VerifyPasswordsMatchFunction implements Function2
{
    function apply($value): Either
    {
        $request = $value;
        if ($request->password() != $request->passwordRepeat()) {
            return Either::ofLeft(Failure::of(L::main_errors_sign_in_request_passwords_dont_match));
        }
        return Either::ofRight($request);
    }
}