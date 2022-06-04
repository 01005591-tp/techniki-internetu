<?php

use p1\configuration\Configuration;
use p1\core\domain\book\BookState;
use p1\core\domain\language\Language;
use p1\core\function\Consumer;
use p1\core\function\Function2;
use p1\core\function\Runnable;
use p1\view\home\HomeController;

require_once "configuration.php";
require_once "core/domain/book/book-state.php";
require_once "core/domain/book/book-list.php";
require_once "core/domain/language/language.php";
require_once "core/function/function.php";

require_once "view/home/home-controller.php";

$homeController = Configuration::instance()->viewConfiguration()->controllers()->homeController();
$bookList = $homeController->findBooks();
$maybePaginationData = $homeController->paginationData();
$addPagination = new class implements Consumer {
    function consume($value): void
    {
        $paginationData = $value;
        require "view/pagination/pagination-component.php";
    }
};
$searchComponentRunnable = new class($homeController) implements Runnable {
    private HomeController $homeController;

    public function __construct(HomeController $homeController)
    {
        $this->homeController = $homeController;
    }

    function run(): void
    {
        $availableTags = $this->homeController->availableTags();
        $searchCriteria = $this->homeController->searchCriteria();
        require "view/home/search-component.php";
    }
};
?>

<div class="shadow-lg p-2 mb-5 bg-body rounded">
    <div class="d-flex justify-content-center">
        <span class="p2 h1 mb-4"><?php echo L::main_home_book_list_header ?></span>
    </div>

    <hr/>
    <?php $searchComponentRunnable->run(); ?>
    <hr/>

    <?php $maybePaginationData->peek($addPagination); ?>

    <hr/>

    <div class="d-flex flex-wrap justify-content-center">
        <?php
        foreach ($bookList->books() as $book) {
            $imgUri = (!is_null($book->imageUri())) ? $book->imageUri() : "/assets/book-icon.svg";
            $bookStateDisplayName = BookState::of($book->state())
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
                <a href="/books/' . $book->nameId() . '">
                    <img src="' . $imgUri . '" class="card-img-top" alt="Book icon">
                </a>
                <div class="card-body">
                    <h5 class="card-title">' . $book->title() . '</h5>
                    <p class="card-text">
                      <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>ISBN:</strong> ' . $book->isbn() . '</li>
                        <li class="list-group-item"><strong>' . L::main_home_book_list_entry_language . ':</strong> ' . $languageDisplayName . '</li>
                        <li class="list-group-item"><strong>' . L::main_home_book_list_entry_state . ':</strong> ' . $bookStateDisplayName . '</li>
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

    <?php $maybePaginationData->peek($addPagination); ?>
</div>