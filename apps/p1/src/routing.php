<?php

namespace p1\routing;

require_once "config.php";
require_once "state.php";
require_once "view/routing/routing-configuration.php";
require_once "view/navbar/navbar-controller.php";

use p1\session\SessionConstants;
use p1\session\SessionManager;
use p1\state\State;
use p1\view\navbar\NavbarItem;
use p1\view\routing\RoutingConfiguration;

class Router {
  private const PAGE_BEFORE_QUERY_PARAMS_REGEX = '/^([a-zA-Z0-9\/\-_]+)(\??.*)$/';

  private SessionManager $sessionManager;
  private array $endpointResolvers;

  public function __construct(SessionManager       $sessionManager,
                              RoutingConfiguration $routingConfiguration) {
    $this->sessionManager = $sessionManager;
    $this->endpointResolvers = $routingConfiguration->endpointResolvers();
  }

  public function navigate(): void {
    $request = $_SERVER['REQUEST_URI'];
    $this->sessionManager->put(SessionConstants::REQUEST_URI, $request);
    $request = preg_replace(Router::PAGE_BEFORE_QUERY_PARAMS_REGEX, "$1", $request);
    if (!empty($this->endpointResolvers)) {
      foreach ($this->endpointResolvers as $endpointResolver) {
        if ($endpointResolver->resolve($request)) {
          return;
        }
      }
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
        State::instance()->put(State::ACTIVE_ITEM_KEY, NavbarItem::NotFound404);
        break;
    }
  }
}