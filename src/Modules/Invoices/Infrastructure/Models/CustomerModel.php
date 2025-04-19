<?php

namespace Modules\Invoices\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerModel extends Model
{
    protected $table = 'customers';

    protected $fillable = [
        'id',
        'name',
        'email',
    ];

    public $incrementing = false; // UUIDs are not auto-incrementing
    protected $keyType = 'string';
}
