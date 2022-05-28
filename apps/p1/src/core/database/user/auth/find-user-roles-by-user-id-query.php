<?php

namespace p1\core\database\user\auth;

use mysqli;

class FindUserRolesByUserIdQuery
{
    private mysqli $connection;

    public function __construct(mysqli $connection)
    {
        $this->connection = $connection;
    }

    public function query(string $userId): array
    {
        $stmt = $this->connection->prepare("SELECT ur.ROLE_NAME FROM USER_ROLES ur WHERE ur.USER_ID = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $userRoles = array();
        while ($row = $result->fetch_assoc()) {
            $userRoles[] = $row['ROLE_NAME'];
        }
        return $userRoles;
    }
}