<?php

namespace p1\view\books\edition;

require_once "core/domain/book/book-details.php";
require_once "core/domain/book/empty-book-details-factory.php";
require_once "core/domain/book/get-book-details-command.php";
require_once "core/domain/book/get-book-details-command-handler.php";
require_once "core/domain/failure.php";

require_once "core/function/either.php";
require_once "core/function/function.php";

require_once "session/session-manager.php";

require_once "view/books/book-details-service.php";

use p1\core\domain\book\BookDetails;
use p1\core\function\Function2;
use p1\core\function\Supplier;
use p1\session\SessionConstants;
use p1\session\SessionManager;
use p1\view\books\BookDetailsService;

class BookEditController {
  private SessionManager $sessionManager;
  private EmptyBookDetailsFactory $emptyBookDetailsFactory;
  private BookDetailsService $bookDetailsService;

  private BookEditParams $editParams;
  private BookDetails $bookDetails;

  public function __construct(SessionManager          $sessionManager,
                              BookDetailsService      $bookDetailsService,
                              EmptyBookDetailsFactory $emptyBookDetailsFactory) {
    $this->sessionManager = $sessionManager;
    $this->bookDetailsService = $bookDetailsService;
    $this->emptyBookDetailsFactory = $emptyBookDetailsFactory;
    $this->editParams = new BookEditParams(BookEditPageMode::CREATE);
  }

  public function loadBookDetails(): BookDetails {
    if (!empty($this->bookDetails)) {
      return $this->bookDetails;
    }
    $this->editParams = $this->resolveEditParams();
    if ($this->editParams->editMode() === BookEditPageMode::CREATE) {
      $this->bookDetails = $this->emptyBookDetailsFactory->create();
    } else {
      $this->bookDetails = $this->bookDetailsService->getBookDetailsRequiredFunction()
        ->apply($this->editParams->nameId());
    }
    return $this->bookDetails;
  }

  private function resolveEditParams(): BookEditParams {
    return $this->sessionManager->get(SessionConstants::BOOK_DETAILS_BOOK_NAME_ID)
      ->map(new class implements Function2 {
        function apply($value): BookEditParams {
          return new BookEditParams(BookEditPageMode::EDIT, $value);
        }
      })
      ->orElseGet(new class implements Supplier {
        function supply(): BookEditParams {
          return new BookEditParams(BookEditPageMode::CREATE);
        }
      });
  }
}

enum BookEditPageMode {
  case CREATE;
  case EDIT;
}

class BookEditParams {
  private BookEditPageMode $editMode;
  private ?string $nameId;

  public function __construct(BookEditPageMode $editMode,
                              ?string          $nameId = null) {
    $this->editMode = $editMode;
    $this->nameId = $nameId;
  }

  public function editMode(): BookEditPageMode {
    return $this->editMode;
  }

  public function nameId(): ?string {
    return $this->nameId;
  }
}