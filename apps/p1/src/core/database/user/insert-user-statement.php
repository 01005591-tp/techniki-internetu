<?php

namespace p1\core\database\user;

require_once "core/database/user/user-entity.php";
require_once "core/function/either.php";
require_once "core/domain/failure.php";

use L;
use mysqli;
use p1\core\domain\Failure;
use p1\core\function\Either;

class InsertUserStatement
{
    private mysqli $connection;

    public function __construct(mysqli $connection)
    {
        $this->connection = $connection;
    }

    public function execute(UserEntity $userEntity): Either
    {
        $stmt = $this->connection->prepare("INSERT INTO USERS(EMAIL, PASSWORD) VALUES (?, ?)");
        $email = $this->connection->real_escape_string($userEntity->email());
        $password = $this->connection->real_escape_string($userEntity->password());
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            return Either::ofRight(null);
        } else {
            return Either::ofLeft(Failure::of(L::main_errors_sign_in_request_db_insert_user_error));
        }
    }
}