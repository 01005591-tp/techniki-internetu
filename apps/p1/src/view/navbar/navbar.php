<?php

use p1\configuration\Configuration;
use p1\view\navbar\NavbarItem;

$navbarController = Configuration::instance()->navbarController();
$toNavbarItemActiveClass = function (NavbarItem $navbarItem) use ($navbarController) {
    return $navbarController->isActiveItem($navbarItem) ? 'active' : 'inactive';
};

echo '
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="/">
        <img src="/assets/book-icon_small.png" alt="Library"/>
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
     aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item ' . ($toNavbarItemActiveClass(NavbarItem::Home)) . '">
                <a class="nav-link" href="/">Home</a>
            </li>
            <li class="nav-item ' . ($toNavbarItemActiveClass(NavbarItem::About)) . '">
                <a class="nav-link" href="/about">About</a>
            </li>
        </ul>
        <form class="form-inline">
            <input class="form-control mr-sm-1" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success mx-1 my-2 my-sm-0" type="submit">Search</button>
            <button class="btn btn-outline-primary ml-2 my-2 my-sm-0" type="submit">Log In</button>
        </form>
    </div>
</nav>
';
