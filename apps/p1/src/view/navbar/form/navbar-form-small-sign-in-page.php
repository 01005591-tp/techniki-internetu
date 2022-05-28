<?php
require_once "configuration.php";

use p1\configuration\Configuration;

$navbarController = Configuration::instance()->viewConfiguration()->navbarController();
?>


<form class="d-flex flex-column">
    <input class="form-control my-1" type="search"
           placeholder="<?php echo L::main_navbar_search_input_placeholder ?>"
           aria-label="<?php echo L::main_navbar_search_input_placeholder ?>">
    <button class="my-1 btn btn-outline-success" type="submit">
        <?php echo L::main_navbar_search_search_button ?>
    </button>
    <div class="my-2 btn btn-outline-primary disabled" role="button" aria-pressed="true">
        <?php echo L::main_navbar_sign_in_sign_in_button ?>
    </div>
</form>