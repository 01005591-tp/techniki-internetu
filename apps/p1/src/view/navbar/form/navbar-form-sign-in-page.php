<?php
require_once "configuration.php";

use p1\configuration\Configuration;

$navbarController = Configuration::instance()->viewConfiguration()->controllers()->navbarController();
?>


<div class="d-flex">
    <div class="ms-2 align-self-center">
        <button class="btn btn-outline-primary disabled text-nowrap" type="button">
          <?php echo L::main_navbar_sign_in_sign_in_button ?>
        </button>
    </div>
</div>