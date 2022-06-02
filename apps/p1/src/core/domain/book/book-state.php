<?php

namespace p1\core\domain\book;

require_once "core/function/option.php";

use L;
use p1\core\function\Option;

enum BookState
{
    case AVAILABLE;
    case UNAVAILABLE;
    case AVAILABLE_SOON;

    public function displayName(): string
    {
        return match ($this) {
            BookState::AVAILABLE => L::main_home_book_availability_display_name_available,
            BookState::UNAVAILABLE => L::main_home_book_availability_display_name_unavailable,
            BookState::AVAILABLE_SOON => L::main_home_book_availability_display_name_available_soon,
        };
    }

    public static function of(string $state): Option
    {
        foreach (BookState::cases() as $enum) {
            if ($state === $enum->name) {
                return Option::of($enum);
            }
        }
        error_log("BookState not found for: " . $state);
        return Option::none();
    }

    public static function ofOrUnavailable(string $state): BookState
    {
        return BookState::of($state)->orElse(BookState::UNAVAILABLE);
    }
}
