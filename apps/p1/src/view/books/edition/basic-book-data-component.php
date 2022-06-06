<?php

require_once "core/domain/book/book-state.php";
require_once "core/domain/language/language.php";

use p1\core\domain\book\BookState;
use p1\core\domain\language\Language;

$publishedAt = empty($bookDetails->book()->publishedAt())
  ? null
  : new DateTimeImmutable('@' . $bookDetails->book()->publishedAt());
$publishedAtString = empty($publishedAt) ? '' : $publishedAt->format('Y-m-d');

$bookTags = empty($bookDetails->bookTags())
  ? ''
  : join(",", array_map(function ($tag) {
    return $tag->code();
  }, $bookDetails->bookTags()));

$languages = Language::cases();
$bookStates = BookState::cases();
?>

<div class="container">
    <div class="row">
        <div class="col-md-2">
            <label for="editBookIdInput"><?php echo L::main_books_book_piece_edit_page_id_label; ?></label>
            <input type="text" class="form-control"
                   id="editBookIdInput"
                   placeholder="<?php echo L::main_books_book_piece_edit_page_id_label; ?>"
                   value="<?php echo $bookDetails->book()->id(); ?>"
                   disabled>
        </div>
        <div class="col-md-2">
            <label for="editBookIsbnInput"><?php echo L::main_books_book_piece_edit_page_isbn_label; ?></label>
            <input type="text" class="form-control"
                   id="editBookIsbnInput"
                   name="isbn"
                   placeholder="<?php echo L::main_books_book_piece_edit_page_isbn_label; ?>"
                   value="<?php echo $bookDetails->book()->isbn(); ?>">
        </div>
        <div class="col-md-2">
            <label for="editBookLanguageSelect">
              <?php echo L::main_books_book_piece_edit_page_state_label; ?>
            </label>
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
        </div>
        <div class="col-md-2">
            <label for="editBookLanguageSelect">
              <?php echo L::main_books_book_piece_edit_page_language_label; ?>
            </label>
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
        </div>
        <div class="col-md-2">
            <label for="editBookPublishedAtInput">
              <?php echo L::main_books_book_piece_edit_page_published_at_label; ?>
            </label>
            <input type="text" class="form-control"
                   id="editBookPublishedAtInput"
                   placeholder="<?php echo L::main_books_book_piece_edit_page_published_at_label; ?>">
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
        <div class="col-md-2">
            <label for="editBookPagesInput">
              <?php echo L::main_books_book_piece_edit_page_page_count_label; ?>
            </label>
            <input type="number" class="form-control"
                   id="editBookPagesInput"
                   name="pageCount"
                   min="0"
                   max="999999999999"
                   placeholder="<?php echo L::main_books_book_piece_edit_page_page_count_label; ?>"
                   value="<?php echo $bookDetails->book()->pages(); ?>">
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <span class="d-inline-block"
                  tabindex="0"
                  data-bs-toggle="popover"
                  data-bs-trigger="hover focus"
                  data-bs-content="<?php echo L::main_books_book_piece_edit_page_tags_popover_instruction; ?>">
                <label for="editBookTagsInput">
                  <?php echo L::main_books_book_piece_edit_page_tags_label; ?>
                  <i class="fa-solid fa-circle-info"></i>
                </label>
            </span>
            <textarea type="text" class="form-control"
                      id="editBookTagsInput"
                      name="tags"
                      placeholder="<?php echo L::main_books_book_piece_edit_page_tags_label; ?>"
            ><?php echo $bookTags; ?></textarea>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <label for="editBookTitleInput">
              <?php echo L::main_books_book_piece_edit_page_title_label; ?>
            </label>
            <textarea type="text" class="form-control h-auto"
                      id="editBookTitleInput"
                      name="title"
                      placeholder="<?php echo L::main_books_book_piece_edit_page_title_label; ?>"><?php echo $bookDetails->book()->title(); ?></textarea>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <label for="editBookNameIdInput">
              <?php echo L::main_books_book_piece_edit_page_name_id_label; ?>
            </label>
            <textarea type="text" class="form-control h-auto"
                      id="editBookNameIdInput"
                      name="nameId"
                      placeholder="<?php echo L::main_books_book_piece_edit_page_name_id_label; ?>"><?php echo $bookDetails->book()->nameId(); ?></textarea>
        </div>
    </div>
    <div class="row">
        <div class="col-md-1 align-self-center">
            <div class="dropdown">
                <button class="btn btn-outline-info dropdown-toggle" type="button"
                        id="editBookImagePreviewButton" data-bs-toggle="dropdown"
                        data-bs-auto-close="outside"
                        aria-expanded="false">
                  <?php echo L::main_books_book_piece_edit_page_image_uri_preview_btn_label; ?>
                </button>
                <div class="dropdown-menu min-vw-90" aria-labelledby="editBookImagePreviewButton">
                    <div class="d-flex flex-wrap p-2">
                        <div class="mx-2 my-2">
                            <p>
                              <?php echo L::main_books_book_piece_edit_page_image_uri_preview_new_label; ?>
                            </p>
                            <img id="editBookImagePreviewImage"
                                 class="img-fluid"
                                 src="<?php echo $bookDetails->book()->imageUri(); ?>" alt="new-image">
                        </div>
                        <div class="mx-2 my-2">
                            <p>
                              <?php echo L::main_books_book_piece_edit_page_image_uri_preview_old_label; ?>
                            </p>
                            <img id="editBookImagePreviewImageOld"
                                 class="img-fluid"
                                 src="<?php echo $bookDetails->book()->imageUri(); ?>" alt="old-image">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-11">
            <div class="d-inline-block"
                 tabindex="0"
                 data-bs-toggle="popover"
                 data-bs-trigger="hover focus"
                 data-bs-content="<?php echo L::main_books_book_piece_edit_page_image_uri_popover_instruction; ?>"
            >
                <label for="editBookImageUriInput">
                  <?php echo L::main_books_book_piece_edit_page_image_uri_label; ?>
                    <i class="fa-solid fa-circle-info"></i>
                </label>
            </div>
            <textarea type="text" class="form-control h-auto"
                      id="editBookImageUriInput"
                      name="imageUri"
                      placeholder="<?php echo L::main_books_book_piece_edit_page_image_uri_label; ?>"><?php echo $bookDetails->book()->imageUri(); ?></textarea>
        </div>
        <script>
            let editBookImagePreviewComponent = new ImagePreviewComponent('editBookImageUriInput', 'editBookImagePreviewImage');
        </script>
    </div>
</div>