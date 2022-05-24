<?php

namespace p1\view\navbar;

require_once "state.php";
require_once "core/observable/observable.php";
require_once "core/observable/observable-map.php";

use p1\core\observable\EntryPutSubscriber;
use p1\state\State;

class NavbarController
{
    public const ACTIVE_ITEM_KEY = 'View.Navbar.ActiveItem';
    private NavbarItem $activeItem;

    public function __construct()
    {
        $this->activeItem = NavbarItem::Home;
        $subscriber = new class($this) implements EntryPutSubscriber {
            private NavbarController $controller;

            public function __construct(NavbarController $controller)
            {
                $this->controller = $controller;
            }

            function onNext($item)
            {
                $this->controller->setActiveItem($item);
            }

            function type(): string
            {
                return NavbarController::ACTIVE_ITEM_KEY;
            }
        };
        State::instance()->subscribe()->entryPut($subscriber);
    }

    public function isActiveItem(NavbarItem $item): bool
    {
        return $this->activeItem === $item;
    }

    public function setActiveItem(NavbarItem $item): void
    {
        $this->activeItem = $item;
        require "view/navbar/navbar.php";
        switch ($this->activeItem) {
            case NavbarItem::About:
                require "view/about/about.php";
                break;
            default:
                require "view/home/home.php";
        }
    }
}

enum NavbarItem
{
    case Home;
    case About;
}