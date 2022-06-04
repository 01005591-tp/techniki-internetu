<?php

namespace p1\configuration;

require_once "state.php";
require_once "i18n/i18n-configuration.php";
require_once "core/database/database-configuration.php";
require_once "core/app/user-configuration.php";
require_once "core/app/book-configuration.php";
require_once "session/session-manager.php";
require_once "view/redirect-manager.php";
require_once "view/view-configuration.php";
require_once "view/navbar/navbar-controller.php";
require_once "view/pagination/pagination-service.php";

use Exception;
use p1\core\app\BookConfiguration;
use p1\core\app\UserConfiguration;
use p1\core\database\DatabaseConfiguration;
use p1\session\SessionManager;
use p1\state\State;
use p1\view\home\PaginationService;
use p1\view\RedirectManager;
use p1\view\ViewConfiguration;

class Configuration
{
    private static Configuration $instance;
    private State $state;
    private ViewConfiguration $viewConfiguration;
    private DatabaseConfiguration $databaseConfiguration;
    private UserConfiguration $userConfiguration;
    private BookConfiguration $bookConfiguration;
    private RedirectManager $redirectManager;
    private SessionManager $sessionManager;
    private PaginationService $paginationService;

    public function __construct()
    {
        $this->state = State::instance();
        $this->sessionManager = SessionManager::instance();

        $this->redirectManager = new RedirectManager();

        $this->databaseConfiguration = new DatabaseConfiguration();

        $this->userConfiguration = new UserConfiguration($this->databaseConfiguration);
        $this->bookConfiguration = new BookConfiguration($this->databaseConfiguration);

        $this->paginationService = new PaginationService();

        $this->viewConfiguration = new ViewConfiguration(
            $this->state,
            $this->userConfiguration->createUserCommandHandler(),
            $this->userConfiguration->authenticateUserCommandHandler(),
            $this->bookConfiguration->getBookListCommandHandler(),
            $this->bookConfiguration->getBookDetailsCommandHandler(),
            $this->bookConfiguration->getAllBookTagsUseCase(),
            $this->redirectManager,
            $this->sessionManager,
            $this->paginationService
        );
    }

    public function viewConfiguration(): ViewConfiguration
    {
        return $this->viewConfiguration;
    }

    // SINGLETON SPECIFIC FUNCTIONS

    /**
     * Singleton cloning is forbidden.
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Singleton deserialization is forbidden.
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot deserialize singleton");
    }

    static function instance(): Configuration
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }
}
