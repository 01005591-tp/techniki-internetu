<?php

namespace p1\core\domain\book;

require_once "core/function/either.php";
require_once "core/domain/book/get-book-list-command.php";

use p1\core\function\Either;

interface BookListRepository {
  function findBooks(GetBookListCommand $command): Either;
}