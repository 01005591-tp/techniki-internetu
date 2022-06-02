<?php

namespace p1\core\domain\book;

require_once "core/domain/audit/auditable-object.php";

use p1\core\domain\audit\AuditableObject;

class BookAuthor
{
    private int $bookId;
    private int $authorId;
    private int $priority;
    private ?AuditableObject $auditableObject;
    private ?int $version;

    public function __construct(int             $bookId,
                                int             $authorId,
                                int             $priority,
                                AuditableObject $auditableObject,
                                ?int            $version)
    {
        $this->bookId = $bookId;
        $this->authorId = $authorId;
        $this->priority = $priority;
        $this->auditableObject = $auditableObject;
        $this->version = $version;
    }

    public function bookId(): int
    {
        return $this->bookId;
    }

    public function authorId(): int
    {
        return $this->authorId;
    }

    public function priority(): int
    {
        return $this->priority;
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