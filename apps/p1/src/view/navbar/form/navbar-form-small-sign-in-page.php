<?php
require_once "configuration.php";

use p1\configuration\Configuration;

$navbarController = Configuration::instance()->viewConfiguration()->controllers()->navbarController();
?>


<div class="d-flex flex-column">
    <div class="my-2 btn btn-outline-primary disabled text-nowrap" role="button" aria-pressed="true">
      <?php echo L::main_navbar_sign_in_sign_in_button ?>
    </div>
</div>