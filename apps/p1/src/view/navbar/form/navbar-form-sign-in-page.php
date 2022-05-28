<?php
require_once "configuration.php";

use p1\configuration\Configuration;

$navbarController = Configuration::instance()->viewConfiguration()->navbarController();
?>


<form class="d-flex">
    <input class="form-control me-2" type="search"
           placeholder="<?php echo L::main_navbar_search_input_placeholder ?>"
           aria-label="<?php echo L::main_navbar_search_input_placeholder ?>">
    <button class="btn btn-outline-success" type="submit">
        <?php echo L::main_navbar_search_search_button ?>
    </button>
    <div class="ms-2 align-self-center">
        <button class="btn btn-outline-primary disabled" type="button">
            <?php echo L::main_navbar_sign_in_sign_in_button ?>
        </button>
    </div>
</form>