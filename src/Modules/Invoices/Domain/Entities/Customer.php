<?php

namespace Modules\Invoices\Domain\Entities;

use Modules\Invoices\Domain\ValueObjects\IdService;

class Customer
{
    private string $id; // string because UUID
    private string $name;
    private string $email;

    /** Constructor that creates customer object with e-mail validation.
     * @param string $id
     * @param string $name
     * @param string $email
     */
    public function __construct(string $name, string $email, ?string $id = null )
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format.');
        }

        $this->id = $id ?? IdService::generate();
        $this->name = $name;
        $this->email = $email;
    }

    public function getId(): string
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        throw new \InvalidArgumentException('Invalid email format.');
    }

}
