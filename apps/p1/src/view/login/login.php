<?php

use p1\configuration\Configuration;

require_once "configuration.php";

$loginController = Configuration::instance()->viewConfiguration()->loginController();
$loginController->login();
?>

<div class="d-flex p-2 justify-content-center">
    <form method="post" action="/login">
        <div class="mb-3">
            <label for="loginEmailInput"
                   class="form-label"><?php echo L::main_sign_in_email_address_input_label ?></label>
            <input type="email" name="loginEmailInput"
                   class="form-control" id="loginEmailInput" aria-describedby="loginEmailHelp"
                   value="<?php echo $loginController->currentEmailAddress() ?>">
            <div id="loginEmailHelp"
                 class="form-text"><?php echo L::main_sign_in_email_address_input_description ?></div>
        </div>
        <div class="mb-3">
            <label for="loginPasswordInput"
                   class="form-label"><?php echo L::main_sign_in_password_input_label ?></label>
            <input type="password" name="loginPasswordInput"
                   class="form-control" id="loginPasswordInput">
        </div>
        <button type="submit" name="login-login-btn"
                class="btn btn-primary"><?php echo L::main_sign_in_log_in_button ?></button>
        <a href="/sign-up">
            <button type="button" class="btn btn-danger"><?php echo L::main_sign_in_sign_up_button ?></button>
        </a>
    </form>
</div>