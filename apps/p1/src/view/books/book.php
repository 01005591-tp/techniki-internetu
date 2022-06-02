<?php

use p1\configuration\Configuration;

require_once "configuration.php";

$bookController = Configuration::instance()->viewConfiguration()->controllers()->bookController();
$bookDetails = $bookController->getBookDetails();
?>


<div class="shadow-lg p-2 mb-5 bg-body rounded">
    <div id="book-title" class="d-flex flex-column justify-content-center">
        <span class="p-2 h1 mb-1"><?php echo $bookDetails->book()->title(); ?></span>
        <div class="d-flex flex-wrap">
            <?php if (!empty($bookDetails->authors())) {
                foreach ($bookDetails->authors() as $author) {
                    echo '<div class="mb-3 mx-2 text-nowrap author author-' . $author->priority() . '">' . $author->firstName() . ' ' . $author->lastName() . '</div>';
                }
            } ?>
        </div>
    </div>

    <hr/>

    
</div>
