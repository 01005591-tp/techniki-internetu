<?php

namespace p1\routing;

use p1\config\Config;
use p1\state\State;
use p1\view\navbar\NavbarController;
use p1\view\navbar\NavbarItem;

require_once "config.php";
require_once "state.php";
require_once "view/navbar/navbar-controller.php";

class Router
{
    public static function navigate(): void
    {
        $request = $_SERVER['REQUEST_URI'];
        switch ($request) {
            case '':
            case '/':
                State::instance()->put(NavbarController::ACTIVE_ITEM_KEY, NavbarItem::Home);
                break;
            case '/about':
                State::instance()->put(NavbarController::ACTIVE_ITEM_KEY, NavbarItem::About);
                break;
            case '/login':
                State::instance()->put(NavbarController::ACTIVE_ITEM_KEY, NavbarItem::Login);
                break;
            case '/sign-up':
                State::instance()->put(NavbarController::ACTIVE_ITEM_KEY, NavbarItem::SignUp);
                break;
            default:
                http_response_code(404);
                require Config::instance()->rootDir() . '/view/errors/404.php';
                break;
        }
    }
}