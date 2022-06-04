<?php

use p1\configuration\Configuration;

require_once "configuration.php";

$bookController = Configuration::instance()->viewConfiguration()->controllers()->bookController();
$bookDetails = $bookController->getBookDetails();

$imgUri = (!is_null($bookDetails->book()->imageUri())) ? $bookDetails->book()->imageUri() : "/assets/book-icon.svg";
$publishedAt = new DateTimeImmutable('@' . $bookDetails->book()->publishedAt());
$tagsString = '';
foreach ($bookDetails->bookTags() as $bookTag) {
  $tagsString = ',' . $bookTag->code();
}
if (str_starts_with($tagsString, ',')) {
  $tagsString = substr($tagsString, 1);
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
        <img src="<?php echo $imgUri; ?>" class="img-thumbnail" alt="Book icon">
        <ul class="mx-1 list-group">
            <li class="list-group-item">
                <strong><?php echo L::main_books_book_piece_details_page_isbn_label; ?>
                    : </strong><?php echo $bookDetails->book()->isbn(); ?>
            </li>
            <li class="list-group-item">
                <strong><?php echo L::main_books_book_piece_details_page_state_label; ?>
                    : </strong><?php echo $bookDetails->book()->state()->displayName(); ?>
            </li>
            <li class="list-group-item">
                <strong><?php echo L::main_books_book_piece_details_page_published_at_label; ?>: </strong>
              <?php echo $publishedAt->format("Y-m-d"); ?>
            </li>
            <li class="list-group-item">
                <strong><?php echo L::main_books_book_piece_details_page_language_label; ?>
                    : </strong><?php echo $bookDetails->book()->language()->displayName(); ?>
            </li>
            <li class="list-group-item">
                <strong><?php echo L::main_books_book_piece_details_page_pages_label; ?>
                    : </strong><?php echo $bookDetails->book()->pages(); ?>
            </li>
            <li class="list-group-item">
                <strong><?php echo L::main_books_book_piece_details_page_publisher_label; ?>
                    : </strong><?php echo $bookDetails->publisher()->name(); ?>
            </li>
            <li class="list-group-item">
                <strong><?php echo L::main_books_book_piece_details_page_tags_label; ?>
                    : </strong><?php echo $tagsString; ?>
            </li>
        </ul>
    </div>
    <div>
        <p class="h3 mt-2"><?php echo L::main_books_book_piece_details_page_about_the_book_header; ?></p>
        <div>
          <?php echo $bookDetails->book()->description(); ?>
        </div>
    </div>

    <hr/>
</div>
