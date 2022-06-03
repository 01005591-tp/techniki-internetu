<?php
require_once "configuration.php";

use p1\configuration\Configuration;

$navbarController = Configuration::instance()->viewConfiguration()->controllers()->navbarController();
?>

<div class="d-flex flex-column">
    <span class="d-flex align-self-center my-2 logged-in-user-email"
          aria-label="<?php echo L::main_navbar_sign_in_signed_in_user_email_label ?>">
        <img src="/assets/user-24-icon-aqua.png" alt="User icon"/>
        <?php echo $navbarController->loggedInUser()->userEmail(); ?>
    </span>
    <a class="my-2 btn btn-outline-danger text-nowrap" role="button" href="/sign-out" aria-pressed="true">
        <?php echo L::main_navbar_sign_in_sign_out_button ?>
    </a>
</div>