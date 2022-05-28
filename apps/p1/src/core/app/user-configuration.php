<?php

namespace p1\core\app;

require_once "core/database/database-configuration.php";
require_once "core/database/database-connection.php";
require_once "core/database/user/find-user-by-email-query.php";
require_once "core/database/user/insert-user-statement.php";
require_once "core/database/user/user-db-repository.php";
require_once "core/domain/user/create-user-command-handler.php";
require_once "core/domain/user/user-repository.php";

use p1\core\database\DatabaseConfiguration;
use p1\core\database\DatabaseConnection;
use p1\core\database\user\FindUserByEmailQuery;
use p1\core\database\user\InsertUserStatement;
use p1\core\database\user\UserDbRepository;
use p1\core\domain\user\auth\AuthenticateUserCommandHandler;
use p1\core\domain\user\CreateUserCommandHandler;
use p1\core\domain\user\UserRepository;

class UserConfiguration
{
    private DatabaseConnection $databaseConnection;
    private FindUserByEmailQuery $findUserByEmailQuery;
    private InsertUserStatement $insertUserStatement;
    private UserRepository $userRepository;
    private CreateUserCommandHandler $createUserCommandHandler;
    private AuthenticateUserCommandHandler $authenticateUserCommandHandler;

    public function __construct(DatabaseConfiguration $databaseConfiguration)
    {
        $this->databaseConnection = $databaseConfiguration->databaseConnection();
        $this->findUserByEmailQuery = new FindUserByEmailQuery($this->databaseConnection->connection());
        $this->insertUserStatement = new InsertUserStatement($this->databaseConnection->connection());
        $this->userRepository = new UserDbRepository(
            $this->findUserByEmailQuery,
            $this->insertUserStatement
        );
        $this->createUserCommandHandler = new CreateUserCommandHandler($this->userRepository);
        $this->authenticateUserCommandHandler = new AuthenticateUserCommandHandler($this->userRepository);
    }

    public function createUserCommandHandler(): CreateUserCommandHandler
    {
        return $this->createUserCommandHandler;
    }

    public function authenticateUserCommandHandler(): AuthenticateUserCommandHandler
    {
        return $this->authenticateUserCommandHandler;
    }
}