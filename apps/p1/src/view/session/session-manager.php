<?php

namespace p1\view\session;

require_once "view/session/user-context.php";

use L;

class SessionManager
{
    public function sessionStart(UserContext $context): void
    {
        $this->cleanUpSession();
        $this->recoverSession();
        $_SESSION['USER_CONTEXT'] = $context;
        // LANG required for i18n
        $_SESSION['LANG'] = $context->userLang();
    }

    public function recoverSession(): void
    {
        if (PHP_SESSION_ACTIVE != session_status()) {
            $this->doStartSession();
        }
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
                $userContext = $_SESSION['USER_CONTEXT'];
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