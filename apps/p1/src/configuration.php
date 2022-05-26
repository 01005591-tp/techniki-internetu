<?php

namespace p1\configuration;

require_once "state.php";
require_once "i18n/i18n-configuration.php";

use Exception;
use p1\i18n\I18nConfiguration;
use p1\state\State;
use p1\view\navbar\NavbarController;

class Configuration
{
    private static Configuration $instance;
    private State $state;
    private NavbarController $navbarController;
    private I18nConfiguration $i18nConfiguration;

    private function __construct()
    {
        $this->state = State::instance();
        $this->navbarController = new NavbarController($this->state);
        $this->i18nConfiguration = I18nConfiguration::instance();
    }

    public function navbarController(): NavbarController
    {
        return $this->navbarController;
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
