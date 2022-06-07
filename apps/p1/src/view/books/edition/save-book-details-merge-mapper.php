<?php

namespace p1\view\books\edition;

require_once "core/function/function.php";
require_once "core/function/option.php";
require_once "core/domain/book/book-details.php";
require_once "core/domain/book/book-state.php";
require_once "core/domain/book/edit/save-book-command.php";
require_once "core/domain/language/language.php";

use DateTimeImmutable;
use p1\core\domain\book\BookDetails;
use p1\core\domain\book\BookState;
use p1\core\domain\book\BookTag;
use p1\core\domain\book\edit\SaveBookCommand;
use p1\core\domain\language\Language;
use p1\core\function\FunctionUtils;
use p1\core\function\Option;

class SaveBookDetailsMergeMapper {

  private TagsParser $tagsParser;

  public function __construct(TagsParser $tagsParser) {
    $this->tagsParser = $tagsParser;
  }

  public function mergeBookDetailsWithCommand(BookDetails $bookDetails, SaveBookCommand $command): BookDetails {
    $mergedBook = $bookDetails->book()
      ->withIsbn($command->isbn())
      ->withState($command->state())
      ->withLanguage($command->language())
      ->withPublishedAt($this->dateTimeImmutableToUnixTime($command->publishedAt()))
      ->withPages($command->pageCount())
      ->withTitle($command->title())
      ->withImageUri($command->imageUri())
      ->withDescription($command->description());

    $bookTags = [];
    foreach ($command->tags() as $tag) {
      $bookTags[] = new BookTag($tag);
    }

    $details = $bookDetails->withBook($mergedBook)
      ->withTags($bookTags);
//    var_dump($details);
    return $details;
  }

  public function mergeBookDetailsWithRequest(BookDetails $bookDetails, array $post): BookDetails {
    $request = new PostRequestWrapper($post);
    $book = $bookDetails->book();
    $mergedBook = $book
      ->withIsbn($request->get('isbn')->orElseGet(FunctionUtils::supplierOfClosure(fn() => $book->isbn())))
      ->withState($request->get('state')
        ->flatMap(FunctionUtils::function2OfClosure(fn($stateString) => BookState::of($stateString)))
        ->orElseGet(FunctionUtils::supplierOfClosure(fn() => $book->state())))
      ->withLanguage($request->get('language')
        ->flatMap(FunctionUtils::function2OfClosure(fn($langString) => Language::of($langString)))
        ->orElseGet(FunctionUtils::supplierOfClosure(fn() => $book->language())))
      ->withPublishedAt($request->get('publishedAt')->fold(
        FunctionUtils::supplierOfClosure(fn() => $book->publishedAt()),
        FunctionUtils::function2OfClosure(
          fn($dateString) => $this->stringDateToUnixTime(
            $dateString, $book->publishedAt()
          )
        )
      ))
      ->withPages($request->get('pageCount')->orElseGet(FunctionUtils::supplierOfClosure(fn() => $book->pages())))
      ->withTitle($request->get('title')->orElseGet(FunctionUtils::supplierOfClosure(fn() => $book->title())))
      ->withImageUri($request->get('imageUri')->orElseGet(FunctionUtils::supplierOfClosure(fn() => $book->imageUri())))
      ->withDescription($request->get('description')->orElseGet(FunctionUtils::supplierOfClosure(fn() => $book->description())));

    $bookTags = $request->get('tags')
      ->map(FunctionUtils::function2OfClosure(
        fn($tags) => $this->tagsParser->parse($tags)
          ->fold(
            FunctionUtils::function2OfClosure(
              fn($fail) => $bookDetails->bookTags()
            ),
            FunctionUtils::function2OfClosure(function ($parsedTags) {
              $parsedBookTags = [];
              foreach ($parsedTags as $parsedTag) {
                $parsedBookTags[] = new BookTag($parsedTag);
              }
              return $parsedBookTags;
            })
          )
      ))
      ->orElseGet(FunctionUtils::supplierOfClosure(
        fn() => $bookDetails->bookTags()
      ));

    $details = $bookDetails->withBook($mergedBook)
      ->withTags($bookTags);
//    var_dump($details);
    return $details;
  }

  private function dateTimeImmutableToUnixTime(?DateTimeImmutable $dateTimeImmutable): ?int {
    return empty($dateTimeImmutable)
      ? null
      : $dateTimeImmutable->getTimestamp();
  }

  private function stringDateToUnixTime(?string $stringDate, ?int $fallbackDate): ?int {
    if (empty($stringDate)) {
      return null;
    }
    $date = DateTimeImmutable::createFromFormat('Y-m-d', $stringDate);
    return $date
      ? $date->getTimestamp()
      : $fallbackDate;
  }
}

class PostRequestWrapper {
  private array $post;

  public function __construct(array $post) {
    $this->post = $post;
  }

  public function get(string $key): Option {
    return array_key_exists($key, $this->post)
      ? Option::of($this->post[$key])
      : Option::none();
  }
}