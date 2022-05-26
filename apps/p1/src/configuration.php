<?php

namespace p1\configuration;
require_once "core/database/database-connection.php";
require_once "../../vendor/philipp15b/php-i18n/i18n.class.php";
require_once "../../vendor/mustangostang/spyc/Spyc.php";

use Exception;
use i18n;
use p1\core\database\DatabaseConnection;
use p1\view\navbar\NavbarController;

class Configuration
{
    private static Configuration $instance;
    private NavbarController $navbarController;
    private DatabaseConnection $databaseConnection;
    private i18n $i18n;

    private function __construct()
    {
        $this->navbarController = new NavbarController();
        $this->databaseConnection = new DatabaseConnection();
        $this->i18n = $this->initI18n();
    }

    public function navbarController(): NavbarController
    {
        return $this->navbarController;
    }

    private function initI18n(): i18n
    {
        $i18n = new i18n();
        $i18n->setFilePath("i18n/bundle_{LANGUAGE}.yml");
        $i18n->setCachePath("/tmp/cache");
//        $i18n->setForcedLang("pl");
        $i18n->setFallbackLang("en");
        $i18n->setSectionSeparator("_");
        $i18n->setMergeFallback(true);
        $i18n->setPrefix("L");
        $i18n->init();
        return $i18n;
    }

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
