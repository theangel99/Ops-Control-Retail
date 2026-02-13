<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'starting_cash',
        'revenue_collection_delay_days',
        'payment_terms_days',
    ];

    protected $casts = [
        'starting_cash' => 'decimal:2',
    ];
}
