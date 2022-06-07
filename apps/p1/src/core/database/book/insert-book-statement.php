<?php

namespace p1\core\database\book;

require_once "core/function/either.php";

use L;
use mysqli;
use p1\core\domain\book\edit\SaveBookCommand;
use p1\core\function\Either;
use RuntimeException;

class InsertBookStatement {
  private mysqli $connection;

  public function __construct(mysqli $connection) {
    $this->connection = $connection;
  }

  public function execute(SaveBookCommand $command): Either {
    $stmt = $this->connection->prepare("INSERT INTO BOOKS (
                   ISBN,
                   TITLE,
                   DESCRIPTION,
                   LANGUAGE,
                   PUBLISHED_AT,
                   PUBLISHER_ID,
                   PAGES,
                   STATE,
                   IMAGE_URI,
                   UPDATED_BY,
                   NAME_ID
                   ) VALUES (
                          ?, /* ISBN */
                          ?, /* TITLE */
                          ?, /* DESCRIPTION */
                          ?, /* LANGUAGE */
                          STR_TO_DATE(?, '%Y-%m-%d'), /* PUBLISHED_AT */
                          ?, /* PUBLISHER_ID */
                          ?, /* PAGES */
                          ?, /* STATE */
                          ?, /* IMAGE_URI */
                          ?, /* UPDATED_BY */
                          ?  /* NAME_ID */)");
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
    $publisherId = $this->connection->real_escape_string(null);
    $pages = $this->connection->real_escape_string($command->pageCount());
    $state = $this->connection->real_escape_string($command->state()->name);
    $imageUri = $this->connection->real_escape_string($command->imageUri());
    $updatedBy = $this->connection->real_escape_string(
      empty($command->updatedBy())
        ? 'unknown'
        : $command->updatedBy()
    );
    $nameId = $this->connection->real_escape_string($command->nameId());
    $stmt->bind_param("sssssiissss",
      $isbn,
      $title,
      $description,
      $language,
      $publishedAt,
      $publisherId,
      $pages,
      $state,
      $imageUri,
      $updatedBy,
      $nameId
    );
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
      return Either::ofRight(null);
    } else {
      throw new RuntimeException(L::main_errors_global_global_error_message);
    }
  }
}