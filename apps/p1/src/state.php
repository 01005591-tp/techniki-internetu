<?php

namespace p1\state;

require_once "core/observable/observable-map.php";

use Exception;
use p1\core\observable\ObservableMap;

class State extends ObservableMap
{
    private static State $instance;

    private function __construct()
    {
        parent::__construct();
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

    public static function instance(): State
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }
}

