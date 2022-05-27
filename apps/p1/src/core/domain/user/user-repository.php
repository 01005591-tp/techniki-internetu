<?php

namespace p1\core\domain\user;

require_once "core/function/either.php";
require_once "core/domain/user/create-user-command.php";

use p1\core\function\Either;

interface UserRepository
{
    public function createUser(CreateUserCommand $command): Either;
}