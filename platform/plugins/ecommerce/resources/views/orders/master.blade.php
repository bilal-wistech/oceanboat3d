<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> @yield('title', __('Checkout')) </title>

    @if (theme_option('favicon'))
        <link rel="shortcut icon" href="{{ RvMedia::getImageUrl(theme_option('favicon')) }}">
    @endif

    {!! Html::style('vendor/core/core/base/libraries/font-awesome/css/fontawesome.min.css') !!}
    {!! Html::style('vendor/core/plugins/ecommerce/css/front-theme.css?v=1.2.0') !!}

    @if (BaseHelper::siteLanguageDirection() == 'rtl')
        {!! Html::style('vendor/core/plugins/ecommerce/css/front-theme-rtl.css?v=1.2.0') !!}
    @endif

    {!! Html::style('vendor/core/core/base/libraries/toastr/toastr.min.css') !!}

    {!! Html::script('vendor/core/plugins/ecommerce/js/checkout.js?v=1.2.0') !!}

    @if (EcommerceHelper::loadCountriesStatesCitiesFromPluginLocation())
        <script src="{{ asset('vendor/core/plugins/location/js/location.js') }}?v=1.2.0"></script>
    @endif

    {!! apply_filters('ecommerce_checkout_header', null) !!}

    @stack('header')
</head>
<body class="checkout-page" @if (BaseHelper::siteLanguageDirection() == 'rtl') dir="rtl" @endif>
    {!! apply_filters('ecommerce_checkout_body', null) !!}
    <div class="checkout-content-wrap">
        <div class="container">
            <div class="row">
                @yield('content')
            </div>
        </div>
    </div>

    @stack('footer')

    {!! Html::script('vendor/core/plugins/ecommerce/js/utilities.js') !!}
    {!! Html::script('vendor/core/core/base/libraries/toastr/toastr.min.js') !!}

    <script type="text/javascript">
        window.messages = {
            error_header: '{{ __('Error') }}',
            success_header: '{{ __('Success') }}',
        }
    </script>

    @if (session()->has('success_msg') || session()->has('error_msg') || isset($errors))
        <script type="text/javascript">
            $(document).ready(function () {
                @if (session()->has('success_msg'))
                    MainCheckout.showNotice('success', '{{ session('success_msg') }}');
                @endif
                @if (session()->has('error_msg'))
                    MainCheckout.showNotice('error', '{{ session('error_msg') }}');
                @endif
                @if (isset($errors))
                    @foreach ($errors->all() as $error)
                        MainCheckout.showNotice('error', '{{ $error }}');
                    @endforeach
                @endif
            });
        </script>
    @endif

    {!! apply_filters('ecommerce_checkout_footer', null) !!}
    <script>
    $(document).ready(function() {        
        var ctr = false;
        $.ajaxSetup({
        beforeSend: function(xhr, settings) {
            // Modify parameters globally before sending the request
                if (settings.url.indexOf('/get-rate') === -1) {
                if (settings.type === 'GET') {
                    //console.log(settings.url);
                    //settings.url += (settings.url.indexOf('?') > -1 ? '&' : '?') + 'global_param=value';
                    if( settings.url.indexOf('?') > -1 && settings.url.indexOf('country=') === -1) {
                        settings.url += '&2=2&country='+$('#address_country').val();
                        ctr = true;
                    }
                }
                }
            },            
            success: function(data) {
               //alert("reached");
               if(ctr == true){
                $('.rd_dhl').addClass('d-none');
                $('.rd_uae').removeClass('d-none');                
                $('#shipping-method-100-100').prop('checked', false);
                $('#shipping-method-default-32').prop('checked', true); 
               }               
            },
            complete: function(data) {                
               //alert("reached22");
               if(ctr == true){
                $('.rd_dhl').addClass('d-none');
                $('.rd_uae').removeClass('d-none');                
                $('#shipping-method-100-100').prop('checked', false);
                $('#shipping-method-default-32').prop('checked', true); 
               }               
            },
        });

        // checks if country field is filled then show phone field
        var country = $('#address_country').val();
        if(country){
            $('#address_phone').css('display', 'block');
        }else{
            $('#address_phone').css('display', 'none');
        }
        checkFields();

        var url="<?php echo url('/'); ?>";
        
        var ship_price = 0;
        var product_code = 0;
        var local_product_code = 0;
        var currency = "<?php echo strtoupper(get_application_currency()->title); ?>";

        function calculaterate(){
    
            var url="<?php echo url('/'); ?>";
            var price = $('.total-text').data('price');
            $('.payment-info-loading').show();
            var city = $('#address_city').val();
            var postal_code = $('#address_postal_code').val();
            var country = $('#address_country').val();
            var height = $('input[name="height"]').val();
            var weight = $('input[name="weight"]').val();
            var length = $('input[name="length"]').val();
            var width = $('input[name="width"]').val();

            if(city!="" && postal_code!="" && country!=""){      
                var token = $('input[name="token"]').val();
                $.ajax({
                    type: "Get",
                    url: url+"/get-rate",
                    data: {
                        token:token,
                        country:country,
                        postal_code:postal_code,
                        city:city,
                        height:height,
                        weight:weight,
                        length:length,
                        width:width,
                    },
                    success: function(data){
                        currency = "";
                        if(data['currency']){
                            currency = data['currency'];
                        }
                        //
                        if(data['price']){
                            ship_price = data['price'];
                            product_code = data['product_code'];
                            local_product_code = data['local_product_code'];
                            $('.shipping-price-text').text(ship_price+" "+currency);
                            $('input[name="shipping_amount"]').val(ship_price);
                            price = (price + ship_price);
                            price = price.toFixed(2);
                            $('.total-text').data('price',price);
                            $('.total-text').text(price+currency);
                            $('input[name="amount"]').val(price);
                            $('input[name="shipping_option"]').val(100);
                            
                            $('input[name="product_code"]').val(data['product_code']);
                            $('input[name="local_product_code"]').val(data['local_product_code']);

                            $('.rd_dhl').removeClass('d-none');
                            $('.rd_uae').addClass('d-none');

                            $('input[name="shipping_method_dhl"]').val(100);
                            $('#shipping-method-100-100').prop('checked', true);
                            $('#shipping-method-default-32').prop('checked', false);
                            $('.rd_dhl2').prop('checked',true);

                        }else{
                            ship_price =0;
                            product_code =0;
                            local_product_code =0;
                            MainCheckout.showNotice('error', data['message']+" Try again with different location");
                            $('.shipping-price-text').text(0);
                            //$('input[name="shipping_option"]').val(0);
                            //$('input[name="shipping_amount"]').val(0);
                            $('.total-text').text(price+currency);
                            $('input[name="amount"]').val(price);
                        
                        var token = $('input[name="token"]').val();
                        
                        $.ajax({
                            type: "Get",
                            url: url+"/checkout/"+token,
                            data: {
                                shipping_amount:ship_price,
                                product_code:product_code,
                                local_product_code:local_product_code
                            },
                            success: function(data){  
                                                            
                                $('.shipping-price-text').text(ship_price+" "+currency);
                                $('input[name="shipping_amount"]').val(ship_price);
                                $('.total-text').data('price',price);
                                $('.total-text').text(price+currency);
                                $('input[name="amount"]').val(price);
                                $('input[name="product_code"]').val(data['product_code']);
                                $('input[name="local_product_code"]').val(data['local_product_code']);

                                $('.rd_dhl').addClass('d-none');
                                $('.rd_uae').removeClass('d-none');                
                                $('#shipping-method-100-100').prop('checked', false);
                                $('#shipping-method-default-32').prop('checked', true);
                                
                            }

                        });
                        

                    }
                        $('.payment-info-loading').hide();
                    }
                });
            }
        }

        function checkFields() {
            var city = $('#address_city').val();
            var postal_code = $('#address_postal_code').val();
            var country = $('#address_country').val();
            if(city==""){
                MainCheckout.showNotice('error', 'City is required');
                $('.payment-info-loading').hide();
            }
            if(postal_code==""){
                MainCheckout.showNotice('error', 'Postal Code is required');
                $('.payment-info-loading').hide();
            }
            if(country==""){
                MainCheckout.showNotice('error', 'Country is required');
                $('.payment-info-loading').hide();
            }
            // Check if all fields are filled
            if (city && postal_code && country && country!="AE") {
                calculaterate();
            }
        }

        // Bind the checkFields function to the input fields' change event
        $(document).on('change', '#address_country', function (e) {
            
           // console.log($('#address_country').val());
            if($('#address_country').val()!="AE"){
            
                checkFields();
            }else{
                var token = $('input[name="token"]').val();
                
                $.ajax({
                    type: "Get",
                    url: url+"/checkout/"+token,
                    //data: $('#checkout-form').serialize() + '&shipping_method=default&1=1',
                    data: 'shipping_method=default&1=1&country='+$('#address_country').val(),
                    /*
                    data: {
                        shipping_method:"default"
                    },*/
                    success: function(data){
                        $('.rd_dhl').addClass('d-none');
                        $('.rd_uae').removeClass('d-none');                
                        $('#shipping-method-100-100').prop('checked', false);
                        $('#shipping-method-default-32').prop('checked', true);
                    }
                });
                
            }
        }); 
        $(document).on('change', '#address_postal_code', function (e) {
            checkFields();
        }); 
        $(document).on('change', '#address_city', function (e) {
            checkFields();
        }); 
    });

    
    </script>

</body>
</html>
