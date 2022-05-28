<?php
require_once "configuration.php";

use p1\configuration\Configuration;

$navbarController = Configuration::instance()->viewConfiguration()->navbarController();
?>

<form class="d-flex flex-column">
    <input class="form-control my-1" type="search"
           placeholder="<?php echo L::main_navbar_search_input_placeholder ?>"
           aria-label="<?php echo L::main_navbar_search_input_placeholder ?>">
    <button class="btn btn-outline-success my-1"
            type="submit">
        <?php echo L::main_navbar_search_search_button ?>
    </button>
    <span class="d-flex align-self-center my-2 logged-in-user-email"
          aria-label="<?php echo L::main_navbar_sign_in_signed_in_user_email_label ?>">
        <img src="/assets/user-24-icon-aqua.png" alt="User icon"/>
        <?php echo $navbarController->loggedInUser()->userEmail(); ?>
    </span>
    <a class="my-2 btn btn-outline-danger" role="button" href="/sign-out" aria-pressed="true">
        <?php echo L::main_navbar_sign_in_sign_out_button ?>
    </a>
</form>