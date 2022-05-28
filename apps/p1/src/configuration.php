<?php

namespace p1\configuration;

require_once "state.php";
require_once "i18n/i18n-configuration.php";
require_once "core/database/database-configuration.php";
require_once "core/app/user-configuration.php";
require_once "view/redirect-manager.php";
require_once "view/view-configuration.php";
require_once "view/navbar/navbar-controller.php";
require_once "view/session/session-manager.php";

use Exception;
use p1\core\app\UserConfiguration;
use p1\core\database\DatabaseConfiguration;
use p1\i18n\I18nConfiguration;
use p1\state\State;
use p1\view\RedirectManager;
use p1\view\session\SessionManager;
use p1\view\ViewConfiguration;

class Configuration
{
    private static Configuration $instance;
    private State $state;
    private ViewConfiguration $viewConfiguration;
    private DatabaseConfiguration $databaseConfiguration;
    private UserConfiguration $userConfiguration;
    private SessionManager $sessionManager;
    private RedirectManager $redirectManager;
    private I18nConfiguration $i18nConfiguration;

    private function __construct()
    {
        $this->state = State::instance();
        $this->i18nConfiguration = I18nConfiguration::instance();

        $this->sessionManager = new SessionManager();
        $this->redirectManager = new RedirectManager();

        $this->databaseConfiguration = new DatabaseConfiguration();
        $this->userConfiguration = new UserConfiguration($this->databaseConfiguration);
        $this->viewConfiguration = new ViewConfiguration(
            $this->state,
            $this->userConfiguration->createUserCommandHandler(),
            $this->userConfiguration->authenticateUserCommandHandler(),
            $this->sessionManager,
            $this->redirectManager
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
