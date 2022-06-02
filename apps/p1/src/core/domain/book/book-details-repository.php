<?php

namespace p1\core\domain\book;

require_once "core/function/option.php";
require_once "core/domain/book/get-book-details-command.php";

use p1\core\function\Option;

interface BookDetailsRepository
{
    function findBookDetails(GetBookDetailsCommand $command): Option;
}