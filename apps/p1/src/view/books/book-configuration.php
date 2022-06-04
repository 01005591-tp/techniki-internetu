<?php

namespace p1\view\book;

require_once "core/domain/book/get-book-details-command-handler.php";

require_once "session/session-manager.php";

require_once "view/redirect-manager.php";

use p1\core\domain\book\GetBookDetailsCommandHandler;
use p1\session\SessionManager;
use p1\view\RedirectManager;

class BookConfiguration
{
    private BookController $bookController;

    public function __construct(SessionManager               $sessionManager,
                                RedirectManager              $redirectManager,
                                GetBookDetailsCommandHandler $getBookDetailsCommandHandler)
    {
        $this->bookController = new BookController(
            $getBookDetailsCommandHandler,
            $sessionManager,
            $redirectManager
        );
    }

    public function bookController(): BookController
    {
        return $this->bookController;
    }
}