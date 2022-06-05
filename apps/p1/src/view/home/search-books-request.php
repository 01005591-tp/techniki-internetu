<?php

namespace p1\view\home;

class SearchBooksRequest {
  private int $page;
  private int $pageSize;
  private ?string $title;
  private ?string $description;
  private array $tags;
  private ?string $author;
  private ?string $isbn;

  public function __construct(int     $page,
                              int     $pageSize,
                              ?string $title = null,
                              ?string $description = null,
                              array   $tags = [],
                              ?string $author = null,
                              ?string $isbn = null) {
    $this->page = $page;
    $this->pageSize = $pageSize;
    $this->title = $title;
    $this->description = $description;
    $this->tags = $tags;
    $this->author = $author;
    $this->isbn = $isbn;
  }

  public function page(): int {
    return $this->page;
  }

  public function pageSize(): int {
    return $this->pageSize;
  }

  public function title(): ?string {
    return $this->title;
  }

  public function description(): ?string {
    return $this->description;
  }

  public function tags(): array {
    return $this->tags;
  }

  public function author(): ?string {
    return $this->author;
  }

  public function isbn(): ?string {
    return $this->isbn;
  }

  public function withPage(int $page): SearchBooksRequest {
    return new SearchBooksRequest(
      $page,
      $this->pageSize,
      $this->title,
      $this->description,
      $this->tags,
      $this->author,
      $this->isbn
    );
  }

  public function withCriteriaCleared(): SearchBooksRequest {
    return new SearchBooksRequest(
      $this->page,
      $this->pageSize
    );
  }

  public static function defaultRequest(): SearchBooksRequest {
    return new SearchBooksRequest(
      1,
      3
    );
  }
}