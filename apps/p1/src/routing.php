<?php

namespace p1\routing;

class Router
{
    public static function navigate(): void
    {
        $request = $_SERVER['REQUEST_URI'];
        switch ($request) {
            case '':
            case '/':
                require __DIR__ . '/view/start-page/start-page.php';
                break;
            default:
                http_response_code(404);
                require __DIR__ . '/view/errors/404.php';
                break;
        }
    }
}