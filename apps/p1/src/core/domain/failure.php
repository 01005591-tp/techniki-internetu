<?php

namespace p1\core\domain;

class Failure
{
    private string $message;

    protected function __construct(string $message)
    {
        $this->message = $message;
    }

    public function message(): string
    {
        return $this->message;
    }

    public static function of(string $message): Failure
    {
        return new Failure($message);
    }
}