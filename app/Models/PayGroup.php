<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'pay_id',
        'order_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * ความสัมพันธ์กับ Pay
     */
    public function pay()
    {
        return $this->belongsTo(Pay::class, 'pay_id');
    }

    /**
     * ความสัมพันธ์กับ Orders
     */
    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }
}