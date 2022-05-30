<?php

namespace p1\core\database\user;

require_once "core/function/either.php";
require_once "core/function/function.php";
require_once "core/function/option.php";
require_once "core/domain/user/user-repository.php";
require_once "core/domain/user/create-user-command.php";
require_once "core/database/user/find-user-by-email-query.php";
require_once "core/database/user/user-entity.php";

use L;
use p1\core\domain\Failure;
use p1\core\domain\user\CreateUserCommand;
use p1\core\domain\user\UserRepository;
use p1\core\function\Either;
use p1\core\function\Function2;
use p1\core\function\Option;
use p1\core\function\Supplier;

class UserDbRepository implements UserRepository
{
    private FindUserByEmailQuery $findUserByEmailQuery;
    private InsertUserStatement $insertUserStatement;

    public function __construct(FindUserByEmailQuery $findUserByEmailQuery,
                                InsertUserStatement  $insertUserStatement)
    {
        $this->findUserByEmailQuery = $findUserByEmailQuery;
        $this->insertUserStatement = $insertUserStatement;
    }

    public function createUser(CreateUserCommand $command): Either
    {
        return $this->findUserByEmailQuery->query($command->email())
            ->fold(
                new CreateUserSupplier($this->insertUserStatement, $command),
                new CreateUserAlreadyExistsFunction()
            );
    }

    public function findUserByEmail(string $email): Option
    {
        return $this->findUserByEmailQuery->query($email);
    }
}

class CreateUserSupplier implements Supplier
{
    private InsertUserStatement $insertUserStatement;
    private CreateUserCommand $command;

    public function __construct(InsertUserStatement $insertUserStatement,
                                CreateUserCommand   $command)
    {
        $this->insertUserStatement = $insertUserStatement;
        $this->command = $command;
    }

    function supply(): Either
    {
        return $this->insertUserStatement->execute(UserEntity::newUser(
            $this->command->email(),
            $this->command->password()
        ));
    }
}

class CreateUserAlreadyExistsFunction implements Function2
{
    function apply($value): Either
    {
        $user = $value;
        error_log('User with email "' . $user->email() . '" already exists');
        return Either::ofLeft(Failure::of(L::main_errors_sign_in_request_user_with_this_email_already_exists));
    }
}