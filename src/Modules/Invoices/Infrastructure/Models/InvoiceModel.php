<?php

namespace Modules\Invoices\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Invoices\Domain\ValueObjects\Money;

class InvoiceModel extends Model
{
    protected $table = 'invoices';

    protected $fillable = [
        'id',
        'status',
        'customer_id',
        'total_price',
    ];

    public $incrementing = false; // UUIDs are not auto-incrementing
    protected $keyType = 'string';

    // Casting fields
    protected $casts = [
        'total_price' => 'integer', // Stored as int in the database
    ];

    // Accessor for totalPrice (conversion from int to Money)
    public function getTotalPriceAttribute($value): Money
    {
        return new Money($value);
    }

    // Mutator for totalPrice (conversion from Money to int)
    public function setTotalPriceAttribute(Money $money): void
    {
        $this->attributes['total_price'] = $money->getAmount();
    }

    // Relationship with Customer
    public function customer(): BelongsTo
    {
        return $this->belongsTo(CustomerModel::class);
    }

    // Relationship with ProductLine -- Invoice has many ProductLines but ProductLines are unique for each Invoice.
    // Even though same product might be added to different invoices, it is not the same product line.
    public function productLines(): HasMany
    {
        return $this->hasMany(ProductLineModel::class, 'invoice_id', 'id');
    }
}
