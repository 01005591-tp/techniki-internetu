<?php

namespace p1\state;

require_once "core/observable/observable-map.php";

use Exception;
use p1\core\observable\ObservableMap;

class State extends ObservableMap {
  public const ACTIVE_ITEM_KEY = 'View.Navbar.ActiveItem';
  public const SIGN_UP_FORM_PROVIDED_EMAIL = "View.Login.SignUp.EmailInputValue";
  public const LOGIN_FORM_PROVIDED_EMAIL = "View.Login.EmailInputValue";

  private static State $instance;

  private function __construct() {
    parent::__construct();
  }

  /**
   * Singleton cloning is forbidden.
   * @return void
   */
  private function __clone() {}

  /**
   * Singleton deserialization is forbidden.
   * @throws Exception
   */
  public function __wakeup() {
    throw new Exception("Cannot deserialize singleton");
  }

  public static function instance(): State {
    if (!isset(self::$instance)) {
      self::$instance = new static();
    }
    return self::$instance;
  }
}

