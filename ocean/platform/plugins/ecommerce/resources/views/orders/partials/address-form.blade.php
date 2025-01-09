<div class="customer-address-payment-form">

    @if (EcommerceHelper::isEnabledGuestCheckout() && !auth('customer')->check())
        <div class="form-group mb-3">
            <p>{{ __('Already have an account?') }} <a href="{{ route('customer.login') }}">{{ __('Login') }}</a></p>
        </div>
    @endif

    @if (auth('customer')->check())
        @php
            $addresses = get_customer_addresses();
            $isAvailableAddress = !$addresses->isEmpty();
            $sessionAddressId = Arr::get($sessionCheckoutData, 'address_id', $isAvailableAddress ? $addresses->first()->id : null);
        @endphp
        <div class="form-group mb-3">

            @if ($isAvailableAddress)
                <label class="control-label mb-2" for="address_id">{{ __('Select available addresses') }}:</label>
            @endif

            <div class="list-customer-address @if (!$isAvailableAddress) d-none @endif">
                <div class="select--arrow">
                    <select name="address[address_id]" class="form-control address-control-item" id="address_id">
                        <option value="new" @if (old('address.address_id', $sessionAddressId) == 'new') selected @endif>{{ __('Add new address...') }}</option>
                        @if ($isAvailableAddress)
                            @foreach ($addresses as $address)
                                <option
                                    value="{{ $address->id }}"
                                    @if (
                                        ($address->is_default && !$sessionAddressId) ||
                                        ($sessionAddressId == $address->id) ||
                                        (!old('address.address_id', $sessionAddressId) && $loop->first)
                                    )
                                        selected="selected"
                                    @endif
                                >
                                    {{ $address->address }}, {{ $address->city_name }}, {{ $address->state_name }}@if (EcommerceHelper::isUsingInMultipleCountries()), {{ $address->country_name }} @endif @if (EcommerceHelper::isZipCodeEnabled() && $address->zip_code), {{ $address->zip_code }} @endif</option>
                            @endforeach
                        @endif
                    </select>
                    <i class="fas fa-angle-down"></i>
                </div>
                <br>
                <div class="address-item-selected @if ($sessionAddressId == 'new') d-none @endif">
                    @if ($isAvailableAddress)
                        @if ($sessionAddressId && $addresses->contains('id', $sessionAddressId))
                            @include('plugins/ecommerce::orders.partials.address-item', ['address' => $addresses->firstWhere('id', $sessionAddressId)])
                        @elseif ($defaultAddress = get_default_customer_address())
                            @include('plugins/ecommerce::orders.partials.address-item', ['address' => $defaultAddress])
                        @else
                            @include('plugins/ecommerce::orders.partials.address-item', ['address' => Arr::first($addresses)])
                        @endif
                    @endif
                </div>
                <div class="list-available-address d-none">
                    @if ($isAvailableAddress)
                        @foreach($addresses as $address)
                            <div class="address-item-wrapper" data-id="{{ $address->id }}">
                                @include('plugins/ecommerce::orders.partials.address-item', compact('address'))
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div class="address-form-wrapper @if (auth('customer')->check() && $isAvailableAddress && (!empty($sessionAddressId) && $sessionAddressId !== 'new')) d-none @endif">
        <div class="row">
            <div class="col-12">
                <div class="form-group mb-4 @if ($errors->has('address.name')) has-error @endif">
                    <input type="text" name="address[name]" id="address_name" placeholder="First Name" class="form-control address-control-item address-control-item-required checkout-input"
                           value="{{ old('address.name', Arr::get($sessionCheckoutData, 'name')) }}">
                    {!! Form::error('address.name', $errors) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group mb-4 @if ($errors->has('address.lname')) has-error @endif">
                    <input type="text" name="address[lname]" id="address_lname" placeholder="Last Name" class="form-control address-control-item address-control-item-required checkout-input"
                           value="{{ old('address.lname', Arr::get($sessionCheckoutData, 'lname')) }}">
                    {!! Form::error('address.lname', $errors) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
            <div class="form-group mb-4 @if ($errors->has('address.email')) has-error @endif">
                    <input type="text" name="address[email]" id="address_email" placeholder="Email" class="form-control address-control-item address-control-item-required checkout-input" value="{{ old('address.email', Arr::get($sessionCheckoutData, 'email')) }}">
                    {!! Form::error('address.email', $errors) !!}
                </div>
            </div>
        </div>

        
        <!-- comment -->
        <!-- <div class="row">
            <div class="col-lg-8 col-12">
                <div class="form-group  @if ($errors->has('address.email')) has-error @endif">
                    <input type="text" name="address[email]" id="address_email" placeholder="{{ __('Email') }}" class="form-control address-control-item address-control-item-required checkout-input" value="{{ old('address.email', Arr::get($sessionCheckoutData, 'email')) }}">
                    {!! Form::error('address.email', $errors) !!}
                </div>
            </div>
            <div class="col-lg-4 col-12">
                <div class="form-group  @if ($errors->has('address.phone')) has-error @endif">
                    {!! Form::phoneNumber('address[phone]', old('address.phone', Arr::get($sessionCheckoutData, 'phone')), ['id' => 'address_phone', 'class' => 'form-control address-control-item ' . (!EcommerceHelper::isPhoneFieldOptionalAtCheckout() ? 'address-control-item-required' : '') . ' checkout-input']) !!}
                    {!! Form::error('address.phone', $errors) !!}
                </div>
            </div>
        </div> -->

        <!-- comment end -->

        <div class="row">
            @if (EcommerceHelper::isUsingInMultipleCountries())
                <div class="col-12">
                    <div class="form-group mb-4 @if ($errors->has('address.country')) has-error @endif">
                        <div class="select--arrow">
                            <select onchange="phonefield()" name="address[country]" class="form-control address-control-item address-control-item-required" id="address_country" data-type="country">
                                @foreach(EcommerceHelper::getAvailableCountries() as $countryCode => $countryName)
                                    <option value="{{ $countryCode }}" @if (old('address.country', Arr::get($sessionCheckoutData, 'country')) == $countryCode) selected @endif>{{ $countryName }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-angle-down"></i>
                        </div>
                        {!! Form::error('address.country', $errors) !!}
                    </div>
                </div>
            @else
                <input type="hidden" name="address[country]" id="address_country" value="{{ EcommerceHelper::getFirstCountryId() }}">
            @endif
            
           
            <div class="col-12">
            <div class="form-group mb-4 @if ($errors->has('address.phone')) has-error @endif">
            <input type="text"  style="display:none; width:100%;" name="address[phone]" id="address_phone" placeholder="Phone Number" class="form-control address-control-item address-control-item-required checkout-input" value="{{ old('address.phone', Arr::get($sessionCheckoutData, 'phone')) }}">
                    {!! Form::error('address.phone', $errors) !!}
                </div>
            </div>
        
 <!-- comment -->
            <!-- <div class="col-sm-6 col-12">
                <div class="form-group mb-3 @if ($errors->has('address.state')) has-error @endif">
                    @if (EcommerceHelper::loadCountriesStatesCitiesFromPluginLocation())
                        <div class="select--arrow">
                            <select name="address[state]" class="form-control address-control-item address-control-item-required" id="address_state" data-type="state" data-url="{{ route('ajax.states-by-country') }}">
                                <option value="">{{ __('Select state...') }}</option>
                                @if (old('address.country', Arr::get($sessionCheckoutData, 'country')) || !EcommerceHelper::isUsingInMultipleCountries())
                                    @foreach(EcommerceHelper::getAvailableStatesByCountry(old('address.country', Arr::get($sessionCheckoutData, 'country'))) as $stateId => $stateName)
                                        <option value="{{ $stateId }}" @if (old('address.state', Arr::get($sessionCheckoutData, 'state')) == $stateId) selected @endif>{{ $stateName }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <i class="fas fa-angle-down"></i>
                        </div>
                    @else
                        <input id="address_state" type="text" class="form-control address-control-item address-control-item-required checkout-input" placeholder="{{ __('State') }}" name="address[state]" value="{{ old('address.state', Arr::get($sessionCheckoutData, 'state')) }}">
                    @endif
                    {!! Form::error('address.state', $errors) !!}
                </div>
            </div> -->

            <!-- <div class="col-sm-6 col-12">
                <div class="form-group  @if ($errors->has('address.city')) has-error @endif">
                    @if (EcommerceHelper::loadCountriesStatesCitiesFromPluginLocation())
                        <div class="select--arrow">
                            <select name="address[city]" class="form-control address-control-item address-control-item-required" id="address_city" data-type="city" data-url="{{ route('ajax.cities-by-state') }}">
                                <option value="">{{ __('Select city...') }}</option>
                                @if (old('address.state', Arr::get($sessionCheckoutData, 'state')))
                                    @foreach(EcommerceHelper::getAvailableCitiesByState(old('address.state', Arr::get($sessionCheckoutData, 'state'))) as $cityId => $cityName)
                                        <option value="{{ $cityId }}" @if (old('address.city', Arr::get($sessionCheckoutData, 'city')) == $cityId) selected @endif>{{ $cityName }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <i class="fas fa-angle-down"></i>
                        </div>
                    @else
                        <input id="address_city" type="text" class="form-control address-control-item address-control-item-required checkout-input" placeholder="{{ __('City') }}" name="address[city]" value="{{ old('address.city', Arr::get($sessionCheckoutData, 'city')) }}">
                    @endif
                    {!! Form::error('address.city', $errors) !!}
                </div>
            </div> -->
 <!-- comment end -->
             <div class="col-12">
                <div class="form-group mb-4 @if ($errors->has('address.area')) has-error @endif">
                    <input id="address_area" type="text" class="form-control address-control-item address-control-item-required checkout-input" placeholder="District or Area" name="address[area]" value="{{ old('address.area', Arr::get($sessionCheckoutData, 'area')) }}">
                    {!! Form::error('address.area', $errors) !!}
                </div>
            </div>
            <div class="col-12">
                <div class="form-group mb-4 @if ($errors->has('address.address')) has-error @endif">
                    <input id="address_address" type="text" class="form-control address-control-item address-control-item-required checkout-input" placeholder="Street Name" name="address[address]" value="{{ old('address.address', Arr::get($sessionCheckoutData, 'address')) }}">
                    {!! Form::error('address.address', $errors) !!}
                </div>
            </div>


            <!--<div class="col-12">-->
            <!--    <div class="form-group mb-4 @if ($errors->has('address.streetname')) has-error @endif">-->
            <!--        <input id="address_streetname" type="text" class="form-control address-control-item address-control-item-required checkout-input" placeholder="Street Name" name="address[streetname]" value="{{ old('address.streetname', Arr::get($sessionCheckoutData, 'streetname')) }}">-->
            <!--        {!! Form::error('address.streetname', $errors) !!}-->
            <!--    </div>-->
            <!--</div>-->

            <div class="col-12">
                <div class="form-group mb-4 @if ($errors->has('address.building')) has-error @endif">
                    <input id="address_building" type="text" class="form-control address-control-item address-control-item-required checkout-input" placeholder="Building or Villa No." name="address[building]" value="{{ old('address.building', Arr::get($sessionCheckoutData, 'building')) }}">
                    {!! Form::error('address.building', $errors) !!}
                </div>
            </div>

            <div class="col-12">
                <div class="form-group mb-4 @if ($errors->has('address.floor')) has-error @endif">
                    <input id="address_floor" type="text" class="form-control address-control-item address-control-item-required checkout-input" placeholder="Floor or Flat No." name="address[floor]" value="{{ old('address.floor', Arr::get($sessionCheckoutData, 'floor')) }}">
                    {!! Form::error('address.floor', $errors) !!}
                </div>
            </div>

            @if (EcommerceHelper::isZipCodeEnabled())
                <div class="col-12">
                    <div class="form-group mb-3 @if ($errors->has('address.zip_code')) has-error @endif">
                        <input id="address_zip_code" type="text" class="form-control address-control-item address-control-item-required checkout-input" placeholder="{{ __('Zip code') }}" name="address[zip_code]" value="{{ old('address.zip_code', Arr::get($sessionCheckoutData, 'zip_code')) }}">
                        {!! Form::error('address.zip_code', $errors) !!}
                    </div>
                </div>
            @endif
            <div class="col-12">
            <div class="form-group mb-4 @if ($errors->has('description')) has-error @endif">
                    <textarea name="description" id="description" rows="3" class="form-control" placeholder="Message&nbsp;(optional)">{{ old('description') }}</textarea>
                    {!! Form::error('description', $errors) !!}
                </div>
            </div>
        </div>
    </div>

    @if (!auth('customer')->check())
        <div class="form-group mb-3">
            <input type="checkbox" name="create_account" value="1" id="create_account" @if (empty($errors) && old('create_account') == 1) checked @endif>
            <label for="create_account" class="control-label" style="padding-left: 5px">{{ __('Register an account with above information?') }}</label>
        </div>

        <div class="password-group @if (!$errors->has('password') && !$errors->has('password_confirmation')) d-none @endif">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <input id="password" type="password" class="form-control checkout-input" name="password" autocomplete="password" placeholder="{{ __('Password') }}">
                        {!! Form::error('password', $errors) !!}
                    </div>
                </div>

                <div class="col-md-6 col-12">
                    <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                        <input id="password-confirm" type="password" class="form-control checkout-input" autocomplete="password-confirmation" placeholder="{{ __('Password confirmation') }}" name="password_confirmation">
                        {!! Form::error('password_confirmation', $errors) !!}
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('header')
    <link rel="stylesheet" href="{{ asset('vendor/core/core/base/libraries/intl-tel-input/css/intlTelInput.min.css') }}">
@endpush

@push('footer')
    <script src="{{ asset('vendor/core/core/base/libraries/intl-tel-input/js/intlTelInput.min.js') }}"></script>
    <script src="{{ asset('vendor/core/core/base/js/phone-number-field.js') }}"></script>
    <script type="text/javascript">
    var iti;
        function phonefield() {
            var x = document.getElementById("address_country").value;
            if (iti) {
                iti.destroy();
            }
            var input = document.querySelector("#address_phone");
            input.style.display = "block";
            iti = window.intlTelInput(input,({
                allowDropdown:false,
                separateDialCode:true,
                initialCountry:x,
            }));
        }
        
    </script>
@endpush
<style>
    .iti--separate-dial-code .iti__selected-flag{
        border-top-left-radius: 30px;
        border-bottom-left-radius: 30px;

    }
    .iti{
        display: block !important;
    }
</style>
