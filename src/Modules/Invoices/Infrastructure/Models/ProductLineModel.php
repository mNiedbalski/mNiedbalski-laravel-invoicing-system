<?php

namespace Modules\Invoices\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Invoices\Domain\ValueObjects\Money;
class ProductLineModel extends Model
{
    protected $table = 'invoice_product_lines';
    protected $fillable = [
        'id',
        'invoice_id',
        'name',
        'quantity',
        'unit_price',
        'total_unit_price',
        'tax_rate',
        'discount_rate',
    ];

    public $incrementing = false; // UUIDs are not auto-incrementing
    protected $keyType = 'string';

    // Casting fields because Eloquent treats fields as strings by default
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'integer',
        'total_unit_price' => 'integer',
        'tax_rate' => 'float',
        'discount_rate' => 'float',
    ];

    // Accessor for unitPrice (conversion from int to Money)
    public function getUnitPriceAttribute($value): Money
    {
        return new Money($value);
    }

    // Mutator for unitPrice (conversion from Money to int)
    public function setUnitPriceAttribute(Money $money): void
    {
        $this->attributes['unit_price'] = $money->getAmount();
    }

    // Accessor for totalUnitPrice (conversion from int to Money)
    public function getTotalUnitPriceAttribute($value): Money
    {
        return new Money($value);
    }

    // Mutator for totalUnitPrice (conversion from Money to int)
    public function setTotalUnitPriceAttribute(Money $money): void
    {
        $this->attributes['total_unit_price'] = $money->getAmount();
    }
}
