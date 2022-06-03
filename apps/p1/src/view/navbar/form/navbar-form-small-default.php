<?php
require_once "configuration.php";

use p1\configuration\Configuration;

$navbarController = Configuration::instance()->viewConfiguration()->controllers()->navbarController();
?>
<div class="d-flex flex-column">
    <a class="my-2 btn btn-outline-primary text-nowrap" href="/login" role="button" aria-pressed="true">
        <?php echo L::main_navbar_sign_in_sign_in_button ?>
    </a>
</div>

