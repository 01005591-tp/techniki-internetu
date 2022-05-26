<?php

require_once "configuration.php";


?>

<div class="d-flex p-2 justify-content-center">
    <form>
        <div class="mb-3">
            <label for="signUpEmailInput" class="form-label">Email address</label>
            <input type="email" class="form-control" id="signUpEmailInput" aria-describedby="signUpEmailHelp">
            <div id="signUpEmailHelp" class="form-text">Your email address is your username.</div>
        </div>
        <div class="mb-3">
            <label for="signUpPasswordInput" class="form-label">Password</label>
            <input type="password" class="form-control" id="signUpPasswordInput" aria-describedby="signUpPasswordHelp">
            <div id="signUpPasswordHelp" class="form-text">
                Password must be min. 6 characters long and contain at least one digit and a special character
            </div>
        </div>
        <div class="mb-3">
            <label for="signUpPasswordRepeatInput" class="form-label">Repeat password</label>
            <input type="password" class="form-control" id="signUpPasswordRepeatInput"
                   aria-describedby="signUpPasswordRepeatHelp">
            <div id="signUpPasswordRepeatHelp" class="form-text">
                Repeat your password here
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Create&nbsp;user</button>
    </form>
</div>