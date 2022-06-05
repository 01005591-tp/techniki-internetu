<?php

require_once "configuration.php";
require_once "core/function/function.php";
require_once "core/domain/book/book-state.php";
require_once "core/domain/language/language.php";

use p1\configuration\Configuration;
use p1\core\domain\book\BookState;
use p1\core\domain\language\Language;
use p1\core\function\Runnable;

$bookEditController = Configuration::instance()->viewConfiguration()->controllers()->bookEditController();
$bookDetails = $bookEditController->loadBookDetails();

$publishedAt = empty($bookDetails->book()->publishedAt())
  ? null
  : new DateTimeImmutable('@' . $bookDetails->book()->publishedAt());
$publishedAtString = empty($publishedAt) ? '' : $publishedAt->format('Y-m-d');

$languages = Language::cases();
$bookStates = BookState::cases();

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

<div class="shadow-lg p-2 mb-5 bg-body rounded">
    <div class="d-flex flex-wrap mb-2">

    </div>
    <div class="d-flex flex-wrap mb-2">
        <div class="form-floating mx-1 flex-grow-1">
            <textarea type="text" class="form-control h-auto"
                      id="editBookTitleInput"
                      name="title"
                      placeholder="<?php echo L::main_books_book_piece_edit_page_title_label; ?>"><?php echo $bookDetails->book()->title(); ?></textarea>
            <label for="editBookTitleInput">
              <?php echo L::main_books_book_piece_edit_page_title_label; ?>
            </label>
        </div>
        <div class="form-floating mx-1">
            <input type="text" class="form-control"
                   id="editBookIsbnInput"
                   name="isbn"
                   placeholder="<?php echo L::main_books_book_piece_edit_page_isbn_label; ?>"
                   value="<?php echo $bookDetails->book()->isbn(); ?>">
            <label for="editBookIsbnInput"><?php echo L::main_books_book_piece_edit_page_isbn_label; ?></label>
        </div>
        <div class="form-floating mx-1">
            <select id="editBookLanguageSelect"
                    name="language"
                    class="form-select form-control"
                    aria-label="<?php echo L::main_books_book_piece_edit_page_language_label; ?>">
              <?php
              foreach ($languages as $bookState) {
                $selected = $bookDetails->book()->language()->name === $bookState->name;
                $loader = new EnumBasedOptionLoader($bookState, $selected);
                $loader->run();
              }
              ?>
            </select>
            <label for="editBookLanguageSelect">
              <?php echo L::main_books_book_piece_edit_page_language_label; ?>
            </label>
        </div>
        <div class="form-floating mx-1">
            <input type="text" class="form-control"
                   id="editBookPublishedAtInput"
                   placeholder="<?php echo L::main_books_book_piece_edit_page_published_at_label; ?>">
            <label for="editBookPublishedAtInput">
              <?php echo L::main_books_book_piece_edit_page_published_at_label; ?>
            </label>
            <input type="text"
                   id="editBookPublishedAtHiddenInput"
                   hidden
                   aria-label="editBookPublishedAtHiddenInput"
                   name="publishedAt"
                   value="<?php echo $publishedAtString; ?>">
        </div>
        <script>
            let datePickerComponent = new DatePickerComponent("editBookPublishedAtInput", "editBookPublishedAtHiddenInput");
        </script>
        <div class="form-floating mx-1">
            <input type="number" class="form-control"
                   id="editBookPagesInput"
                   name="pageCount"
                   min="0"
                   max="999999999999"
                   placeholder="<?php echo L::main_books_book_piece_edit_page_page_count_label; ?>"
                   value="<?php echo $bookDetails->book()->pages(); ?>">
            <label for="editBookPagesInput">
              <?php echo L::main_books_book_piece_edit_page_page_count_label; ?>
            </label>
        </div>
    </div>
    <div class="d-flex flex-wrap mb-2">
        <div class="form-floating mx-1 flex-grow-1">
            <textarea type="text" class="form-control h-auto"
                      id="editBookNameIdInput"
                      name="nameId"
                      placeholder="<?php echo L::main_books_book_piece_edit_page_name_id_label; ?>"><?php echo $bookDetails->book()->nameId(); ?></textarea>
            <label for="editBookNameIdInput">
              <?php echo L::main_books_book_piece_edit_page_name_id_label; ?>
            </label>
        </div>
        <div class="form-floating mx1">
            <select id="editBookLanguageSelect"
                    name="language"
                    class="form-select form-control"
                    aria-label="<?php echo L::main_books_book_piece_edit_page_state_label; ?>">
              <?php
              foreach ($bookStates as $bookState) {
                $selected = $bookDetails->book()->state()->name === $bookState->name;
                $loader = new EnumBasedOptionLoader($bookState, $selected);
                $loader->run();
              }
              ?>
            </select>
            <label for="editBookLanguageSelect">
              <?php echo L::main_books_book_piece_edit_page_state_label; ?>
            </label>
        </div>
    </div>
</div>