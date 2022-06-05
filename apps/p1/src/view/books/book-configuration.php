<?php

namespace p1\view\book;

require_once "core/domain/book/get-book-details-command-handler.php";
require_once "core/domain/book/empty-book-details-factory.php";

require_once "session/session-manager.php";

require_once "view/redirect-manager.php";

require_once "view/books/book-controller.php";
require_once "view/books/book-details-service.php";
require_once "view/books/edition/book-edit-controller.php";

use p1\core\domain\book\GetBookDetailsCommandHandler;
use p1\session\SessionManager;
use p1\view\books\BookDetailsService;
use p1\view\books\edition\BookEditController;
use p1\view\books\edition\EmptyBookDetailsFactory;
use p1\view\RedirectManager;

class BookConfiguration {
  private BookController $bookController;
  private BookEditController $bookEditController;

  public function __construct(SessionManager               $sessionManager,
                              RedirectManager              $redirectManager,
                              GetBookDetailsCommandHandler $getBookDetailsCommandHandler) {
    $bookDetailsService = new BookDetailsService($getBookDetailsCommandHandler, $redirectManager);
    $this->bookController = new BookController(
      $bookDetailsService,
      $sessionManager,
      $redirectManager
    );
    $this->bookEditController = new BookEditController(
      $sessionManager,
      $bookDetailsService,
      new EmptyBookDetailsFactory($sessionManager)
    );
  }

  public function bookController(): BookController {
    return $this->bookController;
  }

  public function bookEditController(): BookEditController {
    return $this->bookEditController;
  }
}