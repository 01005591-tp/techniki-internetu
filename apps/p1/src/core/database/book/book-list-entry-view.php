<?php

namespace p1\core\database\book;

class BookListEntryView
{
    private string $id;
    private string $isbn;
    private string $title;
    private ?string $imageUri;
    private ?string $description;
    private string $state;
    private ?string $language;

    public function __construct(string  $id,
                                string  $isbn,
                                string  $title,
                                ?string $imageUri,
                                ?string $description,
                                string  $state,
                                ?string $language)
    {
        $this->id = $id;
        $this->isbn = $isbn;
        $this->title = $title;
        $this->imageUri = $imageUri;
        $this->description = $description;
        $this->state = $state;
        $this->language = $language;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function isbn(): string
    {
        return $this->isbn;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function imageUri(): ?string
    {
        return $this->imageUri;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function state(): string
    {
        return $this->state;
    }

    public function language(): ?string
    {
        return $this->language;
    }
}