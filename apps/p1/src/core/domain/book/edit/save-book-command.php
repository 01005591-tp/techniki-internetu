<?php

namespace p1\core\domain\book\edit;

require_once "core/domain/book/book-state.php";
require_once "core/domain/language/language.php";

use DateTimeImmutable;
use p1\core\domain\book\BookState;
use p1\core\domain\language\Language;

class SaveBookCommand {
  private ?string $id;
  private ?string $isbn;
  private BookState $state;
  private ?Language $language;
  private ?DateTimeImmutable $publishedAt;
  private ?int $pageCount;
  private array $tags;
  private string $title;
  private string $nameId;
  private ?string $imageUri;
  private ?string $description;
  private int $version;

  public function __construct(?string            $id,
                              ?string            $isbn,
                              BookState          $state,
                              ?Language          $language,
                              ?DateTimeImmutable $publishedAt,
                              ?int               $pageCount,
                              array              $tags,
                              ?string            $title,
                              ?string            $nameId,
                              ?string            $imageUri,
                              ?string            $description,
                              int                $version) {
    $this->id = $id;
    $this->isbn = $isbn;
    $this->state = $state;
    $this->language = $language;
    $this->publishedAt = $publishedAt;
    $this->pageCount = $pageCount;
    $this->tags = $tags;
    $this->title = $title;
    $this->nameId = $nameId;
    $this->imageUri = $imageUri;
    $this->description = $description;
    $this->version = $version;
  }

  public function id(): ?string {
    return $this->id;
  }

  public function isbn(): ?string {
    return $this->isbn;
  }

  public function state(): BookState {
    return $this->state;
  }

  public function language(): ?Language {
    return $this->language;
  }

  public function publishedAt(): ?DateTimeImmutable {
    return $this->publishedAt;
  }

  public function pageCount(): ?int {
    return $this->pageCount;
  }

  public function tags(): array {
    return $this->tags;
  }

  public function title(): ?string {
    return $this->title;
  }

  public function nameId(): ?string {
    return $this->nameId;
  }

  public function imageUri(): ?string {
    return $this->imageUri;
  }

  public function description(): ?string {
    return $this->description;
  }

  public function version(): int {
    return $this->version;
  }

  public static function builder(): SaveBookCommandBuilder {
    return new SaveBookCommandBuilder();
  }
}

// Fluent Builders
class SaveBookCommandBuilder {
  public function id(string $id): SaveBookCommandBuilderId {
    return new SaveBookCommandBuilderId($id);
  }

  public function noId(): SaveBookCommandBuilderId {
    return new SaveBookCommandBuilderId(null);
  }
}

class SaveBookCommandBuilderId {
  private ?string $id;

  public function __construct(?string $id) {
    $this->id = $id;
  }

  public function isbn(?string $isbn): SaveBookCommandBuilderIsbn {
    return new SaveBookCommandBuilderIsbn($this->id, $isbn);
  }
}

class SaveBookCommandBuilderIsbn {
  private ?string $id;
  private ?string $isbn;

  public function __construct(?string $id, ?string $isbn) {
    $this->id = $id;
    $this->isbn = $isbn;
  }

  public function state(BookState $state): SaveBookCommandBuilderState {
    return new SaveBookCommandBuilderState($this->id, $this->isbn, $state);
  }
}

class SaveBookCommandBuilderState {
  private ?string $id;
  private ?string $isbn;
  private BookState $state;

  public function __construct(?string $id, ?string $isbn, BookState $state) {
    $this->id = $id;
    $this->isbn = $isbn;
    $this->state = $state;
  }

  public function language(?Language $language): SaveBookCommandBuilderLanguage {
    return new SaveBookCommandBuilderLanguage($this->id, $this->isbn, $this->state, $language);
  }

  public function noLanguage(): SaveBookCommandBuilderLanguage {
    return new SaveBookCommandBuilderLanguage($this->id, $this->isbn, $this->state, null);
  }
}

class SaveBookCommandBuilderLanguage {
  private ?string $id;
  private ?string $isbn;
  private BookState $state;
  private ?Language $language;

  public function __construct(?string $id, ?string $isbn, BookState $state, ?Language $language) {
    $this->id = $id;
    $this->isbn = $isbn;
    $this->state = $state;
    $this->language = $language;
  }

  public function publishedAt(DateTimeImmutable $publishedAt): SaveBookCommandBuilderPublishedAt {
    return new SaveBookCommandBuilderPublishedAt($this->id, $this->isbn, $this->state, $this->language, $publishedAt);
  }

  public function noPublishedAt(): SaveBookCommandBuilderPublishedAt {
    return new SaveBookCommandBuilderPublishedAt($this->id, $this->isbn, $this->state, $this->language, null);
  }
}

class SaveBookCommandBuilderPublishedAt {
  private ?string $id;
  private ?string $isbn;
  private BookState $state;
  private ?Language $language;
  private ?DateTimeImmutable $publishedAt;

  public function __construct(?string $id, ?string $isbn, BookState $state, ?Language $language, ?DateTimeImmutable $publishedAt) {
    $this->id = $id;
    $this->isbn = $isbn;
    $this->state = $state;
    $this->language = $language;
    $this->publishedAt = $publishedAt;
  }

  public function pageCount(int $pageCount): SaveBookCommandBuilderPageCount {
    return new SaveBookCommandBuilderPageCount(
      $this->id, $this->isbn, $this->state, $this->language, $this->publishedAt, $pageCount
    );
  }

  public function noPageCount(): SaveBookCommandBuilderPageCount {
    return new SaveBookCommandBuilderPageCount(
      $this->id, $this->isbn, $this->state, $this->language, $this->publishedAt, null
    );
  }
}

class SaveBookCommandBuilderPageCount {
  private ?string $id;
  private ?string $isbn;
  private BookState $state;
  private ?Language $language;
  private ?DateTimeImmutable $publishedAt;
  private ?int $pageCount;

  public function __construct(
    ?string $id, ?string $isbn, BookState $state, ?Language $language, ?DateTimeImmutable $publishedAt, ?int $pageCount) {
    $this->id = $id;
    $this->isbn = $isbn;
    $this->state = $state;
    $this->language = $language;
    $this->publishedAt = $publishedAt;
    $this->pageCount = $pageCount;
  }

  public function tags(array $tags): SaveBookCommandBuilderTags {
    return new SaveBookCommandBuilderTags(
      $this->id, $this->isbn, $this->state, $this->language, $this->publishedAt, $this->pageCount, $tags
    );
  }

  public function noTags(): SaveBookCommandBuilderTags {
    return new SaveBookCommandBuilderTags(
      $this->id, $this->isbn, $this->state, $this->language, $this->publishedAt, $this->pageCount, []
    );
  }
}

class SaveBookCommandBuilderTags {
  private ?string $id;
  private ?string $isbn;
  private BookState $state;
  private ?Language $language;
  private ?DateTimeImmutable $publishedAt;
  private ?int $pageCount;
  private array $tags;

  public function __construct(
    ?string $id, ?string $isbn, BookState $state, ?Language $language, ?DateTimeImmutable $publishedAt, ?int $pageCount,
    array   $tags) {
    $this->id = $id;
    $this->isbn = $isbn;
    $this->state = $state;
    $this->language = $language;
    $this->publishedAt = $publishedAt;
    $this->pageCount = $pageCount;
    $this->tags = $tags;
  }

  public function title(string $title): SaveBookCommandBuilderTitle {
    return new SaveBookCommandBuilderTitle(
      $this->id, $this->isbn, $this->state, $this->language, $this->publishedAt, $this->pageCount, $this->tags, $title
    );
  }
}

class SaveBookCommandBuilderTitle {
  private ?string $id;
  private ?string $isbn;
  private BookState $state;
  private ?Language $language;
  private ?DateTimeImmutable $publishedAt;
  private ?int $pageCount;
  private array $tags;
  private string $title;

  public function __construct(?string            $id, ?string $isbn, BookState $state, ?Language $language,
                              ?DateTimeImmutable $publishedAt, ?int $pageCount, array $tags, string $title) {
    $this->id = $id;
    $this->isbn = $isbn;
    $this->state = $state;
    $this->language = $language;
    $this->publishedAt = $publishedAt;
    $this->pageCount = $pageCount;
    $this->tags = $tags;
    $this->title = $title;
  }

  public function nameId(string $nameId): SaveBookCommandBuilderNameId {
    return new SaveBookCommandBuilderNameId(
      $this->id, $this->isbn, $this->state, $this->language, $this->publishedAt, $this->pageCount, $this->tags,
      $this->title, $nameId
    );
  }
}

class SaveBookCommandBuilderNameId {
  private ?string $id;
  private ?string $isbn;
  private BookState $state;
  private ?Language $language;
  private ?DateTimeImmutable $publishedAt;
  private ?int $pageCount;
  private array $tags;
  private string $title;
  private string $nameId;

  public function __construct(?string            $id, ?string $isbn, BookState $state, ?Language $language,
                              ?DateTimeImmutable $publishedAt, ?int $pageCount, array $tags,
                              string             $title, string $nameId) {
    $this->id = $id;
    $this->isbn = $isbn;
    $this->state = $state;
    $this->language = $language;
    $this->publishedAt = $publishedAt;
    $this->pageCount = $pageCount;
    $this->tags = $tags;
    $this->title = $title;
    $this->nameId = $nameId;
  }

  public function imageUri(string $imageUri): SaveBookCommandBuilderImageUri {
    return new SaveBookCommandBuilderImageUri(
      $this->id, $this->isbn, $this->state, $this->language, $this->publishedAt, $this->pageCount, $this->tags,
      $this->title, $this->nameId, $imageUri
    );
  }

  public function noImageUri(): SaveBookCommandBuilderImageUri {
    return new SaveBookCommandBuilderImageUri(
      $this->id, $this->isbn, $this->state, $this->language, $this->publishedAt, $this->pageCount, $this->tags,
      $this->title, $this->nameId, null
    );
  }
}

class SaveBookCommandBuilderImageUri {
  private ?string $id;
  private ?string $isbn;
  private BookState $state;
  private ?Language $language;
  private ?DateTimeImmutable $publishedAt;
  private ?int $pageCount;
  private array $tags;
  private string $title;
  private string $nameId;
  private ?string $imageUri;

  public function __construct(?string            $id, ?string $isbn, BookState $state, ?Language $language,
                              ?DateTimeImmutable $publishedAt, ?int $pageCount, array $tags,
                              string             $title, string $nameId, ?string $imageUri) {
    $this->id = $id;
    $this->isbn = $isbn;
    $this->state = $state;
    $this->language = $language;
    $this->publishedAt = $publishedAt;
    $this->pageCount = $pageCount;
    $this->tags = $tags;
    $this->title = $title;
    $this->nameId = $nameId;
    $this->imageUri = $imageUri;
  }

  public function description(string $description): SaveBookCommandBuilderDescription {
    return new SaveBookCommandBuilderDescription(
      $this->id, $this->isbn, $this->state, $this->language, $this->publishedAt, $this->pageCount, $this->tags,
      $this->title, $this->nameId, $this->imageUri, $description
    );
  }

  public function noDescription(): SaveBookCommandBuilderDescription {
    return new SaveBookCommandBuilderDescription(
      $this->id, $this->isbn, $this->state, $this->language, $this->publishedAt, $this->pageCount, $this->tags,
      $this->title, $this->nameId, $this->imageUri, null
    );
  }
}

class SaveBookCommandBuilderDescription {
  private ?string $id;
  private ?string $isbn;
  private BookState $state;
  private ?Language $language;
  private ?DateTimeImmutable $publishedAt;
  private ?int $pageCount;
  private array $tags;
  private string $title;
  private string $nameId;
  private ?string $imageUri;
  private ?string $description;

  public function __construct(?string            $id, ?string $isbn, BookState $state, ?Language $language,
                              ?DateTimeImmutable $publishedAt, ?int $pageCount, array $tags, string $title,
                              string             $nameId, ?string $imageUri, ?string $description) {
    $this->id = $id;
    $this->isbn = $isbn;
    $this->state = $state;
    $this->language = $language;
    $this->publishedAt = $publishedAt;
    $this->pageCount = $pageCount;
    $this->tags = $tags;
    $this->title = $title;
    $this->nameId = $nameId;
    $this->imageUri = $imageUri;
    $this->description = $description;
  }

  public function version(int $version): SaveBookCommand {
    return new SaveBookCommand(
      $this->id, $this->isbn, $this->state, $this->language, $this->publishedAt, $this->pageCount, $this->tags,
      $this->title, $this->nameId, $this->imageUri, $this->description, $version
    );
  }
}