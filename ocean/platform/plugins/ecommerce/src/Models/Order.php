<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;
use Botble\Ecommerce\Enums\OrderAddressTypeEnum;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Enums\ShippingMethodEnum;
use Botble\Ecommerce\Enums\ShippingStatusEnum;
use Botble\Ecommerce\Repositories\Interfaces\ShipmentInterface;
use Botble\Payment\Models\Payment;
use Botble\Payment\Repositories\Interfaces\PaymentInterface;
use Carbon\Carbon;
use EcommerceHelper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OrderHelper;

class Order extends BaseModel
{
    protected $table = 'ec_orders';

    protected $fillable = [
        'status',
        'user_id',
        'amount',
        'tax_amount',
        'shipping_method',
        'shipping_option',
        'shipping_amount',
        'description',
        'coupon_code',
        'discount_amount',
        'sub_total',
        'is_confirmed',
        'discount_description',
        'is_finished',
        'token',
        'completed_at',
    ];

    protected $casts = [
        'status' => OrderStatusEnum::class,
        'shipping_method' => ShippingMethodEnum::class,
        'completed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        self::deleting(function (Order $order) {
            app(ShipmentInterface::class)->deleteBy(['order_id' => $order->id]);
            OrderHistory::where('order_id', $order->id)->delete();
            OrderProduct::where('order_id', $order->id)->delete();
            OrderAddress::where('order_id', $order->id)->delete();
            app(PaymentInterface::class)->deleteBy(['order_id' => $order->id]);
        });

        static::creating(function (Order $order) {
            $order->code = static::generateUniqueCode();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'user_id', 'id')->withDefault();
    }

    protected function userName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->user->name
        );
    }

    protected function fullAddress(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->shippingAddress->area. ', ' . $this->shippingAddress->address . ', ' . $this->shippingAddress->building . ', ' . $this->shippingAddress->floor . ', ' . $this->shippingAddress->countryname
        );
    }

    protected function shippingMethodName(): Attribute
    {
        return Attribute::make(
            get: fn () => OrderHelper::getShippingMethod(
                $this->attributes['shipping_method'],
                $this->attributes['shipping_option']
            )
        );
    }

    public function address(): HasOne
    {
        return $this->hasOne(OrderAddress::class, 'order_id')
            ->where('type', OrderAddressTypeEnum::SHIPPING)
            ->withDefault();
    }

    public function shippingAddress(): HasOne
    {
        return $this->hasOne(OrderAddress::class, 'order_id')
            ->where('type', OrderAddressTypeEnum::SHIPPING)
            ->withDefault();
    }

    public function billingAddress(): HasOne
    {
        return $this->hasOne(OrderAddress::class, 'order_id')
            ->where('type', OrderAddressTypeEnum::BILLING)
            ->withDefault();
    }

    public function referral(): HasOne
    {
        return $this->hasOne(OrderReferral::class, 'order_id')->withDefault();
    }

    public function products(): HasMany
    {
        return $this->hasMany(OrderProduct::class, 'order_id')->with(['product']);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(OrderHistory::class, 'order_id')->with(['user', 'order']);
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class)->withDefault();
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id')->withDefault();
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'reference_id')->withDefault();
    }

    public function canBeCanceled(): bool
    {
        if ($this->shipment && in_array(
            $this->shipment->status,
            [ShippingStatusEnum::PICKED, ShippingStatusEnum::DELIVERED, ShippingStatusEnum::AUDITED]
        )) {
            return false;
        }

        return in_array($this->status, [OrderStatusEnum::PENDING, OrderStatusEnum::PROCESSING]);
    }

    public function canBeCanceledByAdmin(): bool
    {
        if ($this->shipment && in_array(
            $this->shipment->status,
            [ShippingStatusEnum::DELIVERED, ShippingStatusEnum::AUDITED]
        )) {
            return false;
        }

        if (in_array($this->status, [OrderStatusEnum::COMPLETED, OrderStatusEnum::CANCELED])) {
            return false;
        }

        if ($this->shipment && in_array($this->shipment->status, [
                ShippingStatusEnum::PENDING,
                ShippingStatusEnum::APPROVED,
                ShippingStatusEnum::NOT_APPROVED,
                ShippingStatusEnum::ARRANGE_SHIPMENT,
                ShippingStatusEnum::READY_TO_BE_SHIPPED_OUT,
            ])) {
            return true;
        }

        return true;
    }

    public function getIsFreeShippingAttribute(): bool
    {
        return $this->shipping_amount == 0 && $this->discount_amount == 0 && $this->coupon_code;
    }

    public function getAmountFormatAttribute(): string
    {
        return format_price($this->amount);
    }

    public function getDiscountAmountFormatAttribute(): string
    {
        return format_price($this->shipping_amount);
    }

    public function isInvoiceAvailable(): bool
    {
        return $this->invoice()->exists() && (! EcommerceHelper::disableOrderInvoiceUntilOrderConfirmed(
        ) || $this->is_confirmed);
    }

    public function getProductsWeightAttribute(): float|int
    {
        $weight = 0;

        foreach ($this->products as $product) {
            if ($product && $product->weight) {
                $weight += $product->weight * $product->qty;
            }
        }

        return EcommerceHelper::validateOrderWeight($weight);
    }

    public function returnRequest(): HasOne
    {
        return $this->hasOne(OrderReturn::class, 'order_id')->withDefault();
    }

    public function canBeReturned(): bool
    {
        if ($this->status != OrderStatusEnum::COMPLETED || ! $this->completed_at) {
            return false;
        }

        $shipmentDayCount = Carbon::now()->diffInDays($this->completed_at);

        if ($shipmentDayCount > EcommerceHelper::getReturnableDays()) {
            return false;
        }

        if (EcommerceHelper::isEnabledSupportDigitalProducts()) {
            if ($this->products->where('times_downloaded')->count()) {
                return false;
            }
        }

        return ! $this->returnRequest()->exists();
    }

    public static function generateUniqueCode(): string
    {
        $nextInsertId = static::query()->max('id') + 1;

        do {
            $code = get_order_code($nextInsertId);
            $nextInsertId++;
        } while (static::query()->where('code', $code)->exists());

        return $code;
    }
}
