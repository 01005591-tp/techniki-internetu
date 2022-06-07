<?php

namespace p1\core\domain\language;

require_once "core/function/option.php";

use L;
use p1\core\function\FunctionUtils;
use p1\core\function\Option;

enum Language {
  case en;
  case pl;
  case unknown;

  public function displayName(): string {
    return match ($this) {
      self::en => L::main_languages_en,
      self::pl => L::main_languages_pl,
      self::unknown => L::main_languages_unknown,
    };
  }

  public static function of(?string $lang): Option {
    foreach (Language::cases() as $enum) {
      if ($lang === $enum->name) {
        return Option::of($enum);
      }
    }
    return Option::none();
  }

  public static function ofOrUnknown(string $lang): Language {
    return self::of($lang)
      ->onEmpty(FunctionUtils::runnableOfClosure(function () use ($lang) {
        error_log("Language not found for: " . $lang);
      }))
      ->orElse(Language::unknown);
  }
}