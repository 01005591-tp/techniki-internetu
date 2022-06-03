<?php

require_once "core/function/function.php";
require_once "view/select/multiselect.php";

use p1\core\function\Runnable;
use p1\view\select\Multiselect;
use p1\view\select\SelectOption;

$multiselectName = 'bookSearchTags';
$loadOptionEntryRunnable = new class($multiselectName, $availableTags) implements Runnable {
    private string $multiselectName;
    private array $availableTags;

    public function __construct(string $multiselectName,
                                array  $availableTags)
    {
        $this->multiselectName = $multiselectName;
        $this->availableTags = $availableTags;
    }

    function run(): void
    {
        $tagOptions = [];
        if (!empty($this->availableTags)) {
            foreach ($this->availableTags as $tag) {
                $tagOptions[] = new SelectOption(
                    $tag->code(),
                    $tag->code(),
                    $tag->code()
                );
            }
        }
        $multiselect = new Multiselect(
            $this->multiselectName,
            L::main_home_book_search_book_tags_input_label,
            L::main_home_book_search_book_tags_input_label,
            $tagOptions
        );
        require "view/select/multiple-select-component.php";
    }
};
?>
<form name="search-books-criteria-form">
    <div class="container">
        <div class="d-flex flex-wrap">
            <div class="form-floating mb-2 mx-2">
                <input type="text"
                       id="searchBookTitleInput"
                       name="searchBookTitleInput"
                       class="form-control"
                       placeholder="<?php

                       echo L::main_home_book_search_book_title_input_label; ?>"
                />
                <label for="searchBookTitleInput">
                    <?php echo L::main_home_book_search_book_title_input_label; ?>
                </label>
            </div>
            <div class="form-floating mb-2 mx-2">
                <input type="text"
                       id="searchBookDescriptionInput"
                       name="searchBookDescriptionInput"
                       class="form-control"
                       placeholder="<?php echo L::main_home_book_search_book_description_input_label; ?>"
                />
                <label for="searchBookDescriptionInput">
                    <?php echo L::main_home_book_search_book_description_input_label; ?>
                </label>
            </div>
            <div class="form-floating mb-2 mx-2">
                <input type="text"
                       id="searchBookAuthorInput"
                       name="searchBookAuthorInput"
                       class="form-control"
                       placeholder="<?php echo L::main_home_book_search_book_author_input_label; ?>"
                />
                <label for="searchBookAuthorInput">
                    <?php echo L::main_home_book_search_book_author_input_label; ?>
                </label>
            </div>
            <div class="form-floating mb-2 mx-2">
                <?php $loadOptionEntryRunnable->run(); ?>
            </div>
            <div class="form-floating mb-2 mx-2">
                <input type="text"
                       id="searchBookIsbnInput"
                       name="searchBookIsbnInput"
                       class="form-control"
                       placeholder="<?php echo L::main_home_book_search_book_isbn_input_label; ?>"
                />
                <label for="searchBookIsbnInput">
                    <?php echo L::main_home_book_search_book_isbn_input_label; ?>
                </label>
            </div>
        </div>
        <button type="submit" name="book-search-search-btn"
                class="btn btn-primary mx-2 text-nowrap">
            <i class="fa-solid fa-magnifying-glass"></i>
            <?php echo L::main_home_book_search_search_btn_label; ?>
        </button>
        <button type="button" name="book-search-clear-btn text-nowrap"
                class="btn btn-outline-danger mx-2"
                onclick="inputUtils.clearInputs([
                        'searchBookTitleInput',
                        'searchBookDescriptionInput',
                        'searchBookAuthorInput',
                        'searchBookIsbnInput',
                        '<?php echo $multiselectName . '_displayInput'; ?>',
                        '<?php echo $multiselectName . '_valueHolderInput'; ?>'
                        ]);">
            <i class="fa-solid fa-xmark"></i>
            <?php echo L::main_home_book_search_clear_btn_label; ?>
        </button>
    </div>
</form>