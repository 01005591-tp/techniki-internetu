<?php
require_once "configuration.php";

use p1\configuration\Configuration;

$navbarController = Configuration::instance()->viewConfiguration()->controllers()->navbarController();
?>

<dov class="d-flex">
    <span class="d-flex align-self-center ms-2 logged-in-user-email"
          aria-label="<?php echo L::main_navbar_sign_in_signed_in_user_email_label ?>">
        <img src="/assets/user-24-icon-aqua.png" alt="User icon"/>
        <?php echo $navbarController->loggedInUser()->userEmail(); ?>
    </span>
    <a class="ms-2 align-self-center btn btn-outline-danger text-nowrap" href="/sign-out"
       role="button" aria-pressed="true">
        <?php echo L::main_navbar_sign_in_sign_out_button ?>
    </a>
</dov>