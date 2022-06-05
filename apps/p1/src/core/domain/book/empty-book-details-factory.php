<?php

namespace p1\view\books\edition;

require_once "core/domain/audit/auditable-object.php";
require_once "core/domain/book/book.php";
require_once "core/domain/book/book-details.php";
require_once "core/domain/book/book-piece.php";
require_once "core/domain/book/book-pieces.php";
require_once "core/domain/book/book-state.php";
require_once "core/domain/book/book-tag.php";
require_once "core/domain/book/publisher.php";

use p1\core\domain\audit\AuditableObject;
use p1\core\domain\book\Book;
use p1\core\domain\book\BookDetails;
use p1\core\domain\book\BookPieces;
use p1\core\domain\book\BookPieceState;
use p1\core\domain\book\BookState;
use p1\core\domain\book\Publisher;
use p1\core\domain\language\Language;
use p1\session\SessionManager;

class EmptyBookDetailsFactory {
  private SessionManager $sessionManager;
  
  public function __construct(SessionManager $sessionManager) {
    $this->sessionManager = $sessionManager;
  }

  public function create(): BookDetails {
    return new BookDetails($this->emptyBook(),
      $this->emptyPublisher(),
      [],
      $this->emptyBookPieces(),
      [],
      []
    );
  }

  private function emptyBook(): Book {
    return new Book(
      "", "", "", "", "", Language::en,
      null, null, 0, BookState::UNAVAILABLE,
      null, $this->emptyAuditableObject(),
      1
    );
  }

  private function emptyPublisher(): Publisher {
    return new Publisher(
      -1,
      ""
      , $this->emptyAuditableObject(),
      1
    );
  }

  private function emptyBookPieces(): BookPieces {
    $stateCounts = [];
    foreach (BookPieceState::cases() as $case) {
      $stateCounts[$case->name] = 0;
    }
    return new BookPieces([], $stateCounts);
  }

  private function emptyAuditableObject(): AuditableObject {
    return new AuditableObject(time(), time(), $this->sessionManager->userContext()->userEmail());
  }
}