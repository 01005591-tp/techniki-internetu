<?php

namespace p1\view\book;

require_once "core/function/either.php";
require_once "core/function/function.php";
require_once "core/function/option.php";

require_once "core/domain/book/book-details.php";
require_once "core/domain/book/get-book-details-command.php";
require_once "core/domain/book/get-book-details-command-handler.php";
require_once "core/domain/failure.php";

require_once "session/session-manager.php";

require_once "view/redirect-manager.php";

require_once "view/books/book-details-service.php";

use L;
use p1\core\domain\book\BookDetails;
use p1\core\domain\Failure;
use p1\core\function\Either;
use p1\core\function\FunctionIdentity;
use p1\core\function\FunctionUtils;
use p1\session\SessionConstants;
use p1\session\SessionManager;
use p1\view\books\BookDetailsService;
use p1\view\RedirectManager;

class BookController {
  private SessionManager $sessionManager;
  private BookDetailsService $bookDetailsService;
  private RedirectManager $redirectManager;

  public function __construct(BookDetailsService $bookDetailsService,
                              SessionManager     $sessionManager,
                              RedirectManager    $redirectManager) {
    $this->sessionManager = $sessionManager;
    $this->redirectManager = $redirectManager;
    $this->bookDetailsService = $bookDetailsService;
  }

  public function getBookDetails(): BookDetails {
    return $this->resolveBookNameIdPathParam()
      ->mapRight($this->bookDetailsService->getBookDetailsRequiredFunction())
      ->fold(
        FunctionUtils::runnableToFunction2($this->redirectManager->redirectTo404NotFoundPage()),
        FunctionIdentity::instance()
      );
  }

  private function resolveBookNameIdPathParam(): Either {
    return $this->sessionManager->get(SessionConstants::BOOK_DETAILS_BOOK_NAME_ID)
      ->fold(
        Either::leftSupplier(Failure::of(L::main_errors_global_global_error_message)),
        Either::wrapToRightFunction()
      );
  }
}