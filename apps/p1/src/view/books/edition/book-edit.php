<?php

require_once "configuration.php";
require_once "core/function/function.php";

use p1\configuration\Configuration;
use p1\core\function\Runnable;

$bookEditController = Configuration::instance()->viewConfiguration()->controllers()->bookEditController();
$bookDetails = $bookEditController->loadBookDetails();


class EnumBasedOptionLoader implements Runnable {
  private $enumBasedItem;
  private bool $selected;

  public function __construct($enumBasedValue, bool $selected) {
    $this->enumBasedItem = $enumBasedValue;
    $this->selected = $selected;
  }

  function run(): void {
    $enumBasedItem = $this->enumBasedItem;
    $selected = $this->selected;
    require "view/books/edition/enum-based-item-select-option.php";
  }
}

?>

<!-- Edit Book JS -->
<script src="/assets/scripts/edit-book.js"></script>

<form method="post" action="/books/<?php echo $bookDetails->book()->nameId(); ?>/edition">
    <div class="shadow-lg p-2 mb-5 bg-body rounded">
        <div class="d-flex justify-content-center">
    <span class="p-2 h2">
        <?php echo L::main_books_book_piece_edit_page_book_details_section_header; ?>
    </span>
        </div>
        <hr/>
      <?php require "view/books/edition/basic-book-data-component.php"; ?>
        <hr/>
        <hr/>
      <?php require "view/books/edition/book-edit-buttons-component.php"; ?>
    </div>
</form>