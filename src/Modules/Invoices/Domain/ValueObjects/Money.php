<?php

namespace Modules\Invoices\Domain\ValueObjects;

class Money
{
    /** @var int Represented for example in cents. It's good because if we wanted to operate on for example dollars, we would have to use float, which is not precise enough and billing HAS to be precise. */
    private int $amount;

    /** Constructor that verifies whether amount is not negative before creating object.
     * @param int $amount
     */
    public function __construct(int $amount)
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative.');
        }
        $this->amount = $amount;
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

}
