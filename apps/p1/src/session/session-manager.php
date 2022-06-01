<?php

namespace p1\view\session;

require_once "core/function/option.php";
require_once "session/user-context.php";

use Exception;
use L;
use p1\core\function\Option;

class SessionManager
{
    private static SessionManager $instance;

    public function sessionStart(UserContext $context): void
    {
        $this->cleanUpSession();
        $this->recoverSession();
        $_SESSION[SessionConstants::USER_CONTEXT] = $context;
        // LANG required for i18n
        $_SESSION[SessionConstants::LANG] = $context->userLang();
    }

    public function sessionDestroy(): void
    {
        $this->cleanUpSession();
    }

    public function recoverSession(): void
    {
        if (PHP_SESSION_ACTIVE != session_status()) {
            $this->doStartSession();
        }
    }

    public function userContext(): ?UserContext
    {
        return PHP_SESSION_ACTIVE === session_status() && array_key_exists(SessionConstants::USER_CONTEXT, $_SESSION)
            ? $_SESSION[SessionConstants::USER_CONTEXT]
            : null;
    }

    public function get(string $key): Option
    {
        return PHP_SESSION_ACTIVE === session_status() && array_key_exists($key, $_SESSION)
            ? Option::of($_SESSION[$key])
            : Option::none();
    }

    public function put(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    private function doStartSession(): void
    {
        if (!session_start()) {
            echo '<script type="text/javascript">alert(\'' . L::main_errors_global_global_error_message . '\');</script>';
        }
    }

    private function cleanUpSession(): void
    {
        $_SESSION = [];
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

    static function instance(): SessionManager
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }
}

class SessionConstants
{
    public const USER_CONTEXT = 'USER_CONTEXT';
    public const LANG = 'LANG';
    public const ALERT_MESSAGES = 'ALERT_MESSAGES';
    public const BOOK_LIST_CURRENT_PAGE = "BOOK_LIST_CURRENT_PAGE";

    /**
     * Utility class instantiation is forbidden.
     * @throws Exception
     */
    private function __construct()
    {
        throw new Exception("Cannot instantiate utility class");
    }


    /**
     * Utility class cloning is forbidden.
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Utility class deserialization is forbidden.
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot deserialize utility class");
    }

}