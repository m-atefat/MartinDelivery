<?php

namespace App\Models;

use App\Enums\OrderStatusesEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => OrderStatusesEnum::class
    ];

    public function sourceDetails(): HasOne
    {
        return $this->hasOne(OrderSourceDetail::class);
    }

    public function destinationDetails(): HasOne
    {
        return $this->hasOne(OrderDestinationDetail::class);
    }

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function canCancel(): bool
    {
        return in_array($this->status->value, [OrderStatusesEnum::PENDING->value, OrderStatusesEnum::ACCEPT->value]);
    }
}
