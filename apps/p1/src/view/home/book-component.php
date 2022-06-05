<?php

use p1\core\domain\book\BookState;
use p1\core\domain\language\Language;
use p1\core\function\Function2;

require_once "core/domain/book/book-state.php";
require_once "core/domain/language/language.php";
require_once "core/function/function.php";

$imgUri = (!is_null($book->imageUri())) ? $book->imageUri() : "/assets/book-icon.svg";
$bookStateDisplayName = BookState::of($book->state())
  ->map(new class implements Function2 {
    function apply($value) {
      return $value->displayName();
    }
  })
  ->orElse('');
$languageDisplayName = Language::ofOrUnknown($book->language())->displayName();
?>

<div id="book-list-cards-container" class="p-2">
    <div class="card">
        <a href="/books/<?php echo $book->nameId(); ?>">
            <img src="<?php echo $imgUri; ?>" class="card-img-top" alt="Book icon">
        </a>
        <div class="card-body">
            <h5 class="card-title"><?php echo $book->title(); ?></h5>
            <p class="card-text">
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <strong>ISBN:</strong> <?php echo $book->isbn(); ?>
                </li>
                <li class="list-group-item">
                    <strong><?php echo L::main_home_book_list_entry_language; ?>:
                    </strong> <?php echo $languageDisplayName; ?>
                </li>
                <li class="list-group-item">
                    <strong><?php echo L::main_home_book_list_entry_state; ?>
                        :</strong> <?php echo $bookStateDisplayName; ?>
                </li>
            </ul>
            </p>
            <p class="card-text"><?php echo $book->description(); ?></p>
        </div>
    </div>
</div>