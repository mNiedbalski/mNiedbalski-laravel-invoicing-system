<?php

namespace Modules\Invoices\Domain\ValueObjects;

use Ramsey\Uuid\Uuid;

class InvoiceId
{
    private string $id;

    //Private constructor so in order to create new instance method generate() has to be used.

    private function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function generate(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function equals(self $other): bool
    {
        return $this->id === $other->id;
    }
}
