<?php

namespace p1\view\select;

use Exception;

class MultiSelectIdNormalizer {
  private const INVALID_CHARS_REGEX = "/([\'\"\,\<\.\>\/\?\\\\\[\]\(\)\+\=\|\!\@\#\$\%\^\&\*])/";

  private function __construct() {
    throw new Exception("Utility class cannot be instantiated.");
  }

  public static function normalize(string $rawId): string {
    return preg_replace(self::INVALID_CHARS_REGEX, "_", $rawId);
  }

}