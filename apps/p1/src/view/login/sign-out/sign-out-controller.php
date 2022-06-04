<?php

namespace p1\view\login\signout;

require_once "session/session-manager.php";
require_once "view/alerts/alert-service.php";
require_once "view/redirect-manager.php";

use L;
use p1\session\SessionManager;
use p1\view\alerts\AlertService;
use p1\view\RedirectManager;

class SignOutController
{
    private SessionManager $sessionManager;
    private RedirectManager $redirectManager;
    private AlertService $alertService;

    public function __construct(SessionManager  $sessionManager,
                                RedirectManager $redirectManager,
                                AlertService    $alertService)
    {
        $this->sessionManager = $sessionManager;
        $this->redirectManager = $redirectManager;
        $this->alertService = $alertService;
    }


    public function logout(): void
    {
        // TODO: ALERTS - fix alert printing after redirect
        $this->sessionManager->sessionDestroy();
        $this->alertService->success(L::main_sign_out_signed_out_successfully_msg);
        $this->redirectManager->redirectToMainPage()->run();
    }
}