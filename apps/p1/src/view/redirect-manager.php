<?php

namespace p1\view;

require_once "core/function/function.php";

use p1\core\function\Runnable;

class RedirectManager
{
    private RedirectToMainPageRunnable $redirectToMainPageRunnable;

    public function __construct()
    {
        $this->redirectToMainPageRunnable = new RedirectToMainPageRunnable();
    }

    public function redirectToMainPage(): RedirectToMainPageRunnable
    {
        return $this->redirectToMainPageRunnable;
    }
}

class RedirectToMainPageRunnable implements Runnable
{
    function run(): void
    {
        if (headers_sent()) {
            echo('<script type="text/javascript">window.location\'/\';</script>');
        } else {
            header("Location: /");
        }
        exit();
    }
}