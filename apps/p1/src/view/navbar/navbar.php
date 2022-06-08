<?php

require_once "configuration.php";

use p1\configuration\Configuration;
use p1\view\navbar\NavbarItem;

$navbarController = Configuration::instance()->viewConfiguration()->controllers()->navbarController();

$toNavbarItemActiveCssClass = function (NavbarItem $navbarItem) use ($navbarController): string {
  return $navbarController->isActiveItem($navbarItem) ? 'active' : '';
};

$loggedInUserComponents = function () use ($navbarController): void {
  if ($navbarController->isLoggedInUser()) {
    require "view/navbar/form/navbar-form-logged-in-user.php";
  } else if ($navbarController->isActiveItem(NavbarItem::Login)) {
    require "view/navbar/form/navbar-form-sign-in-page.php";
  } else {
    require "view/navbar/form/navbar-form-default.php";
  }
};

$loggedInUserComponentsSmall = function () use ($navbarController): void {
  if ($navbarController->isLoggedInUser()) {
    require "view/navbar/form/navbar-form-small-logged-in-user.php";
  } else if ($navbarController->isActiveItem(NavbarItem::Login)) {
    require "view/navbar/form/navbar-form-small-sign-in-page.php";
  } else {
    require "view/navbar/form/navbar-form-small-default.php";
  }
};
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-md-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">
            <img src="/assets/book-icon_small.png" alt="Library logo"/>
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
                    <a class="nav-link <?php echo($toNavbarItemActiveCssClass(NavbarItem::Home)); ?>" href="/">
                      <?php echo L::main_navbar_home ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo($toNavbarItemActiveCssClass(NavbarItem::About)); ?>" href="/about">
                      <?php echo L::main_navbar_about ?>
                    </a>
                </li>
            </ul>
            <div class="d-none d-lg-block">
              <?php $loggedInUserComponents(); ?>
            </div>
            <div class="d-xxl-none d-xl-none d-lg-none d-block">
              <?php $loggedInUserComponentsSmall(); ?>
            </div>
        </div>
    </div>
</nav>
