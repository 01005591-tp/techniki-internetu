<?php
require_once "configuration.php";

use p1\configuration\Configuration;

$navbarController = Configuration::instance()->viewConfiguration()->controllers()->navbarController();
?>

<form class="d-flex">
    <input class="form-control me-2" type="search"
           placeholder="<?php echo L::main_navbar_search_input_placeholder ?>"
           aria-label="<?php echo L::main_navbar_search_input_placeholder ?>">
    <button class="btn btn-outline-success text-nowrap"
            type="submit">
        <?php echo L::main_navbar_search_search_button ?>
    </button>
    <span class="d-flex align-self-center ms-2 logged-in-user-email"
          aria-label="<?php echo L::main_navbar_sign_in_signed_in_user_email_label ?>">
        <img src="/assets/user-24-icon-aqua.png" alt="User icon"/>
        <?php echo $navbarController->loggedInUser()->userEmail(); ?>
    </span>
    <a class="ms-2 align-self-center btn btn-outline-danger text-nowrap" href="/sign-out"
       role="button" aria-pressed="true">
        <?php echo L::main_navbar_sign_in_sign_out_button ?>
    </a>
</form>