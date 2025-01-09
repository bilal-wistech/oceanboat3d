@extends('plugins/ecommerce::orders.master')
@section('title')
    {{ __('Order successfully. Order number :id', ['id' => $order->code]) }}
@stop
@section('content')

    <div class="container">
        <div class="row">
            <div class="thankyou text-center">
                <h2>Thank you! Your order has been recieved. We have sent a copy of receipt on your email {{$order->user->email ?: $order->address->email}} aswell.</h2>
            </div>
            <!-- <div class="col-lg-7 col-md-6 col-12 left"> -->
                <!-- @include('plugins/ecommerce::orders.partials.logo') -->

                <!-- <div class="thank-you">
                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                    <div class="d-inline-block">
                        <h3 class="thank-you-sentence">
                            {{ __('Your order is successfully placed') }}
                        </h3>
                        <p>{{ __('Thank you for purchasing our products!') }}</p>
                    </div>
                </div>

                @include('plugins/ecommerce::orders.thank-you.customer-info', compact('order'))

                <a href="{{ route('public.index') }}" class="btn payment-checkout-btn"> {{ __('Continue shopping') }} </a> -->
            <!-- </div> -->
            <!---------------------- start right column ------------------>
            <div class="order-container col-11 mx-auto">

                @include('plugins/ecommerce::orders.thank-you.order-info')

               

                <!-- total info -->
                @include('plugins/ecommerce::orders.thank-you.total-info', ['order' => $order])
            </div>
            <div class="col-lg-12 col-md-12 col-12 mx-auto mb-5 text-center">
                <a href="{{ route('public.index') }}" class="btn payment-checkout-btn"> {{ __('Continue shopping') }} </a>
            </div>
        </div>
    </div>
@stop
