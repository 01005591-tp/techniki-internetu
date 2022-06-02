<?php

namespace p1\routing;

use p1\config\Config;
use p1\state\State;
use p1\view\navbar\NavbarItem;
use p1\view\session\SessionConstants;
use p1\view\session\SessionManager;

require_once "config.php";
require_once "state.php";
require_once "view/navbar/navbar-controller.php";

class Router
{
    private const PAGE_BEFORE_QUERY_PARAMS_REGEX = '/^([a-zA-Z0-9\/\-_]+)(\??.*)$/';
    private const GET_SINGLE_BOOK_ENDPOINT_REGEX = '/^(\/books\/){1}([^\/\?]+){1}$/';
    private const GET_SINGLE_BOOK_ENDPOINT_BOOK_NAME_ID_PATH_PARAM_REGEX = '/^(\/books\/){1}([^\/\?]+){1}$/';

    private SessionManager $sessionManager;

    public function __construct(SessionManager $sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }

    public function navigate(): void
    {
        $request = $_SERVER['REQUEST_URI'];
        $request = preg_replace(Router::PAGE_BEFORE_QUERY_PARAMS_REGEX, "$1", $request);
        if ($this->resolveGetBookDetailsByBookNameIdEndpoint($request)) {
            return;
        }
        switch ($request) {
            case '':
            case '/':
            case '/book-list':
                State::instance()->put(State::ACTIVE_ITEM_KEY, NavbarItem::Home);
                break;
            case '/about':
                State::instance()->put(State::ACTIVE_ITEM_KEY, NavbarItem::About);
                break;
            case '/login':
                State::instance()->put(State::ACTIVE_ITEM_KEY, NavbarItem::Login);
                break;
            case '/sign-up':
                State::instance()->put(State::ACTIVE_ITEM_KEY, NavbarItem::SignUp);
                break;
            case '/sign-out':
                State::instance()->put(State::ACTIVE_ITEM_KEY, NavbarItem::SignOut);
                break;
            default:
                http_response_code(404);
                require Config::instance()->rootDir() . '/view/errors/404.php';
                break;
        }
    }

    private function resolveGetBookDetailsByBookNameIdEndpoint(string $request): bool
    {
        if (preg_match(Router::GET_SINGLE_BOOK_ENDPOINT_REGEX, $request)) {
            $bookNameId = preg_replace(Router::GET_SINGLE_BOOK_ENDPOINT_BOOK_NAME_ID_PATH_PARAM_REGEX, "$2", $request);
            $this->sessionManager->put(SessionConstants::BOOK_DETAILS_BOOK_NAME_ID, $bookNameId);
            State::instance()->put(State::ACTIVE_ITEM_KEY, NavbarItem::BookDetails);
            return true;
        } else {
            return false;
        }
    }
}