<?php

namespace p1\view\books\edition;

require_once "core/function/either.php";
require_once "core/domain/failure.php";

use L;
use p1\core\domain\Failure;
use p1\core\function\Either;

class TagsParser {
  private const TAGS_REGEX = "/^[a-zA-Z0-9][a-zA-Z0-9-,]*(?<![,-])$/";

  public function parse(?string $tags): Either {
    if (empty($tags)) {
      return Either::ofRight([]);
    } else if (preg_match(self::TAGS_REGEX, $tags)) {
      return Either::ofRight(explode(",", $tags));
    } else {
      return Either::ofLeft(Failure::of(L::main_errors_save_book_request_invalid_tags_format_msg));
    }
  }
}