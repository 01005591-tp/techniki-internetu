<?php

namespace p1\core\domain\book;

require_once "core/domain/audit/auditable-object.php";
require_once "core/function/option.php";

use L;
use p1\core\domain\audit\AuditableObject;
use p1\core\function\Option;

class BookPiece {
  private int $id;
  private int $bookId;
  private BookPieceState $state;
  private ?AuditableObject $auditableObject;
  private ?int $version;

  public function __construct(int              $id,
                              int              $bookId,
                              BookPieceState   $state,
                              ?AuditableObject $auditableObject,
                              ?int             $version) {
    $this->id = $id;
    $this->bookId = $bookId;
    $this->state = $state;
    $this->auditableObject = $auditableObject;
    $this->version = $version;
  }

  public function id(): int {
    return $this->id;
  }

  public function bookId(): int {
    return $this->bookId;
  }

  public function state(): BookPieceState {
    return $this->state;
  }

  public function auditableObject(): ?AuditableObject {
    return $this->auditableObject;
  }

  public function version(): ?int {
    return $this->version;
  }
}

enum BookPieceState {
  case AVAILABLE;
  case UNAVAILABLE;
  case BOOKED;
  case RENTED;

  public function displayName(): string {
    return match ($this) {
      BookPieceState::AVAILABLE => L::main_books_book_piece_state_display_name_available,
      BookPieceState::UNAVAILABLE => L::main_books_book_piece_state_display_name_unavailable,
      BookPieceState::BOOKED => L::main_books_book_piece_state_display_name_booked,
      BookPieceState::RENTED => L::main_books_book_piece_state_display_name_rented,
    };
  }

  public static function of(string $state): Option {
    foreach (BookPieceState::cases() as $enum) {
      if ($state === $enum->name) {
        return Option::of($enum);
      }
    }
    error_log("BookPieceState not found for: " . $state);
    return Option::none();
  }
}