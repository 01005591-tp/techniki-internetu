<?php

use p1\configuration\Configuration;
use p1\view\navbar\NavbarItem;

$navbarController = Configuration::instance()->navbarController();
$toNavbarItemActiveClass = function (NavbarItem $navbarItem) use ($navbarController) {
    return $navbarController->isActiveItem($navbarItem) ? 'active' : 'inactive';
};

echo '
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="/">
        <img src="/assets/book-icon_small.png" alt="Library"/>
    </a>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item ' . ($toNavbarItemActiveClass(NavbarItem::Home)) . '">
                <a class="nav-link" href="/">Home</a>
            </li>
            <li class="nav-item ' . ($toNavbarItemActiveClass(NavbarItem::About)) . '">
                <a class="nav-link" href="/about">About</a>
            </li>
        </ul>
        <div class="d-flex flex-md-row-reverse">
            <input class="form-control p-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success p-2" type="submit">Search</button>
            <button class="btn btn-outline-primary p-2" type="submit">Log In</button>
        </div>
    </div>
</nav>
    ';
