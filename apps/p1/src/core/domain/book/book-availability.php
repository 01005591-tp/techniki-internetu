<?php

namespace p1\core\domain\book;

require_once "core/function/option.php";

use L;
use p1\core\function\Option;

enum BookAvailability
{
    case AVAILABLE;
    case UNAVAILABLE;
    case AVAILABLE_SOON;

    public function displayName(): string
    {
        return match ($this) {
            BookAvailability::AVAILABLE => L::main_home_book_availability_display_name_available,
            BookAvailability::UNAVAILABLE => L::main_home_book_availability_display_name_unavailable,
            BookAvailability::AVAILABLE_SOON => L::main_home_book_availability_display_name_available_soon,
        };
    }

    public static function of(string $state): Option
    {
        foreach (BookAvailability::cases() as $enum) {
            if ($state === $enum->name) {
                return Option::of($enum);
            }
        }
        error_log("BookAvailability not found for: " . $state);
        return Option::none();
    }
}
