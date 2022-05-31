<?php

namespace p1\view\home;

require_once "core/domain/book/get-book-list-command-handler.php";
require_once "session/session-manager.php";

use p1\core\domain\book\GetBookListCommandHandler;
use p1\view\session\SessionManager;

class HomeConfiguration
{
    private HomeController $homeController;

    public function __construct(SessionManager            $sessionManager,
                                GetBookListCommandHandler $getBookListCommandHandler)
    {
        $this->homeController = new HomeController($getBookListCommandHandler, $sessionManager);
    }

    public function homeController(): HomeController
    {
        return $this->homeController;
    }
}