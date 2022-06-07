<?php

namespace p1\core\domain\book;

require_once "core/domain/book/book.php";
require_once "core/domain/book/publisher.php";

class BookDetails {
  private Book $book;
  private ?Publisher $publisher;
  private array $authors;
  private BookPieces $bookPieces;
  private array $bookTags;
  private array $bookAuthors;

  public function __construct(Book       $book,
                              ?Publisher $publisher,
                              array      $authors,
                              BookPieces $bookPieces,
                              array      $bookTags,
                              array      $bookAuthors) {
    $this->book = $book;
    $this->publisher = $publisher;
    $this->authors = $authors;
    $this->bookPieces = $bookPieces;
    $this->bookTags = $bookTags;
    $this->bookAuthors = $bookAuthors;
  }

  public function book(): Book {
    return $this->book;
  }

  public function publisher(): ?Publisher {
    return $this->publisher;
  }

  public function authors(): array {
    return $this->authors;
  }

  public function bookPieces(): BookPieces {
    return $this->bookPieces;
  }

  public function bookTags(): array {
    return $this->bookTags;
  }

  public function bookAuthors(): array {
    return $this->bookAuthors;
  }

  public function withBook(Book $book): BookDetails {
    return new BookDetails(
      $book, $this->publisher, $this->authors, $this->bookPieces, $this->bookTags, $this->bookAuthors
    );
  }

  public function withTags(array $bookTags): BookDetails {
    return new BookDetails(
      $this->book, $this->publisher, $this->authors, $this->bookPieces, $bookTags, $this->bookAuthors
    );
  }
}