<?php

namespace p1\view\books\edition;

require_once "core/function/either.php";
require_once "core/function/function.php";
require_once "core/domain/failure.php";
require_once "core/domain/book/edit/save-book-command.php";

require_once "view/books/edition/tags-parser.php";
require_once "view/security/html-sanitizer.php";

use DateTimeImmutable;
use L;
use p1\core\domain\book\BookState;
use p1\core\domain\book\edit\SaveBookCommand;
use p1\core\domain\book\edit\SaveBookCommandBuilderDescription;
use p1\core\domain\book\edit\SaveBookCommandBuilderId;
use p1\core\domain\book\edit\SaveBookCommandBuilderImageUri;
use p1\core\domain\book\edit\SaveBookCommandBuilderIsbn;
use p1\core\domain\book\edit\SaveBookCommandBuilderLanguage;
use p1\core\domain\book\edit\SaveBookCommandBuilderNameId;
use p1\core\domain\book\edit\SaveBookCommandBuilderPageCount;
use p1\core\domain\book\edit\SaveBookCommandBuilderPublishedAt;
use p1\core\domain\book\edit\SaveBookCommandBuilderState;
use p1\core\domain\book\edit\SaveBookCommandBuilderTags;
use p1\core\domain\book\edit\SaveBookCommandBuilderTitle;
use p1\core\domain\Failure;
use p1\core\domain\language\Language;
use p1\core\function\Either;
use p1\core\function\FunctionUtils;
use p1\session\UserContext;
use p1\view\security\HtmlSanitizer;

class SaveBookDetailsRequestToCommandMapper {
  private const ISBN_REGEX = "/^[0-9][0-9-]*(?<!-)$/";
  private const POSITIVE_INTEGER_REGEX = "/^[0-9]+$/";
  private const NAME_ID_REGEX = "/^[a-zA-Z0-9][a-zA-Z0-9-]*(?<![-])$/";

  private HtmlSanitizer $htmlSanitizer;
  private TagsParser $tagsParser;

  public function __construct(HtmlSanitizer $htmlSanitizer,
                              TagsParser    $tagsParser) {
    $this->htmlSanitizer = $htmlSanitizer;
    $this->tagsParser = $tagsParser;
  }

  public function toCommand(array $post, UserContext $userContext): Either {
    return $this->parseId($post)
      ->flatMapRight(FunctionUtils::function2OfClosure(fn($builder) => $this->parseIsbn($builder, $post)))
      ->flatMapRight(FunctionUtils::function2OfClosure(fn($builder) => $this->parseState($builder, $post)))
      ->flatMapRight(FunctionUtils::function2OfClosure(fn($builder) => $this->parseLanguage($builder, $post)))
      ->flatMapRight(FunctionUtils::function2OfClosure(fn($builder) => $this->parsePublishedAt($builder, $post)))
      ->flatMapRight(FunctionUtils::function2OfClosure(fn($builder) => $this->parsePageCount($builder, $post)))
      ->flatMapRight(FunctionUtils::function2OfClosure(fn($builder) => $this->parseTags($builder, $post)))
      ->flatMapRight(FunctionUtils::function2OfClosure(fn($builder) => $this->parseTitle($builder, $post)))
      ->flatMapRight(FunctionUtils::function2OfClosure(fn($builder) => $this->parseNameId($builder, $post)))
      ->flatMapRight(FunctionUtils::function2OfClosure(fn($builder) => $this->parseImageUri($builder, $post)))
      ->flatMapRight(FunctionUtils::function2OfClosure(fn($builder) => $this->parseDescription($builder, $post)))
      ->flatMapRight(FunctionUtils::function2OfClosure(fn($builder) => $this->parseVersion($builder, $post)))
      ->mapRight(FunctionUtils::function2OfClosure(fn($builder) => $builder->updatedBy($userContext->userEmail())));
  }

  private function parseId(array $post): Either {
    if (!array_key_exists('id', $post)) {
      return Either::ofRight(SaveBookCommand::builder()->noId());
    }
    $maybeId = $post['id'];
    if (empty($maybeId)) {
      return Either::ofRight(SaveBookCommand::builder()->noId());
    } else if (!is_numeric($maybeId)) {
      return Either::ofLeft(SaveBookDetailsRequestFailure::ofSaveFailure(
        htmlentities(str_replace("{id}", $maybeId, L::main_errors_save_book_request_id_is_not_numeric)),
        $post
      ));
    } else if ($maybeId < 0) {
      return Either::ofLeft(SaveBookDetailsRequestFailure::ofSaveFailure(
        htmlentities(str_replace("{id}", $maybeId, L::main_errors_save_book_request_id_is_negative)),
        $post
      ));
    } else {
      return Either::ofRight(SaveBookCommand::builder()->id($maybeId));
    }
  }

  private function parseIsbn(SaveBookCommandBuilderId $builder, array $post): Either {
    if (!array_key_exists('isbn', $post)) {
      return Either::ofLeft(SaveBookDetailsRequestFailure::ofSaveFailure(L::main_errors_save_book_request_isbn_is_empty, $post));
    }
    $maybeIsbn = $post['isbn'];
    if (empty($maybeIsbn)) {
      return Either::ofLeft(SaveBookDetailsRequestFailure::ofSaveFailure(L::main_errors_save_book_request_isbn_is_empty, $post));
    } else if (preg_match(self::ISBN_REGEX, $maybeIsbn)) {
      return Either::ofRight($builder->isbn($maybeIsbn));
    } else {
      return Either::ofLeft(SaveBookDetailsRequestFailure::ofSaveFailure(
        htmlentities(str_replace("{isbn}", $maybeIsbn, L::main_errors_save_book_request_isbn_invalid_format)),
        $post
      ));
    }
  }

  private function parseState(SaveBookCommandBuilderIsbn $builder, array $post): Either {
    if (!array_key_exists('state', $post)) {
      return Either::ofLeft(SaveBookDetailsRequestFailure::ofSaveFailure(L::main_errors_save_book_request_state_empty, $post));
    }
    $maybeState = $post['state'];
    if (empty($maybeState)) {
      return Either::ofLeft(SaveBookDetailsRequestFailure::ofSaveFailure(L::main_errors_save_book_request_state_empty, $post));
    } else {
      return BookState::of($maybeState)
        ->fold(
          FunctionUtils::supplierOfClosure(fn() => Either::ofLeft(SaveBookDetailsRequestFailure::ofSaveFailure(
            htmlentities(str_replace("{state}", $maybeState, L::main_errors_save_book_request_state_invalid)),
            $post
          ))),
          FunctionUtils::function2OfClosure(fn($bookState) => Either::ofRight($builder->state($bookState)))
        );
    }
  }

  private function parseLanguage(SaveBookCommandBuilderState $builder, array $post): Either {
    if (!array_key_exists('language', $post)) {
      return Either::ofRight($builder->noLanguage());
    }
    $maybeLanguage = $post['language'];
    if (empty($maybeLanguage)) {
      return Either::ofRight($builder->noLanguage());
    } else {
      return Language::of($maybeLanguage)
        ->fold(FunctionUtils::supplierOfClosure(fn() => Either::ofLeft(SaveBookDetailsRequestFailure::ofSaveFailure(
          htmlentities(str_replace("{language}", $maybeLanguage, L::main_errors_save_book_request_language_invalid)),
          $post
        ))),
          FunctionUtils::function2OfClosure(fn($language) => Either::ofRight(
            $language === Language::unknown
              ? $builder->noLanguage()
              : $builder->language($language)
          ))
        );
    }
  }

  private function parsePublishedAt(SaveBookCommandBuilderLanguage $builder, array $post): Either {
    if (!array_key_exists('publishedAt', $post)) {
      return Either::ofRight($builder->noPublishedAt());
    }
    $maybePublishedAt = $post['publishedAt'];
    if (empty($maybePublishedAt)) {
      return Either::ofRight($builder->noPublishedAt());
    }
    $publishedAtDate = DateTimeImmutable::createFromFormat('Y-m-d', $maybePublishedAt);
    if (!$publishedAtDate) {
      return Either::ofLeft(SaveBookDetailsRequestFailure::ofSaveFailure(
        htmlentities(str_replace("{date}", $publishedAtDate, L::main_errors_save_book_request_published_at_invalid)),
        $post
      ));
    } else {
      return Either::ofRight($builder->publishedAt($publishedAtDate));
    }
  }

  private function parsePageCount(SaveBookCommandBuilderPublishedAt $builder, array $post): Either {
    if (!array_key_exists('pageCount', $post)) {
      return Either::ofRight($builder->noPageCount());
    }
    $maybePageCount = $post['pageCount'];
    if (empty($maybePageCount)) {
      return Either::ofRight($builder->noPageCount());
    }
    if (!preg_match(self::POSITIVE_INTEGER_REGEX, $maybePageCount)) {
      return Either::ofLeft(SaveBookDetailsRequestFailure::ofSaveFailure(
        htmlentities(str_replace("{pageCount}", $maybePageCount, L::main_errors_save_book_request_page_count_format_invalid)),
        $post
      ));
    }
    if ($maybePageCount < 0) {
      return Either::ofLeft(SaveBookDetailsRequestFailure::ofSaveFailure(
        htmlentities(str_replace("{pageCount}", $maybePageCount, L::main_errors_save_book_request_page_count_negative)),
        $post
      ));
    }
    return Either::ofRight($builder->pageCount($maybePageCount));
  }

  private function parseTags(SaveBookCommandBuilderPageCount $builder, array $post): Either {
    if (!array_key_exists('tags', $post)) {
      return Either::ofRight($builder->noTags());
    }
    $maybeTags = $post['tags'];
    return $this->tagsParser->parse($maybeTags)
      ->mapLeft(FunctionUtils::function2OfClosure(
        fn($error) => SaveBookDetailsRequestFailure::ofSaveFailure(
          htmlentities(str_replace("{tags}", $maybeTags, L::main_errors_save_book_request_invalid_tags_format_msg)),
          $post
        )
      ))
      ->mapRight(FunctionUtils::function2OfClosure(
        fn($tags) => $builder->tags($tags)
      ));
  }

  private function parseTitle(SaveBookCommandBuilderTags $builder, array $post): Either {
    if (!array_key_exists('title', $post)) {
      return Either::ofLeft(SaveBookDetailsRequestFailure::ofSaveFailure(L::main_errors_save_book_request_title_empty, $post));
    }
    $maybeTitle = $post['title'];
    if (empty($maybeTitle)) {
      return Either::ofLeft(SaveBookDetailsRequestFailure::ofSaveFailure(L::main_errors_save_book_request_title_empty, $post));
    } else {
      return Either::ofRight($builder->title(htmlentities(trim($maybeTitle))));
    }
  }

  private function parseNameId(SaveBookCommandBuilderTitle $builder, array $post): Either {
    if (!array_key_exists('nameId', $post)) {
      return Either::ofLeft(SaveBookDetailsRequestFailure::ofSaveFailure(L::main_errors_save_book_request_name_id_empty, $post));
    }
    $maybeNameId = $post['nameId'];
    if (empty($maybeNameId)) {
      return Either::ofLeft(SaveBookDetailsRequestFailure::ofSaveFailure(L::main_errors_save_book_request_name_id_empty, $post));
    }
    if (!preg_match(self::NAME_ID_REGEX, $maybeNameId)) {
      return Either::ofLeft(SaveBookDetailsRequestFailure::ofSaveFailure(
        htmlentities(str_replace("{nameId}", $maybeNameId, L::main_errors_save_book_request_name_id_invalid_format)),
        $post
      ));
    }
    return Either::ofRight($builder->nameId(mb_strtolower($maybeNameId, "UTF-8")));
  }

  private function parseImageUri(SaveBookCommandBuilderNameId $builder, array $post): Either {
    if (!array_key_exists('imageUri', $post)) {
      return Either::ofRight($builder->noImageUri());
    }
    $maybeImageUri = $post['imageUri'];
    if (empty($maybeImageUri)) {
      return Either::ofRight($builder->noImageUri());
    }
    $uriForTest = str_starts_with($maybeImageUri, '/')
      ? 'https://localhost.com' . $maybeImageUri
      : $maybeImageUri;

    if (filter_var($uriForTest, FILTER_VALIDATE_URL)) {
      return Either::ofRight($builder->imageUri($maybeImageUri));
    } else {
      return Either::ofLeft(SaveBookDetailsRequestFailure::ofSaveFailure(
        htmlentities(str_replace("{imageUri}", $maybeImageUri, L::main_errors_save_book_request_image_uri_invalid)),
        $post
      ));
    }
  }

  private function parseDescription(SaveBookCommandBuilderImageUri $builder, array $post): Either {
    if (!array_key_exists('description', $post)) {
      return Either::ofRight($builder->noDescription());
    }
    $maybeDescription = $post['description'];
    if (empty($maybeDescription)) {
      return Either::ofRight($builder->noDescription());
    }
    $description = $this->htmlSanitizer->sanitizeConfigDefault($maybeDescription);
    return Either::ofRight($builder->description($description));
  }

  private function parseVersion(SaveBookCommandBuilderDescription $builder, array $post): Either {
    if (!array_key_exists('version', $post)) {
      error_log("SaveBookDetailsRequestToCommandMapper.parseVersion() Version is not submitted");
      return Either::ofRight($builder->version(1));
    }
    $maybeVersion = $post['version'];
    if (empty($maybeVersion)) {
      error_log("SaveBookDetailsRequestToCommandMapper.parseVersion() Version is empty");
      return Either::ofRight($builder->version(1));
    }
    if (!preg_match(self::POSITIVE_INTEGER_REGEX, $maybeVersion)) {
      error_log("SaveBookDetailsRequestToCommandMapper.parseVersion() Version is in invalid format: " . $maybeVersion);
      return Either::ofLeft(SaveBookDetailsRequestFailure::ofSaveFailure(L::main_errors_global_global_error_message, $post));
    }
    if ($maybeVersion < 0) {
      error_log("SaveBookDetailsRequestToCommandMapper.parseVersion() Version is less than 0:  " . $maybeVersion);
      return Either::ofLeft(SaveBookDetailsRequestFailure::ofSaveFailure(L::main_errors_global_global_error_message, $post));
    }
    return Either::ofRight($builder->version($maybeVersion));
  }
}

class SaveBookDetailsRequestFailure extends Failure {
  private array $request;

  public function __construct(string $message, array $post) {
    parent::__construct($message);
    $this->request = $post;
  }

  public function request(): array {
    return $this->request;
  }

  public static function ofSaveFailure(string $message, array $post): SaveBookDetailsRequestFailure {
    return new SaveBookDetailsRequestFailure($message, $post);
  }
}