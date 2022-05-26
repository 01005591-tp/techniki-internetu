<?php


?>

<div class="d-flex p-2 justify-content-center">
    <form>
        <div class="mb-3">
            <label for="signUpEmailInput"
                   class="form-label"><?php echo L::main_sign_up_email_address_input_label ?></label>
            <input type="email" class="form-control" id="signUpEmailInput" aria-describedby="signUpEmailHelp">
            <div id="signUpEmailHelp"
                 class="form-text"><?php echo L::main_sign_up_email_address_input_description ?></div>
        </div>
        <div class="mb-3">
            <label for="signUpPasswordInput"
                   class="form-label"><?php echo L::main_sign_up_password_input_label ?></label>
            <input type="password" class="form-control" id="signUpPasswordInput" aria-describedby="signUpPasswordHelp">
            <div id="signUpPasswordHelp" class="form-text">
                <?php echo L::main_sign_up_password_input_description ?>
            </div>
        </div>
        <div class="mb-3">
            <label for="signUpPasswordRepeatInput"
                   class="form-label"><?php echo L::main_sign_up_repeat_password_input_label ?></label>
            <input type="password" class="form-control" id="signUpPasswordRepeatInput"
                   aria-describedby="signUpPasswordRepeatHelp">
            <div id="signUpPasswordRepeatHelp" class="form-text">
                <?php echo L::main_sign_up_repeat_password_input_description ?>
            </div>
        </div>
        <button type="submit" class="btn btn-primary"><?php echo L::main_sign_up_create_user_button ?></button>
    </form>
</div>