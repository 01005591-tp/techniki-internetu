<?php

use p1\configuration\Configuration;
use p1\core\domain\book\Author;
use p1\core\domain\book\BookPieceState;
use p1\core\domain\user\auth\Roles;
use p1\core\function\Runnable;

require_once "configuration.php";
require_once "core/domain/book/author.php";
require_once "core/domain/book/book-piece.php";
require_once "core/domain/user/auth/roles.php";
require_once "core/function/function.php";

$bookController = Configuration::instance()->viewConfiguration()->controllers()->bookController();
$userContext = Configuration::instance()->userContext();
$bookDetails = $bookController->getBookDetails();

$imgUri = (!is_null($bookDetails->book()->imageUri())) ? $bookDetails->book()->imageUri() : "/assets/book-icon.svg";
$publishedAt = new DateTimeImmutable('@' . $bookDetails->book()->publishedAt());

$tagsString = resolveBookTagsString($bookDetails->bookTags());

function resolveBookTagsString($bookTags): string {
  if (empty($bookTags)) {
    return '';
  }
  $bookTagCodes = array_map(function ($tag) {
    return $tag->code();
  }, $bookTags);
  return join(',', $bookTagCodes);
}

class BookDetailsComponentLoader implements Runnable {
  private Author $author;

  public function __construct(Author $author) {
    $this->author = $author;
  }

  function run(): void {
    $author = $this->author;
    require "view/books/book-author-details-component.php";
  }
}

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
    <div class="d-flex">
        <div>
            <img src="<?php echo $imgUri; ?>" class="img-thumbnail" alt="Book icon">
        </div>
        <div>
            <ul class="mx-1 list-group">
                <li class="list-group-item">
                    <strong><?php echo L::main_books_book_piece_details_page_isbn_label; ?></strong>:
                  <?php echo $bookDetails->book()->isbn(); ?>
                </li>
                <li class="list-group-item">
                    <strong><?php echo L::main_books_book_piece_details_page_state_label; ?></strong>:
                  <?php echo $bookDetails->book()->state()->displayName(); ?>
                </li>
                <li class="list-group-item">
                    <strong><?php echo L::main_books_book_piece_details_page_published_at_label; ?></strong>:
                  <?php echo $publishedAt->format("Y-m-d"); ?>
                </li>
                <li class="list-group-item">
                    <strong><?php echo L::main_books_book_piece_details_page_language_label; ?></strong>:
                  <?php echo $bookDetails->book()->language()->displayName(); ?>
                </li>
                <li class="list-group-item">
                    <strong><?php echo L::main_books_book_piece_details_page_pages_label; ?></strong>:
                  <?php echo $bookDetails->book()->pages(); ?>
                </li>
                <li class="list-group-item">
                    <strong><?php echo L::main_books_book_piece_details_page_publisher_label; ?></strong>:
                  <?php echo $bookDetails->publisher()->name(); ?>
                </li>
                <li class="list-group-item">
                    <strong><?php echo L::main_books_book_piece_details_page_tags_label; ?></strong>:
                  <?php echo $tagsString; ?>
                </li>
            </ul>
        </div>
        <div>
            <p class="h5 mx-1">
              <?php echo L::main_books_book_piece_details_page_pieces_available_header; ?>:
            </p>
            <ul class="mx-1 list-group-flush">
                <li class="list-group-item">
                    <strong><?php echo L::main_books_book_piece_state_display_name_available; ?></strong>:
                  <?php echo $bookDetails->bookPieces()->stateCounts()[BookPieceState::AVAILABLE->name]; ?>
                </li>
                <li class="list-group-item">
                    <strong><?php echo L::main_books_book_piece_state_display_name_booked; ?></strong>:
                  <?php echo $bookDetails->bookPieces()->stateCounts()[BookPieceState::BOOKED->name]; ?>
                </li>
                <li class="list-group-item">
                    <strong><?php echo L::main_books_book_piece_state_display_name_rented; ?></strong>:
                  <?php echo $bookDetails->bookPieces()->stateCounts()[BookPieceState::RENTED->name]; ?>
                </li>
              <?php if ($userContext->hasAnyRole(Roles::EMPLOYEE->name)) {
                echo '
                <li class="list-group-item">
                <strong>' . L::main_books_book_piece_state_display_name_unavailable . '</strong>: 
                ' . $bookDetails->bookPieces()->stateCounts()[BookPieceState::UNAVAILABLE->name] . '
                </li>
                ';
              } ?>
            </ul>
        </div>
        <div>
          <?php
          if ($userContext->hasAnyRole(Roles::EMPLOYEE->name)) {
            echo '
            <a class="mx-2 align-self-end btn btn-outline-warning"
               href="/books/' . $bookDetails->book()->nameId() . '/edition"
               role="button" aria-pressed="true">
                ' . L::main_books_book_piece_details_page_edit_btn_label . '
            </a>
            ';
          }
          ?>
        </div>
    </div>
    <div>
        <p class="h3 mt-2"><?php echo L::main_books_book_piece_details_page_about_the_book_header; ?></p>
        <div>
          <?php echo $bookDetails->book()->description(); ?>
        </div>
    </div>

    <hr/>

    <p class="h3 mt-2"><?php echo L::main_books_book_piece_details_page_about_the_authors_header; ?></p>
    <div>
      <?php
      foreach ($bookDetails->authors() as $author) {
        $loader = new BookDetailsComponentLoader($author);
        $loader->run();
      }
      ?>
    </div>
</div>
