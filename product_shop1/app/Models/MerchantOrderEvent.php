<?php

namespace App\Models;

use App\Enums\MerchantOrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A single entry in a merchant order's status history.
 */
class MerchantOrderEvent extends Model
{
    public const UPDATED_AT = null; // history rows are immutable — created_at only

    protected $fillable = [
        'merchant_order_id',
        'status',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => MerchantOrderStatus::class,
            'created_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(MerchantOrder::class, 'merchant_order_id');
    }
}
