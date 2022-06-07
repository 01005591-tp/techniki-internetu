<?php

namespace p1\view\book;

require_once "core/domain/book/get-book-details-command-handler.php";
require_once "core/domain/book/empty-book-details-factory.php";
require_once "core/domain/book/edit/save-book-command-handler.php";

require_once "session/session-manager.php";

require_once "view/redirect-manager.php";
require_once "view/books/book-controller.php";
require_once "view/books/book-details-service.php";
require_once "view/books/edition/book-edit-controller.php";
require_once "view/books/edition/save-book-details-merge-mapper.php";
require_once "view/books/edition/save-book-details-request-to-command-mapper.php";
require_once "view/books/edition/tags-parser.php";

require_once "view/security/html-sanitizer.php";

use p1\core\domain\book\edit\SaveBookCommandHandler;
use p1\core\domain\book\GetBookDetailsCommandHandler;
use p1\session\SessionManager;
use p1\view\alerts\AlertService;
use p1\view\books\BookDetailsService;
use p1\view\books\edition\BookEditController;
use p1\view\books\edition\EmptyBookDetailsFactory;
use p1\view\books\edition\SaveBookDetailsMergeMapper;
use p1\view\books\edition\SaveBookDetailsRequestToCommandMapper;
use p1\view\books\edition\SaveBookDetailsService;
use p1\view\books\edition\TagsParser;
use p1\view\RedirectManager;
use p1\view\security\HtmlSanitizer;

class BookConfiguration {
  private BookController $bookController;
  private BookEditController $bookEditController;

  public function __construct(SessionManager               $sessionManager,
                              RedirectManager              $redirectManager,
                              GetBookDetailsCommandHandler $getBookDetailsCommandHandler,
                              SaveBookCommandHandler       $saveBookCommandHandler,
                              HtmlSanitizer                $htmlSanitizer,
                              AlertService                 $alertService) {
    $bookDetailsService = new BookDetailsService($getBookDetailsCommandHandler, $redirectManager);
    $tagsParser = new TagsParser();
    $saveBookDetailsRequestToCommandMapper = new SaveBookDetailsRequestToCommandMapper($htmlSanitizer, $tagsParser);
    $saveBookDetailsMergeMapper = new SaveBookDetailsMergeMapper($tagsParser);
    $saveBookDetailsService = new SaveBookDetailsService(
      $saveBookCommandHandler,
      $saveBookDetailsRequestToCommandMapper,
      $sessionManager,
      $bookDetailsService,
      $redirectManager,
      $alertService,
      $saveBookDetailsMergeMapper
    );
    $this->bookController = new BookController(
      $bookDetailsService,
      $sessionManager,
      $redirectManager
    );
    $this->bookEditController = new BookEditController(
      $sessionManager,
      $bookDetailsService,
      new EmptyBookDetailsFactory($sessionManager),
      $saveBookDetailsService
    );
  }

  public function bookController(): BookController {
    return $this->bookController;
  }

  public function bookEditController(): BookEditController {
    return $this->bookEditController;
  }
}