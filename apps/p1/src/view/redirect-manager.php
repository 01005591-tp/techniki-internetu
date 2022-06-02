<?php

namespace p1\view;

require_once "core/function/function.php";

use p1\core\function\Runnable;

class RedirectManager
{
    private RedirectToMainPageRunnable $redirectToMainPageRunnable;
    private RedirectTo404NotFoundPageRunnable $redirectTo404NotFoundPageRunnable;

    public function __construct()
    {
        $this->redirectToMainPageRunnable = new RedirectToMainPageRunnable();
        $this->redirectTo404NotFoundPageRunnable = new RedirectTo404NotFoundPageRunnable();
    }

    public function redirectToMainPage(): RedirectToMainPageRunnable
    {
        return $this->redirectToMainPageRunnable;
    }

    public function redirectTo404NotFoundPage(): RedirectTo404NotFoundPageRunnable
    {
        return $this->redirectTo404NotFoundPageRunnable;
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

class RedirectTo404NotFoundPageRunnable implements Runnable
{
    function run(): void
    {
        if (headers_sent()) {
            echo('<script type="text/javascript">window.location\'/404-not-found\';</script>');
        } else {
            header("Location: /404-not-found");
        }
        exit();
    }
}