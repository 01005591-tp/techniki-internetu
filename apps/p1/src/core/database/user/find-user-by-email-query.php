<?php

namespace p1\core\database\user;

require_once "core/function/option.php";
require_once "core/database/user/user-entity.php";

use mysqli;
use p1\core\function\Option;


class FindUserByEmailQuery
{
    private mysqli $connection;

    public function __construct(mysqli $connection)
    {
        $this->connection = $connection;
    }

    public function query(string $email): Option
    {
        $stmt = $this->connection->prepare("SELECT u.ID, u.EMAIL, u.PASSWORD FROM USERS u WHERE u.EMAIL = (?)");
        $escapedEmail = $this->connection->real_escape_string($email);
        $stmt->bind_param("s", $escapedEmail);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if (!$row) {
            return Option::none();
        } else {
            return Option::of(UserEntity::existingUser($row['ID'], $row['EMAIL'], $row['PASSWORD']));
        }
    }
}