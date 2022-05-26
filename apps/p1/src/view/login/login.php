<?php

require_once "configuration.php";


?>

<div class="d-flex p-2 justify-content-center">
    <form>
        <div class="mb-3">
            <label for="loginEmailInput" class="form-label">Email address</label>
            <input type="email" class="form-control" id="loginEmailInput" aria-describedby="loginEmailHelp">
            <div id="loginEmailHelp" class="form-text">Your email address is your username.</div>
        </div>
        <div class="mb-3">
            <label for="loginPasswordInput" class="form-label">Password</label>
            <input type="password" class="form-control" id="loginPasswordInput">
        </div>
        <button type="submit" class="btn btn-primary">Log&nbsp;In</button>
        <a href="/sign-up">
            <button type="button" class="btn btn-danger">Sign&nbsp;Up</button>
        </a>
    </form>
</div>