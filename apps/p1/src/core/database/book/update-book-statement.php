<?php

namespace p1\core\database\book;

require_once "core/function/either.php";

use L;
use mysqli;
use p1\core\domain\book\edit\SaveBookCommand;
use p1\core\function\Either;
use RuntimeException;

class UpdateBookStatement {
  private mysqli $connection;

  public function __construct(mysqli $connection) {
    $this->connection = $connection;
  }

  public function execute(SaveBookCommand $command): Either {
    $stmt = $this->connection->prepare("UPDATE BOOKS SET 
                 ISBN = ?,
                 TITLE = ?,
                 DESCRIPTION = ?,
                 LANGUAGE = ?,
                 PUBLISHED_AT = STR_TO_DATE(?, '%Y-%m-%d'),
                 /*PUBLISHER_ID unsupported yet*/
                 PAGES = ?,
                 STATE = ?,
                 IMAGE_URI = ?,
                 UPDATE_DATE = NOW(),
                 UPDATED_BY = ?,
                 VERSION = VERSION + 1,
                 NAME_ID = ?
                 WHERE ID = ?
                 AND VERSION <= ?");
    $isbn = $this->connection->real_escape_string($command->isbn());
    $title = $this->connection->real_escape_string($command->title());
    $description = $this->connection->real_escape_string($command->description());
    $language = $this->connection->real_escape_string(empty($command->language())
      ? null
      : $command->language()->name
    );
    $publishedAt = $this->connection->real_escape_string(
      empty($command->publishedAt())
        ? null
        : $command->publishedAt()->format('Y-m-d')
    );
    // $publisherId = $this->connection->real_escape_string(null);  /* unsupported yet */
    $pages = $this->connection->real_escape_string($command->pageCount());
    $state = $this->connection->real_escape_string($command->state()->name);
    $imageUri = $this->connection->real_escape_string($command->imageUri());
    $updatedBy = $this->connection->real_escape_string(
      empty($command->updatedBy())
        ? 'unknown'
        : $command->updatedBy()
    );
    $nameId = $this->connection->real_escape_string($command->nameId());
    $id = $this->connection->real_escape_string($command->id());
    $version = $this->connection->real_escape_string($command->version());
    $stmt->bind_param("sssssissssii",
      $isbn,
      $title,
      $description,
      $language,
      $publishedAt,
      $pages,
      $state,
      $imageUri,
      $updatedBy,
      $nameId,
      $id,
      $version
    );
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
      return Either::ofRight(null);
    } else {
      throw new RuntimeException(L::main_errors_global_global_error_message);
    }
  }
}