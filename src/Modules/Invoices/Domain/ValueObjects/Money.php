<?php

namespace Modules\Invoices\Domain\ValueObjects;

class Money
{
    /** @var int Represented for example in cents. It's good because if we wanted to operate on for example dollars, we would have to use float, which is not precise enough and billing HAS to be precise. */
    private int $amount;

    // @var string Currency code, e.g. 'USD', 'EUR', etc.
    // It would be a good practice to create another ValueObject for currency, but for the sake of simplicity, I will just use string.
    private string $currency;

    /** Constructor that verifies whether amount is not negative before creating object.
     * Also applies default currency code.
     * @param int $amount
     */
    public function __construct(int $amount, string $currency = 'USD')
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative.');
        }
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }
    /**
     * Adds another Money object to this one.
     *
     * We return new object because class has to be immutable.
     * If we wanted to for example just do: <p>$this->amount += $otherAmount->getAmount()</p><br> class would be mutable, which is not a good practice.
     * @param Money $otherAmount
     * @return Money
     */
    public function add(Money $otherAmount): Money
    {
        return new Money($this->amount + $otherAmount->getAmount());
    }

    /**
     * Subtracts another Money object from this one.
     *
     * We return new object because class has to be immutable.
     * If we wanted to for example just do: <p>$this->amount -= $otherAmount->getAmount()</p><br> class would be mutable, which is not a good practice.
     * @param Money $otherAmount
     * @return Money
     */
    public function subtract(Money $otherAmount): Money
    {
        if ($this->amount < $otherAmount->getAmount()) {
            throw new \InvalidArgumentException('Resulting amount cannot be negative.');
        }
        return new Money($this->amount - $otherAmount->getAmount());
    }
    public function equals(Money $other): bool
    {
        return $this->amount === $other->getAmount();
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

}
