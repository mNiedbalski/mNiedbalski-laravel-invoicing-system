<?php

namespace Modules\Invoices\Domain\ValueObjects;

use Ramsey\Uuid\Uuid;

/**
 * Class IdService
 *
 * IdService responsible for generating IDs for entities.
 * Separate class because if we decide to change the ID generation logic in the future it will apply to all entities.
 */
class IdService
{
    public static function generate(): string
    {
        return Uuid::uuid4()->toString();
    }
}
