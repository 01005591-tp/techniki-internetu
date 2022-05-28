<?php

use p1\configuration\Configuration;
use p1\view\navbar\NavbarItem;

$navbarController = Configuration::instance()->viewConfiguration()->navbarController();

$toNavbarItemActiveCssClass = function (NavbarItem $navbarItem) use ($navbarController): string {
    return $navbarController->isActiveItem($navbarItem) ? 'active' : '';
};

$loginButtonHtml = function () use ($navbarController): void {
    if ($navbarController->isActiveItem(NavbarItem::Login)) {
        echo '<div class="ms-2">
                  <button class="btn btn-outline-primary disabled" type="button">' . L::main_navbar_sign_in_sign_in_button . '</button>
              </div>';
    } else {
        echo '<a class="ms-2" href="/login">
                <button class="btn btn-outline-primary" type="button">' . L::main_navbar_sign_in_sign_in_button . '</button>
              </a>';
    }
};
?>
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
            <form class="d-flex">
                <input class="form-control me-2" type="search"
                       placeholder="<?php echo L::main_navbar_search_input_placeholder ?>"
                       aria-label="<?php echo L::main_navbar_search_input_placeholder ?>">
                <button class="btn btn-outline-success"
                        type="submit"><?php echo L::main_navbar_search_search_button ?></button>
                <?php $loginButtonHtml(); ?>
            </form>
        </div>
    </div>
</nav>
