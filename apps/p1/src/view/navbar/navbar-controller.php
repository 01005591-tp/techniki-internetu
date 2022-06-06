<?php

namespace p1\view\navbar;

require_once "core/observable/observable.php";
require_once "core/observable/observable-map.php";
require_once "session/session-manager.php";
require_once "state.php";

use p1\core\observable\EntryPutSubscriber;
use p1\session\SessionManager;
use p1\session\UserContext;
use p1\state\State;

class NavbarController {
  private NavbarItem $activeItem;
  private State $state;
  private SessionManager $sessionManager;

  public function __construct(State          $state,
                              SessionManager $sessionManager) {
    $this->activeItem = NavbarItem::Home;
    $this->state = $state;
    $this->sessionManager = $sessionManager;
    $subscriber = new class($this) implements EntryPutSubscriber {
      private NavbarController $controller;

      public function __construct(NavbarController $controller) {
        $this->controller = $controller;
      }

      function onNext($item) {
        $this->controller->setActiveItem($item);
      }

      function type(): string {
        return State::ACTIVE_ITEM_KEY;
      }
    };
    $this->state->subscribe()->entryPut($subscriber);
  }

  public function isLoggedInUser(): bool {
    return $this->sessionManager->userContext()->isDefined();
  }

  public function loggedInUser(): UserContext {
    return $this->sessionManager->userContext();
  }

  public function isActiveItem(NavbarItem $item): bool {
    return $this->activeItem === $item;
  }

  public function setActiveItem(NavbarItem $item): void {
    $this->activeItem = $item;
    require "view/navbar/navbar.php";
    switch ($this->activeItem) {
      case NavbarItem::About:
        require "view/about/about.php";
        break;
      case NavbarItem::Login:
        require "view/login/login.php";
        break;
      case NavbarItem::SignUp:
        require "view/login/sign-up/sign-up.php";
        break;
      case NavbarItem::SignOut:
        require "view/login/sign-out/sign-out.php";
        break;
      case NavbarItem::BookDetails:
        require "view/books/book.php";
        break;
      case NavbarItem::BookEdition:
        require "view/books/edition/book-edit.php";
        break;
      case NavbarItem::NotFound404:
        require "view/errors/404.php";
        break;
      default:
        require "view/home/home.php";
    }
  }
}

enum NavbarItem {
  case Home;
  case About;
  case Login;
  case SignUp;
  case SignOut;
  case BookDetails;
  case BookEdition;
  case NotFound404;
}