<?php

namespace p1\core\domain\book;

require_once "core/function/either.php";
require_once "core/function/option.php";
require_once "core/domain/book/get-book-details-command.php";
require_once "core/domain/book/edit/save-book-command.php";

use p1\core\domain\book\edit\SaveBookCommand;
use p1\core\function\Either;
use p1\core\function\Option;

interface BookDetailsRepository {
  function findBookDetails(GetBookDetailsCommand $command): Option;

  function save(SaveBookCommand $command): Either;
}