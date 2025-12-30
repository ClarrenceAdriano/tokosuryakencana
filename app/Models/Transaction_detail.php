<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction_detail extends Model
{
    protected $fillable = [
        'product_id',
        'transaction_id',
        'quantity',
        'subtotal',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
