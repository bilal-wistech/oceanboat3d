<div class="shipment-info-panel hide-print">
    <div class="shipment-info-header">
        <!-- <a target="_blank" href="{{ route('ecommerce.shipments.edit', $shipment->id) }}"> -->
            <h4>{{ get_shipment_code($shipment->id) }}</h4>
        <!-- </a> -->
            <span class="label carrier-status carrier-status-{{ $order->dhl_shipping_status==null ? $shipment->status->label() : $order->dhl_shipping_status  }}">{{$order->dhl_shipping_status==null ? $shipment->status->label() : $order->dhl_shipping_status}}</span>
    </div>

    <div class="pd-all-20 pt10">
        <div class="flexbox-grid-form flexbox-grid-form-no-outside-padding rps-form-767 pt10">
            <div class="flexbox-grid-form-item ws-nm">
                <span>{{ trans('plugins/ecommerce::shipping.shipping_method') }}: <span><i>DHL Express</i></span></span>
            </div>
            <!-- tracking id from dhl -->
            @if($order->dhl_tracking_number!=null)
            <div class="flexbox-grid-form-item rps-no-pd-none-r ws-nm">
                <span>DHL Tracking Number:</span> <span><i>{{ $order->dhl_tracking_number }}</i></span>
            </div>
            @endif
            @if($order->dhl_dispatch_number!=null)
            <div class="flexbox-grid-form-item rps-no-pd-none-r ws-nm">
                <span>DHL Dispatch Confirmation Number:</span> <span><i>{{ $order->dhl_dispatch_number }}</i></span>
            </div>
            @endif
            <div class="flexbox-grid-form-item rps-no-pd-none-r ws-nm">
                <span>{{ trans('plugins/ecommerce::shipping.weight_unit', ['unit' => ecommerce_weight_unit()]) }}:</span> <span><i>{{ $order->weight*1000 }} {{ ecommerce_weight_unit() }}</i></span>
            </div>
        </div>
        <div class="flexbox-grid-form flexbox-grid-form-no-outside-padding rps-form-767 pt10">
            <div class="flexbox-grid-form-item ws-nm">
                <span>{{ trans('plugins/ecommerce::shipping.updated_at') }}:</span> <span><i>{{ $shipment->updated_at }}</i></span>
            </div>
            @if ((float)$shipment->cod_amount)
                <div class="flexbox-grid-form-item ws-nm rps-no-pd-none-r">
                    <span>{{ trans('plugins/ecommerce::shipping.cod_amount') }}:</span>
                    <span><i>{{ format_price($shipment->cod_amount) }}</i></span>
                </div>
            @endif
        </div>

        @if ($shipment->note)
            <div class="flexbox-grid-form flexbox-grid-form-no-outside-padding rps-form-767 pt10">
                <div class="flexbox-grid-form-item ws-nm rps-no-pd-none-r">
                    <span>{{ trans('plugins/ecommerce::shipping.delivery_note') }}:</span>
                    <strong><i>{{ $shipment->note }}</i></strong>
                </div>
            </div>
        @endif
    </div>

    @if ($shipment->status != \Botble\Ecommerce\Enums\ShippingStatusEnum::CANCELED)
        <div class="panel-heading order-bottom shipment-actions-wrapper">
            <div class="flexbox-grid-default">
                <div class="flexbox-content">
                    @if (in_array($shipment->status, [\Botble\Ecommerce\Enums\ShippingStatusEnum::NOT_APPROVED, \Botble\Ecommerce\Enums\ShippingStatusEnum::APPROVED]))
                        <button type="button" class="btn btn-secondary btn-destroy btn-cancel-shipment" data-action="{{ route('orders.cancel-shipment', $shipment->id) }}">{{ trans('plugins/ecommerce::shipping.cancel_shipping') }}</button>
                    @endif

                    @if ($order->is_confirmed && $order->dhl_tracking_number==null && $order->dhl_dispatch_number==null)
                    @include('plugins/ecommerce::orders.confirm-form', ['order' => $order])
                    <a href="#" data-bs-toggle="modal" data-bs-target="#confirm-order">
                        <span class="btn btn-info"><i class="fas fa-shipping-fast"></i>
                                Ship Order
                        </span>
                    </a>
                    @endif
                    @if ($order->dhl_tracking_number!=null && $order->dhl_shipping_status!="Delivered")
                    <a href="{{ route('track.order', $order->id) }}" class="btn btn-info ml10"><i class="fas fa-shipping-fast"></i> Track Order </a>
                    @endif

                    {!! apply_filters('shipment_buttons_detail_order', null, $shipment) !!}

                </div>
            </div>
        </div>
    @endif
</div>
