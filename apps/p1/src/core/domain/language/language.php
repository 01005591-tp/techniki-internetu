<?php

namespace p1\core\domain\language;

use L;

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

  public static function ofOrUnknown(string $lang): Language {
    foreach (Language::cases() as $enum) {
      if ($lang === $enum->name) {
        return $enum;
      }
    }
    error_log("Language not found for: " . $lang);
    return Language::unknown;
  }
}