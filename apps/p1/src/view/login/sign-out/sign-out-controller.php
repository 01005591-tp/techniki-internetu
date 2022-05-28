<?php

namespace p1\view\login\signout;

require_once "view/redirect-manager.php";
require_once "view/session/session-manager.php";

use p1\view\RedirectManager;
use p1\view\session\SessionManager;

class SignOutController
{
    private SessionManager $sessionManager;
    private RedirectManager $redirectManager;

    public function __construct(SessionManager  $sessionManager,
                                RedirectManager $redirectManager)
    {
        $this->sessionManager = $sessionManager;
        $this->redirectManager = $redirectManager;
    }


    public function logout(): void
    {
        $this->sessionManager->sessionDestroy();
        $this->redirectManager->redirectToMainPage()->run();
    }
}