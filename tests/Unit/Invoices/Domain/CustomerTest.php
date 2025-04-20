<?php

namespace Invoices\Domain;

use Modules\Invoices\Domain\Entities\Customer;
use PHPUnit\Framework\TestCase;

class CustomerTest extends TestCase
{
    public function testValidEmail(): void
    {
        $customer = new Customer('John Doe', 'john.doe@example.com', '123e4567-e89b-12d3-a456-426614174000');
        $this->assertEquals('john.doe@example.com', $customer->getEmail());
    }

    public function testInvalidEmailThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format');

        new Customer('John Doe', 'invalid-email', '123e4567-e89b-12d3-a456-426614174000');
    }

    public function testEmptyEmailThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format');

        new Customer('John Doe', '', '123e4567-e89b-12d3-a456-426614174000');
    }
}
