@extends(Theme::getThemeNamespace() . '::views.ecommerce.customers.master')
@section('content')
    @if(cache()->has('boat_data'))
        <div class="alert alert-success">
            Boat saved successfully. Click Book Now for payment!
        </div>
        <?php
        cache()->forget('boat_data');
        ?>
    @endif
    
    @if(cache()->has('payment_success'))
        <div class="alert alert-success">
            Thanks for Booking, we have received your downpayment. Our team will get in touch with you shortly.
        </div>
        <?php
        cache()->forget('payment_success');
        ?>
    @endif
    @php
    cache()->forget('boat_data');
    @endphp
    @if(cache()->has('failure'))
        <div class="alert alert-danger">
            Something went wrong! Please try again later
        </div>
        <?php
        cache()->forget('failure');
        ?>
    @endif
    @php
    cache()->forget('boat_data');
    @endphp
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ __('Hello :name!', ['name' => auth('customer')->user()->name]) }} </h5>
        </div>
        <div class="card-body">
            <p>
                {!! BaseHelper::clean(__('From your account dashboard. you can easily check &amp; view your <a href=":order">recent orders</a>', [
                    'order' => route('customer.orders'),
                ])) !!},

                {!! BaseHelper::clean(__('manage your <a href=":address">shipping and billing addresses</a> and <a href=":profile">edit your password and account details.</a>', [
                    'profile' => route('customer.edit-account'),
                    'address' => route('customer.address'),
                ])) !!}
            </p>
        </div>
    </div>
@endsection
