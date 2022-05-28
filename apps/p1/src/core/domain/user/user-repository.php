<?php

namespace p1\core\domain\user;

require_once "core/function/either.php";
require_once "core/function/option.php";
require_once "core/domain/user/create-user-command.php";

use p1\core\function\Either;
use p1\core\function\Option;

interface UserRepository
{
    public function createUser(CreateUserCommand $command): Either;

    public function findUserByEmail(string $email): Option;
}