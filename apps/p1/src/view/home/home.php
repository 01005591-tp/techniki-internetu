<?php

use p1\configuration\Configuration;
use p1\core\domain\book\BookAvailability;
use p1\core\domain\language\Language;
use p1\core\function\Consumer;
use p1\core\function\Function2;

require_once "configuration.php";
require_once "core/domain/book/book-availability.php";
require_once "core/domain/book/book-list.php";
require_once "core/domain/language/language.php";
require_once "core/function/function.php";

$homeController = Configuration::instance()->viewConfiguration()->controllers()->homeController();
$bookList = $homeController->getDefaultBookList();
$maybePaginationData = $homeController->paginationData();
$addNavbar = new class implements Consumer {
    function consume($value): void
    {
        $paginationData = $value;
        require "view/pagination/pagination-component.php";
    }
}
?>

<div class="shadow-lg p-2 mb-5 bg-body rounded">
    <div class="d-flex justify-content-center">
        <span class="p2 h1 mb-4"><?php echo L::main_home_book_list_header ?></span>
    </div>

    <?php $maybePaginationData->peek($addNavbar); ?>

    <hr/>

    <div class="d-flex flex-wrap justify-content-center">
        <?php
        foreach ($bookList->books() as $book) {
            $imgUri = (!is_null($book->imageUri())) ? $book->imageUri() : "/assets/book-icon.svg";
            $availabilityDisplayName = BookAvailability::of($book->state())
                ->map(new class implements Function2 {
                    function apply($value)
                    {
                        return $value->displayName();
                    }
                })
                ->orElse('');
            $languageDisplayName = Language::ofOrUnknown($book->language())->displayName();
            echo '
        <div id="book-list-cards-container" class="p-2">
            <div class="card">
                <a href="/">
                    <img src="' . $imgUri . '" class="card-img-top" alt="Book icon">
                </a>
                <div class="card-body">
                    <h5 class="card-title">' . $book->title() . '</h5>
                    <p class="card-text">
                      <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>ISBN-13:</strong> ' . $book->isbn() . '</li>
                        <li class="list-group-item"><strong>' . L::main_home_book_list_entry_language . ':</strong> ' . $languageDisplayName . '</li>
                        <li class="list-group-item"><strong>' . L::main_home_book_list_entry_state . ':</strong> ' . $availabilityDisplayName . '</li>
                      </ul>
                    </p>
                    <p class="card-text">' . $book->description() . '</p>
                </div>
            </div>
        </div>
        ';
        } ?>
    </div>

    <hr/>

    <?php $maybePaginationData->peek($addNavbar); ?>
</div>