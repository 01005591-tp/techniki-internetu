<?php

use p1\view\navbar\NavbarController;
use p1\view\navbar\NavbarItem;

require_once "view/navbar/navbar-controller.php";

$navbarController = new NavbarController();

$toNavbarItemActiveClass = function (NavbarItem $navbarItem) use ($navbarController) {
    return $navbarController->isActiveItem($navbarItem) ? 'active' : 'inactive';
};

echo '
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">
        <img src="/assets/book-icon.png" alt="Library"/>
    </a>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item ' . ($toNavbarItemActiveClass(NavbarItem::Home)) . '">
                <a class="nav-link" href="#">Home</a>
            </li>
            <li class="nav-item ' . ($toNavbarItemActiveClass(NavbarItem::About)) . '">
                <a class="nav-link" href="#">About</a>
            </li>
        </ul>
        <form class="form-inline my-2 my-lg-0">
            <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
            <button class="btn btn-outline-primary my-2 my-sm-0" type="submit">Log In</button>
        </form>
    </div>
</nav>
    ';

?>
