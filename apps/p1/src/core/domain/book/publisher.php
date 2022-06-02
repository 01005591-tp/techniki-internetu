<?php

namespace p1\core\domain\book;

require_once "core/domain/audit/auditable-object.php";

use p1\core\domain\audit\AuditableObject;

class Publisher
{
    private int $id;
    private string $name;
    private ?AuditableObject $auditableObject;
    private ?int $version;

    public function __construct(int              $id,
                                string           $name,
                                ?AuditableObject $auditableObject,
                                ?int             $version)
    {
        $this->id = $id;
        $this->name = $name;
        $this->auditableObject = $auditableObject;
        $this->version = $version;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function AuditableObject(): ?AuditableObject
    {
        return $this->auditableObject;
    }

    public function version(): ?int
    {
        return $this->version;
    }
}