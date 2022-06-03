<?php

namespace p1\view\home;

require_once "core/domain/book/get-book-list-command-handler.php";

require_once "core/domain/book/tag/get-all-book-tags-use-case.php";

require_once "session/session-manager.php";

require_once "view/pagination/pagination-service.php";

use p1\core\domain\book\GetBookListCommandHandler;
use p1\core\domain\book\tag\GetAllBookTagsUseCase;
use p1\view\session\SessionManager;

class HomeConfiguration
{
    private HomeController $homeController;

    public function __construct(SessionManager            $sessionManager,
                                GetBookListCommandHandler $getBookListCommandHandler,
                                GetAllBookTagsUseCase     $getAllBookTagsUseCase,
                                PaginationService         $paginationService)
    {
        $this->homeController = new HomeController(
            $getBookListCommandHandler,
            $getAllBookTagsUseCase,
            $sessionManager,
            $paginationService
        );
    }

    public function homeController(): HomeController
    {
        return $this->homeController;
    }
}