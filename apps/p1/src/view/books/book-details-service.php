<?php

namespace p1\view\books;

require_once "core/domain/book/book-details.php";
require_once "core/domain/book/get-book-details-command.php";
require_once "core/domain/book/get-book-details-command-handler.php";
require_once "core/domain/failure.php";

require_once "core/function/either.php";
require_once "core/function/function.php";

require_once "view/redirect-manager.php";

use L;
use p1\core\domain\book\BookDetails;
use p1\core\domain\book\GetBookDetailsCommand;
use p1\core\domain\book\GetBookDetailsCommandHandler;
use p1\core\domain\Failure;
use p1\core\function\Either;
use p1\core\function\Function2;
use p1\core\function\FunctionIdentity;
use p1\core\function\FunctionUtils;
use p1\view\RedirectManager;

class BookDetailsService {
  private GetBookDetailsRequiredFunction $getBookDetailsRequiredFunction;

  public function __construct(GetBookDetailsCommandHandler $getBookDetailsCommandHandler,
                              RedirectManager              $redirectManager) {
    $this->getBookDetailsRequiredFunction = new GetBookDetailsRequiredFunction(
      $getBookDetailsCommandHandler,
      $redirectManager
    );
  }

  public function getBookDetailsRequiredFunction(): Function2 {
    return $this->getBookDetailsRequiredFunction;
  }
}

class GetBookDetailsRequiredFunction implements Function2 {
  private GetBookDetailsCommandHandler $getBookDetailsCommandHandler;
  private RedirectManager $redirectManager;

  public function __construct(GetBookDetailsCommandHandler $getBookDetailsCommandHandler,
                              RedirectManager              $redirectManager) {
    $this->getBookDetailsCommandHandler = $getBookDetailsCommandHandler;
    $this->redirectManager = $redirectManager;
  }

  function apply($value): BookDetails {
    $bookNameId = $value;
    return $this->getBookDetailsCommandHandler->handle(new GetBookDetailsCommand($bookNameId))
      ->fold(
        Either::leftSupplier(Failure::of(L::main_errors_global_global_error_message)),
        Either::wrapToRightFunction()
      )
      ->fold(
        FunctionUtils::runnableToFunction2($this->redirectManager->redirectTo404NotFoundPage()),
        FunctionIdentity::instance()
      );
  }
}