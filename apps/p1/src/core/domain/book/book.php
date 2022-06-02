<?php

namespace p1\core\domain\book;

require_once "core/domain/audit/auditable-object.php";

use p1\core\domain\audit\AuditableObject;
use p1\core\domain\language\Language;

class Book
{
    private string $id;
    private string $nameId;
    private ?string $isbn;
    private string $title;
    private ?string $description;
    private Language $language;
    private ?int $publishedAt;
    private ?int $publisherId;
    private ?int $pages;
    private BookState $state;
    private ?string $imageUri;
    private ?AuditableObject $auditableObject;
    private ?int $version;

    public function __construct(string           $id,
                                string           $nameId,
                                ?string          $isbn,
                                string           $title,
                                ?string          $description,
                                Language         $language,
                                ?int             $publishedAt,
                                ?int             $publisherId,
                                ?int             $pages,
                                BookState        $state,
                                ?string          $imageUri,
                                ?AuditableObject $auditableObject,
                                ?int             $version)
    {
        $this->id = $id;
        $this->nameId = $nameId;
        $this->isbn = $isbn;
        $this->title = $title;
        $this->description = $description;
        $this->language = $language;
        $this->publishedAt = $publishedAt;
        $this->publisherId = $publisherId;
        $this->pages = $pages;
        $this->state = $state;
        $this->imageUri = $imageUri;
        $this->auditableObject = $auditableObject;
        $this->version = $version;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function nameId(): string
    {
        return $this->nameId;
    }

    public function isbn(): ?string
    {
        return $this->isbn;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function language(): Language
    {
        return $this->language;
    }

    public function publishedAt(): ?int
    {
        return $this->publishedAt;
    }

    public function publisherId(): ?int
    {
        return $this->publisherId;
    }

    public function pages(): ?int
    {
        return $this->pages;
    }

    public function state(): BookState
    {
        return $this->state;
    }

    public function imageUri(): ?string
    {
        return $this->imageUri;
    }

    public function auditableObject(): ?AuditableObject
    {
        return $this->auditableObject;
    }

    public function version(): ?int
    {
        return $this->version;
    }
}