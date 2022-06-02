<?php

namespace p1\core\database\book;

require_once "core/domain/audit/auditable-object.php";
require_once "core/domain/book/publisher.php";
require_once "core/function/option.php";

use mysqli;
use p1\core\domain\audit\AuditableObject;
use p1\core\domain\book\Publisher;
use p1\core\function\Option;

class FindPublisherByBookNameIdQuery
{
    private mysqli $connection;

    public function __construct(mysqli $connection)
    {
        $this->connection = $connection;
    }

    public function query(string $nameId): Option
    {
        $stmt = $this->connection->prepare(
            "SELECT
                P.ID AS P_ID
                ,P.NAME AS P_NAME
                ,UNIX_TIMESTAMP(P.CREATION_DATE) AS P_CREATION_DATE
                ,UNIX_TIMESTAMP(P.UPDATE_DATE) AS P_UPDATE_DATE
                ,P.UPDATED_BY AS P_UPDATED_BY
                ,P.VERSION AS P_VERSION
            FROM
                PUBLISHERS P
                JOIN BOOKS B ON
                    P.ID = B.PUBLISHER_ID
            WHERE
                B.NAME_ID = ?");
        $escapedNameId = $this->connection->real_escape_string($nameId);
        $stmt->bind_param("s", $escapedNameId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if (!$row) {
            return Option::none();
        } else {
            return Option::of(new Publisher(
                $row['P_ID'],
                $row['P_NAME'],
                new AuditableObject(
                    $row['P_CREATION_DATE'],
                    $row['P_UPDATE_DATE'],
                    $row['P_UPDATED_BY']
                ),
                $row['P_VERSION']
            ));
        }
    }
}