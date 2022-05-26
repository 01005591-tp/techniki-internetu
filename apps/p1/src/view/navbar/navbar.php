<?php

use p1\configuration\Configuration;
use p1\view\navbar\NavbarItem;

$navbarController = Configuration::instance()->navbarController();
$toNavbarItemActiveClass = function (NavbarItem $navbarItem) use ($navbarController): string {
    return $navbarController->isActiveItem($navbarItem) ? 'active' : '';
};

$loginButtonHtml = function () use ($navbarController): string {
    if ($navbarController->isActiveItem(NavbarItem::Login)) {
        return '<div class="ms-2">
                    <button class="btn btn-outline-primary disabled" type="button">Sign&nbsp;In</button>
                </div>';
    } else {
        return '<a class="ms-2" href="/login">
                    <button class="btn btn-outline-primary" type="button">Sign&nbsp;In</button>
                </a>';
    }
};
echo '
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">
            <img src="/assets/book-icon_small.png" alt="Library"/>
        </a>
        <button class="navbar-toggler" type="button" 
            data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" 
            aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link ' . ($toNavbarItemActiveClass(NavbarItem::Home)) . '" href="/">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link ' . ($toNavbarItemActiveClass(NavbarItem::About)) . '" href="/about">About</a>
                </li>
            </ul>
            <form class="d-flex">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
                ' . $loginButtonHtml() . '
            </form>
        </div>
    </div>
</nav>
';
