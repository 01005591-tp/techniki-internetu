<?php

namespace p1\view\session;

require_once "view/session/user-context.php";

use Exception;
use L;

class SessionManager
{
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

    private function doStartSession(): void
    {
        if (!session_start()) {
            // TODO: implement error message printer
            echo '<script type="text/javascript">alert(\'' . L::main_errors_global_global_error_message . '\');</script>';
        }
    }

    private function cleanUpSession(): void
    {
        $_SESSION = [];
    }

    public function printSession(): void
    {
        $sessionStatus = session_status();
        switch ($sessionStatus) {
            case PHP_SESSION_NONE:
                echo "Session none<br/>";
                break;
            case PHP_SESSION_ACTIVE:
                echo "Session active";
                $userContext = $_SESSION[SessionConstants::USER_CONTEXT];
                if ($userContext) {
                    echo "<br/>Id: " . $userContext->userId();
                    echo "<br/>Email: " . $userContext->userEmail();
                    echo "<br/>Lang: " . $userContext->userLang();
                    echo "<br/>Roles: ";
                    print_r($userContext->userRoles());
                } else {
                    echo "<br/>No user context";
                }
                break;
            case PHP_SESSION_DISABLED:
                echo "Session disabled";
                break;
        }
    }
}

class SessionConstants
{
    public const USER_CONTEXT = 'USER_CONTEXT';
    public const LANG = 'LANG';

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