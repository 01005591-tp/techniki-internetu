<?php

use p1\configuration\Configuration;
use p1\core\domain\book\BookListEntry;
use p1\core\function\Consumer;
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
  function consume($value): void {
    $paginationData = $value;
    require "view/pagination/pagination-component.php";
  }
};
$searchComponentLoader = new class($homeController) implements Runnable {
  private HomeController $homeController;

  public function __construct(HomeController $homeController) {
    $this->homeController = $homeController;
  }

  function run(): void {
    $availableTags = $this->homeController->availableTags();
    $searchCriteria = $this->homeController->searchCriteria();
    require "view/home/search-component.php";
  }
};

class BookCardComponentLoader implements Runnable {
  private BookListEntry $book;

  public function __construct(BookListEntry $book) {
    $this->book = $book;
  }

  function run(): void {
    $book = $this->book;
    require "view/home/book-component.php";
  }
}

?>

<div class="shadow-lg p-2 mb-5 bg-body rounded">
    <div class="d-flex justify-content-center">
        <span class="p2 h1 mb-4"><?php echo L::main_home_book_list_header ?></span>
    </div>

    <hr/>
  <?php $searchComponentLoader->run(); ?>
    <hr/>

  <?php $maybePaginationData->peek($addPagination); ?>

    <hr/>

    <div class="d-flex flex-wrap justify-content-center">
      <?php
      if (empty($bookList->books())) {
        echo '<span class="p-2 h5">' . L::main_home_book_list_get_empty_result . '</span>';
      }
      foreach ($bookList->books() as $book) {
        $bookCardComponentLoader = new BookCardComponentLoader($book);
        $bookCardComponentLoader->run();
      } ?>
    </div>

    <hr/>

  <?php $maybePaginationData->peek($addPagination); ?>
</div>