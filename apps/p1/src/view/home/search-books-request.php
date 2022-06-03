<?php

namespace p1\view\home;

class SearchBooksRequest
{
    private ?string $title;
    private ?string $description;
    private array $tags;
    private ?string $author;
    private ?string $isbn;
}