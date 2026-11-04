<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesTransaction extends Model
{
    protected $table = 'sales_transactions';

    protected $fillable = [
        'region',
        'country',
        'item_type',
        'sales_channel',
        'order_priority',
        'order_date',
        'order_id',
        'ship_date',
        'units_sold',
        'unit_price',
        'unit_cost',
        'total_revenue',
        'total_cost',
        'total_profit',
    ];

    protected $casts = [
        'order_date' => 'date',
        'ship_date' => 'date',
        'units_sold' => 'integer',
        'unit_price' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'total_profit' => 'decimal:2',
    ];
}
