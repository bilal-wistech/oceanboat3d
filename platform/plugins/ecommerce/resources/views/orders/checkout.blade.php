@extends('plugins/ecommerce::orders.master')
@section('title')
    {{ __('Checkout') }}
@stop
@section('content')

    @if (Cart::instance('cart')->count() > 0)
        @include('plugins/payment::partials.header')

        {!! Form::open(['route' => ['public.checkout.process', $token], 'class' => 'checkout-form payment-checkout-form', 'id' => 'checkout-form']) !!}
        <input type="hidden" name="checkout-token" id="checkout-token" value="{{ $token }}">

        <div class="container" id="main-checkout-product-info">
            <div class="row">
                <div class="check-bill order-1 order-md-2 col-lg-5 col-md-6 right">
                    <!--<div class="d-block d-sm-none">-->
                    <!--    @include('plugins/ecommerce::orders.partials.logo')-->
                    <!--</div>-->
                    <div id="cart-item" class="position-relative">

                        <div class="payment-info-loading" style="display: none;">
                            <div class="payment-info-loading-content">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>

                        {!! apply_filters(RENDER_PRODUCTS_IN_CHECKOUT_PAGE, $products) !!}

                        <div class="mt-2 p-2">
                            <div class="row">
                                <div class="col-6">
                                    <p class="bill-text">{{ __('Subtotal') }}</p>
                                </div>
                                <div class="col-6">
                                    <p class="price-text sub-total-text text-end"> {{ format_price_cart(Cart::instance('cart')->rawSubTotal()) }} </p>
                                </div>
                            </div>
                            @if (session('applied_coupon_code'))
                                <div class="row coupon-information">
                                    <div class="col-6">
                                        <p class="bill-text">{{ __('Coupon code') }}:</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="price-text coupon-code-text"> {{ session('applied_coupon_code') }} </p>
                                    </div>
                                </div>
                            @endif
                            @if ($couponDiscountAmount > 0)
                                <div class="row price discount-amount">
                                    <div class="col-6">
                                        <p class="bill-text">{{ __('Coupon code discount amount') }}:</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="price-text total-discount-amount-text"> {{ format_price_cart($couponDiscountAmount) }} </p>
                                    </div>
                                </div>
                            @endif
                            @if ($promotionDiscountAmount > 0)
                                <div class="row">
                                    <div class="col-6">
                                        <p class="bill-text">{{ __('Promotion discount amount') }}:</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="price-text"> {{ format_price_cart($promotionDiscountAmount) }} </p>
                                    </div>
                                </div>
                            @endif
                            <!-- @if (!empty($shipping) && Arr::get($sessionCheckoutData, 'is_available_shipping', true))
                                <div class="row">
                                    <div class="col-6">
                                        <p class="bill-text">{{ __('Shipping') }}</p>
                                    </div>
                                    <div class="col-6 float-end">
                                        <p class="price-text shipping-price-text">{{ format_price_cart($shippingAmount) }}</p>
                                    </div>
                                </div>
                            @endif -->
                            <div class="row">
                                <div class="col-6">
                                    <p class="bill-text">{{ __('Shipping') }}</p>
                                </div>
                                <div class="col-6 float-end">
                                    <p class="price-text shipping-price-text">
                                        @if(Arr::get($sessionCheckoutData, 'shipping_amount')!="" && Arr::get($sessionCheckoutData, 'shipping_amount')!=0)
                                        {{  Arr::get($sessionCheckoutData, 'shipping_amount') }} AED
                                        @else
                                        0 AED
                                        @endif
                                    </p>
                                    <input type="hidden" name="shipping_amount" value="{{ Arr::get($sessionCheckoutData, 'shipping_amount') }}">
                                    <input type="hidden" name="token" value="{{$token }}">
                                    <input type="hidden" name="product_code" value="{{  Arr::get($sessionCheckoutData, 'product_code') }}">
                                    <input type="hidden" name="local_product_code" value="{{  Arr::get($sessionCheckoutData, 'local_product_code') }}">
                                    <input type="hidden" name="height" value="{{  Arr::get($sessionCheckoutData, 'height') }}">
                                    <input type="hidden" name="weight" value="{{  Arr::get($sessionCheckoutData, 'weight') }}">
                                    <input type="hidden" name="length" value="{{  Arr::get($sessionCheckoutData, 'length') }}">
                                    <input type="hidden" name="width" value="{{  Arr::get($sessionCheckoutData, 'width') }}">
                                </div>
                            </div>
                            @if (EcommerceHelper::isTaxEnabled())
                                <div class="row">
                                    <div class="col-6">
                                        <p class="bill-text">{{ __('VAT 5%') }}:</p>
                                    </div>
                                    <div class="col-6 float-end">
                                        <p class="price-text tax-price-text">{{ format_price_cart(Cart::instance('cart')->rawTax()) }}</p>
                                    </div>
                                </div>
                            @endif 

                            <div class="row">
                                <div class="col-6">
                                    <p class="bill-text">{{ __('Total') }}</p>
                                </div>
                                <div class="col-6 float-end">
                                    <p class="total-text raw-total-text"
                                       data-price="{{ format_price_cart(Cart::instance('cart')->rawTotal(), null, true) }}"> {{ ($promotionDiscountAmount + $couponDiscountAmount - (float)$shippingAmount) > Cart::instance('cart')->rawTotal() ? format_price_cart(0) : format_price_cart(Cart::instance('cart')->rawTotal() - $promotionDiscountAmount - $couponDiscountAmount + (float)$shippingAmount) }} </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="position-relative">
                            <div class="payment-info-loading" style="display: none;">
                                <div class="payment-info-loading-content">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                            </div>
                            <h5 class="checkout-payment-title">{{ __('Payment method') }}</h5>
                            <input type="hidden" name="amount" value="{{ ($promotionDiscountAmount + $couponDiscountAmount - $shippingAmount) > Cart::instance('cart')->rawTotal() ? 0 : format_price_cart(Cart::instance('cart')->rawTotal() - $promotionDiscountAmount - $couponDiscountAmount + $shippingAmount, null, true) }}">
                            <input type="hidden" name="currency" value="{{ strtoupper(get_application_currency()->title) }}">
                            {!! apply_filters(PAYMENT_FILTER_PAYMENT_PARAMETERS, null) !!}
                            <ul class="list_payment_method">
                                @php
                                    $selected = session('selected_payment_method');
                                    $default = \Botble\Payment\Supports\PaymentHelper::defaultPaymentMethod();
                                    $selecting = $selected ?: $default;
                                @endphp

                                {!! apply_filters(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, null, [
                                        'amount'    => ($promotionDiscountAmount + $couponDiscountAmount - $shippingAmount) > Cart::instance('cart')->rawTotal() ? 0 : format_price_cart(Cart::instance('cart')->rawTotal() - $promotionDiscountAmount - $couponDiscountAmount + $shippingAmount, null, true),
                                        'currency'  => strtoupper(get_application_currency()->title),
                                        'name'      => null,
                                        'selected'  => $selected,
                                        'default'   => $default,
                                        'selecting' => $selecting,
                                    ]) !!}

                                @if (get_payment_setting('status', 'cod') == 1)
                                    <li class="list-group-item mb-4">
                                        <input class="magic-radio js_payment_method" type="radio" name="payment_method" id="payment_cod"
                                            @if ($selecting == \Botble\Payment\Enums\PaymentMethodEnum::COD) checked @endif
                                            value="cod" data-bs-toggle="collapse" data-bs-target=".payment_cod_wrap" data-parent=".list_payment_method">
                                        <label for="payment_cod" class="text-start">{{ setting('payment_cod_name', trans('plugins/payment::payment.payment_via_cod')) }}</label>
                                        <!-- <div class="payment_cod_wrap payment_collapse_wrap collapse @if ($selecting == \Botble\Payment\Enums\PaymentMethodEnum::COD) show @endif" style="padding: 15px 0;">
                                            {!! BaseHelper::clean(setting('payment_cod_description')) !!}

                                            @php $minimumOrderAmount = setting('payment_cod_minimum_amount', 0); @endphp
                                            @if ($minimumOrderAmount > Cart::instance('cart')->rawSubTotal())
                                                <div class="alert alert-warning" style="margin-top: 15px;">
                                                    {{ __('Minimum order amount to use COD (Cash On Delivery) payment method is :amount, you need to buy more :more to place an order!', ['amount' => format_price_cart($minimumOrderAmount), 'more' => format_price_cart($minimumOrderAmount - Cart::instance('cart')->rawSubTotal())]) }}
                                                </div>
                                            @endif
                                        </div> -->
                                    </li>
                                @endif

                                @if (get_payment_setting('status', 'bank_transfer') == 1)
                                    <li class="list-group-item mb-4">
                                        <input class="magic-radio js_payment_method" type="radio" name="payment_method" id="payment_bank_transfer"
                                            @if ($selecting == \Botble\Payment\Enums\PaymentMethodEnum::BANK_TRANSFER) checked @endif
                                            value="bank_transfer"
                                            data-bs-toggle="collapse" data-bs-target=".payment_bank_transfer_wrap" data-parent=".list_payment_method">
                                        <label for="payment_bank_transfer" class="text-start">{{ setting('payment_bank_transfer_name', trans('plugins/payment::payment.payment_via_bank_transfer')) }}</label>
                                        <!-- <div class="payment_bank_transfer_wrap payment_collapse_wrap collapse @if ($selecting == \Botble\Payment\Enums\PaymentMethodEnum::BANK_TRANSFER) show @endif" style="padding: 15px 0;">
                                            {!! BaseHelper::clean(setting('payment_bank_transfer_description')) !!}
                                        </div> -->
                                    </li>
                                @endif

                                  <li class="list-group-item card mb-4 d-flex">
                                    <input class="magic-radio js_payment_method" type="radio" name="payment_method" id="payment_ngenius"
                                        @if ($selecting == \Botble\Payment\Enums\PaymentMethodEnum::NGENIUS) checked @endif
                                        value="ngenius" data-bs-toggle="collapse" data-bs-target=".payment_ngenius_wrap" data-parent=".list_payment_method">
                                    <label for="payment_ngenius" class="text-start">Credit/Debit Payment </label>
                                    <img class= "ml-2" alt="cards" src="{{ Theme::asset()->url('images/icons/paymentcards.png') }}" style="width:100%; max-width: 150px;">
                                   
                                </li>

                                <li class="list-group-item card d-flex">
                                    <input class="magic-radio js_payment_method" type="radio" name="payment_method" id="payment_apple_pay"
                                        @if ($selecting == \Botble\Payment\Enums\PaymentMethodEnum::APPLEPAY) checked @endif
                                        value="apple_pay" data-bs-toggle="collapse" data-bs-target=".payment_apple_pay_wrap" data-parent=".list_payment_method">
                                    <label for="payment_apple_pay" class="text-start">Apple Pay</label>
                                    <img class= "ml-2" alt="cards" src="{{ Theme::asset()->url('images/icons/Apple-Pay-Card.png') }}" style="width:100%; max-width: 53px;">
                                    
                                </li>

                            </ul>
                    </div>

                        <br>
                        @if (EcommerceHelper::getMinimumOrderAmount() > Cart::instance('cart')->rawSubTotal())
                            <div class="alert alert-warning">
                                {{ __('Minimum order amount is :amount, you need to buy more :more to place an order!', ['amount' => format_price_cart(EcommerceHelper::getMinimumOrderAmount()), 'more' => format_price_cart(EcommerceHelper::getMinimumOrderAmount() - Cart::instance('cart')->rawSubTotal())]) }}
                            </div>
                        @endif

                        <div class="form-group mb-3">
                            <div class="row">
                                <!-- <div class="col-md-6 d-none d-md-block" style="line-height: 53px">
                                    <a class="text-info" href="{{ route('public.cart') }}"><i class="fas fa-long-arrow-alt-left"></i> <span class="d-inline-block back-to-cart">{{ __('Back to cart') }}</span></a>
                                </div> -->
                                <div class="col-12 text-center checkout-button-group">
                                    <button type="submit" @if (EcommerceHelper::getMinimumOrderAmount() > Cart::instance('cart')->rawSubTotal()) disabled @endif class="btn payment-checkout-btn payment-checkout-btn-step" data-processing-text="{{ __('Processing. Please wait...') }}" data-error-header="{{ __('Error') }}">
                                       Place Order
                                    </button>
                                </div>
                            </div>
                            <!-- <div class="d-block d-md-none back-to-cart-button-group">
                                <a class="text-info" href="{{ route('public.cart') }}"><i class="fas fa-long-arrow-alt-left"></i> <span class="d-inline-block">{{ __('Back to cart') }}</span></a>
                            </div> -->
                        </div>
                    <!-- <div class="mt-3 mb-5">
                        @include('plugins/ecommerce::themes.discounts.partials.form')
                    </div> -->
                </div>
                <div class="col-lg-7 col-md-6 left">
                    <!-- <div class="d-none d-sm-block">
                        @include('plugins/ecommerce::orders.partials.logo')
                    </div> -->
                    <div class="form-checkout">
                        @if (Arr::get($sessionCheckoutData, 'is_available_shipping', true))
                            <div>
                                <h5 class="checkout-payment-title"><span class="billing">Billing</span> Details</h5>
                                <input type="hidden" value="{{ route('public.checkout.save-information', $token) }}" id="save-shipping-information-url">
                                @include('plugins/ecommerce::orders.partials.address-form', compact('sessionCheckoutData'))
                            </div>
                            <br>
                        @endif

                        @if (EcommerceHelper::isBillingAddressEnabled())
                            <div>
                                <h5 class="checkout-payment-title">{{ __('Billing information') }}</h5>
                                @include('plugins/ecommerce::orders.partials.billing-address-form', compact('sessionCheckoutData'))
                            </div>
                            <br>
                        @endif

                        @if (!is_plugin_active('marketplace'))
                            @if (Arr::get($sessionCheckoutData, 'is_available_shipping', true))
                                <div id="shipping-method-wrapper">
                                    <h5 class="checkout-payment-title"><span class="billing">Shipping</span> Method</h5>
                                    <div class="shipping-info-loading" style="display: none;">
                                        <div class="shipping-info-loading-content">
                                            <i class="fas fa-spinner fa-spin"></i>
                                        </div>
                                    </div>
                                        <div class="payment-checkout-form">
                                            <input type="hidden" name="shipping_option" value="{{ old('shipping_option', $defaultShippingOption) }}">
                                            <ul class="list-group list_payment_method">

                                                @foreach ($shipping as $shippingKey => $shippingItems)
                                                    @foreach($shippingItems as $shippingOption => $shippingItem)
                                                        @include('plugins/ecommerce::orders.partials.shipping-option', [
                                                            'shippingItem' => $shippingItem,
                                                            'attributes' =>[
                                                                'id' => 'shipping-method-' . $shippingKey . '-' . $shippingOption,
                                                                'name' => 'shipping_method',
                                                                'class' => 'magic-radio UAE-options',
                                                                'checked' => true,
                                                                'disabled' => Arr::get($shippingItem, 'disabled'),
                                                                'data-option' => $shippingOption,
                                                            ],
                                                        ])
                                                    @endforeach
                                                @endforeach
                                            
                                            
                                                <li class="rd_dhl list-group-item @if(Arr::get($sessionCheckoutData, 'country')=='AE' ||  Arr::get($sessionCheckoutData, 'country')=='' ) d-none  @endif ">
                                                    {!! Form::radio('shipping_method_dhl',100 ,'checked' , ['class' => 'rd_dhl2 magic-radio DHL-options','data-option' => 100]) !!}
                                                    <label for="shipping-method-100-100">
                                                        <span>DHL Express </span>
                                                    </label>
                                                </li>
                                            
                                            </ul>
                                        </div>
                                </div>
                                <br>
                            @endif
                        @endif

                        <!-- <div class="position-relative">
                            <div class="payment-info-loading" style="display: none;">
                                <div class="payment-info-loading-content">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                            </div>
                            <h5 class="checkout-payment-title">{{ __('Payment method') }}</h5>
                            <input type="hidden" name="amount" value="{{ ($promotionDiscountAmount + $couponDiscountAmount - $shippingAmount) > Cart::instance('cart')->rawTotal() ? 0 : format_price_cart(Cart::instance('cart')->rawTotal() - $promotionDiscountAmount - $couponDiscountAmount + $shippingAmount, null, true) }}">
                            <input type="hidden" name="currency" value="{{ strtoupper(get_application_currency()->title) }}">
                            {!! apply_filters(PAYMENT_FILTER_PAYMENT_PARAMETERS, null) !!}
                            <ul class="list-group list_payment_method">
                                @php
                                    $selected = session('selected_payment_method');
                                    $default = \Botble\Payment\Supports\PaymentHelper::defaultPaymentMethod();
                                    $selecting = $selected ?: $default;
                                @endphp

                                {!! apply_filters(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, null, [
                                        'amount'    => ($promotionDiscountAmount + $couponDiscountAmount - $shippingAmount) > Cart::instance('cart')->rawTotal() ? 0 : format_price_cart(Cart::instance('cart')->rawTotal() - $promotionDiscountAmount - $couponDiscountAmount + $shippingAmount, null, true),
                                        'currency'  => strtoupper(get_application_currency()->title),
                                        'name'      => null,
                                        'selected'  => $selected,
                                        'default'   => $default,
                                        'selecting' => $selecting,
                                    ]) !!}

                                @if (get_payment_setting('status', 'cod') == 1)
                                    <li class="list-group-item">
                                        <input class="magic-radio js_payment_method" type="radio" name="payment_method" id="payment_cod"
                                            @if ($selecting == \Botble\Payment\Enums\PaymentMethodEnum::COD) checked @endif
                                            value="cod" data-bs-toggle="collapse" data-bs-target=".payment_cod_wrap" data-parent=".list_payment_method">
                                        <label for="payment_cod" class="text-start">{{ setting('payment_cod_name', trans('plugins/payment::payment.payment_via_cod')) }}</label>
                                        <div class="payment_cod_wrap payment_collapse_wrap collapse @if ($selecting == \Botble\Payment\Enums\PaymentMethodEnum::COD) show @endif" style="padding: 15px 0;">
                                            {!! BaseHelper::clean(setting('payment_cod_description')) !!}

                                            @php $minimumOrderAmount = setting('payment_cod_minimum_amount', 0); @endphp
                                            @if ($minimumOrderAmount > Cart::instance('cart')->rawSubTotal())
                                                <div class="alert alert-warning" style="margin-top: 15px;">
                                                    {{ __('Minimum order amount to use COD (Cash On Delivery) payment method is :amount, you need to buy more :more to place an order!', ['amount' => format_price_cart($minimumOrderAmount), 'more' => format_price_cart($minimumOrderAmount - Cart::instance('cart')->rawSubTotal())]) }}
                                                </div>
                                            @endif
                                        </div>
                                    </li>
                                @endif

                                @if (get_payment_setting('status', 'bank_transfer') == 1)
                                    <li class="list-group-item">
                                        <input class="magic-radio js_payment_method" type="radio" name="payment_method" id="payment_bank_transfer"
                                            @if ($selecting == \Botble\Payment\Enums\PaymentMethodEnum::BANK_TRANSFER) checked @endif
                                            value="bank_transfer"
                                            data-bs-toggle="collapse" data-bs-target=".payment_bank_transfer_wrap" data-parent=".list_payment_method">
                                        <label for="payment_bank_transfer" class="text-start">{{ setting('payment_bank_transfer_name', trans('plugins/payment::payment.payment_via_bank_transfer')) }}</label>
                                        <div class="payment_bank_transfer_wrap payment_collapse_wrap collapse @if ($selecting == \Botble\Payment\Enums\PaymentMethodEnum::BANK_TRANSFER) show @endif" style="padding: 15px 0;">
                                            {!! BaseHelper::clean(setting('payment_bank_transfer_description')) !!}
                                        </div>
                                    </li>
                                @endif
                            </ul>
                        </div>

                        <br> -->

                        <!-- <div class="form-group mb-3 @if ($errors->has('description')) has-error @endif">
                            <label for="description" class="control-label">{{ __('Order notes') }}</label>
                            <br>
                            <textarea name="description" id="description" rows="3" class="form-control" placeholder="{{ __('Notes about your order, e.g. special notes for delivery.') }}">{{ old('description') }}</textarea>
                            {!! Form::error('description', $errors) !!}
                        </div> -->

                        <!-- @if (EcommerceHelper::getMinimumOrderAmount() > Cart::instance('cart')->rawSubTotal())
                            <div class="alert alert-warning">
                                {{ __('Minimum order amount is :amount, you need to buy more :more to place an order!', ['amount' => format_price_cart(EcommerceHelper::getMinimumOrderAmount()), 'more' => format_price_cart(EcommerceHelper::getMinimumOrderAmount() - Cart::instance('cart')->rawSubTotal())]) }}
                            </div>
                        @endif -->

                        <div class="form-group mb-3">
                            <div class="row">
                                <div class="col-md-6 d-none d-md-block" style="line-height: 53px">
                                    <a class="text-info" href="{{ route('public.cart') }}"><i class="fas fa-long-arrow-alt-left"></i> <span class="d-inline-block back-to-cart">{{ __('Back to cart') }}</span></a>
                                </div>
                                <!-- <div class="col-md-6 checkout-button-group">
                                    <button type="submit" @if (EcommerceHelper::getMinimumOrderAmount() > Cart::instance('cart')->rawSubTotal()) disabled @endif class="btn payment-checkout-btn payment-checkout-btn-step float-end" data-processing-text="{{ __('Processing. Please wait...') }}" data-error-header="{{ __('Error') }}">
                                        {{ __('Checkout') }}
                                    </button>
                                </div> -->
                            </div>
                            <div class="d-block d-md-none back-to-cart-button-group">
                                <a class="text-info" href="{{ route('public.cart') }}"><i class="fas fa-long-arrow-alt-left"></i> <span class="d-inline-block">{{ __('Back to cart') }}</span></a>
                            </div>
                        </div>

                    </div> <!-- /form checkout -->
                </div>
            </div>
        </div>

        @include('plugins/payment::partials.footer')
    @else
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-warning my-5">
                        <span>{!! __('No products in cart. :link!', ['link' => Html::link(route('public.index'), __('Back to shopping'))]) !!}</span>
                    </div>
                </div>
            </div>
        </div>
    @endif
@stop
<style>
.list-group-item.card{
    align-items:center;
    flex-direction: row;
}
.ml-2{
    margin-left:0.8rem;
}
#main-checkout-product-info .check-bill{
    padding:40px;
}
@media (max-width: 768px) {
.list-group-item.card{
    flex-direction: column;
    align-items:normal !important;
}
}
</style>
