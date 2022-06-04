<?php

namespace p1\core\domain\book;

class GetBookListCommand {
  private int $page;
  private int $pageSize;
  private ?string $title;
  private ?string $description;
  private array $tags;
  private ?string $author;
  private ?string $isbn;

  public function __construct(int     $page,
                              int     $pageSize,
                              ?string $title,
                              ?string $description,
                              array   $tags,
                              ?string $author,
                              ?string $isbn) {
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
}