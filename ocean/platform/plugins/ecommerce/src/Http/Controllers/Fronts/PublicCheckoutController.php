<?php

namespace Botble\Ecommerce\Http\Controllers\Fronts;

use BaseHelper;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Enums\OrderAddressTypeEnum;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Enums\ShippingCodStatusEnum;
use Botble\Ecommerce\Enums\ShippingMethodEnum;
use Botble\Ecommerce\Enums\ShippingStatusEnum;
use Botble\Ecommerce\Events\OrderPlacedEvent;
use Botble\Ecommerce\Http\Requests\ApplyCouponRequest;
use Botble\Ecommerce\Http\Requests\CheckoutRequest;
use Botble\Ecommerce\Http\Requests\SaveCheckoutInformationRequest;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderHistory;
use Botble\Ecommerce\Repositories\Interfaces\AddressInterface;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Botble\Ecommerce\Repositories\Interfaces\DiscountInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderAddressInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderHistoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderProductInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShipmentInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShippingInterface;
use Botble\Ecommerce\Repositories\Interfaces\TaxInterface;
use Botble\Ecommerce\Services\Footprints\FootprinterInterface;
use Botble\Ecommerce\Services\HandleApplyCouponService;
use Botble\Ecommerce\Services\HandleApplyPromotionsService;
use Botble\Ecommerce\Services\HandleRemoveCouponService;
use Botble\Ecommerce\Services\HandleShippingFeeService;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Supports\PaymentHelper;
use Carbon\Carbon;
use Cart;
use EcommerceHelper;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use OptimizerHelper;
use OrderHelper;
use Theme;
use Validator;

class PublicCheckoutController
{
    protected TaxInterface $taxRepository;

    protected OrderInterface $orderRepository;

    protected OrderProductInterface $orderProductRepository;

    protected OrderAddressInterface $orderAddressRepository;

    protected AddressInterface $addressRepository;

    protected CustomerInterface $customerRepository;

    protected ShippingInterface $shippingRepository;

    protected OrderHistoryInterface $orderHistoryRepository;

    protected ProductInterface $productRepository;

    protected DiscountInterface $discountRepository;

    public function __construct(
        TaxInterface $taxRepository,
        OrderInterface $orderRepository,
        OrderProductInterface $orderProductRepository,
        OrderAddressInterface $orderAddressRepository,
        AddressInterface $addressRepository,
        CustomerInterface $customerRepository,
        ShippingInterface $shippingRepository,
        OrderHistoryInterface $orderHistoryRepository,
        ProductInterface $productRepository,
        DiscountInterface $discountRepository
    ) {
        $this->taxRepository = $taxRepository;
        $this->orderRepository = $orderRepository;
        $this->orderProductRepository = $orderProductRepository;
        $this->orderAddressRepository = $orderAddressRepository;
        $this->addressRepository = $addressRepository;
        $this->customerRepository = $customerRepository;
        $this->shippingRepository = $shippingRepository;
        $this->orderHistoryRepository = $orderHistoryRepository;
        $this->productRepository = $productRepository;
        $this->discountRepository = $discountRepository;

        OptimizerHelper::disable();
    }

    public function getCheckout(
        string $token,
        Request $request,
        BaseHttpResponse $response,
        HandleShippingFeeService $shippingFeeService,
        HandleApplyCouponService $applyCouponService,
        HandleRemoveCouponService $removeCouponService,
        HandleApplyPromotionsService $applyPromotionsService
    ) {
        if (! EcommerceHelper::isCartEnabled()) {
            abort(404);
        }

        if (! EcommerceHelper::isEnabledGuestCheckout() && ! auth('customer')->check()) {
            return $response->setNextUrl(route('customer.login'));
        }

        if ($token !== session('tracked_start_checkout')) {
            $order = $this->orderRepository->getFirstBy(['token' => $token, 'is_finished' => false]);

            if (! $order) {
                return $response->setNextUrl(route('public.index'));
            }
        }

        if (! $request->session()->has('error_msg') && $request->input('error') == 1 && $request->input('error_type') == 'payment') {
            $request->session()->flash('error_msg', __('Payment failed!'));
        }

        $sessionCheckoutData = OrderHelper::getOrderSessionData($token);

        $products = Cart::instance('cart')->products();
        if (! $products->count()) {
            return $response->setNextUrl(route('public.cart'));
        }

        foreach ($products as $product) {
            if ($product->isOutOfStock()) {
                return $response
                    ->setError()
                    ->setNextUrl(route('public.cart'))
                    ->setMessage(__('Product :product is out of stock!', ['product' => $product->original_product->name]));
            }
        }

        $digitalProducts = EcommerceHelper::countDigitalProducts($products);
        if ($digitalProducts && ! auth('customer')->check()) {
            return $response
                ->setError()
                ->setNextUrl(route('customer.login'))
                ->setMessage(__('Your shopping cart has digital product(s), so you need to sign in to continue!'));
        }

        $sessionCheckoutData = $this->processOrderData($token, $sessionCheckoutData, $request);

        $paymentMethod = $request->input('payment_method', session('selected_payment_method') ?: PaymentHelper::defaultPaymentMethod());
        if ($paymentMethod) {
            session()->put('selected_payment_method', $paymentMethod);
        }

        if (is_plugin_active('marketplace')) {
            [
                $sessionCheckoutData,
                $shipping,
                $defaultShippingMethod,
                $defaultShippingOption,
                $shippingAmount,
                $promotionDiscountAmount,
                $couponDiscountAmount,
            ] = apply_filters(PROCESS_CHECKOUT_ORDER_DATA_ECOMMERCE, $products, $token, $sessionCheckoutData, $request);
        } else {
            $promotionDiscountAmount = $applyPromotionsService->execute($token);

            $sessionCheckoutData['promotion_discount_amount'] = $promotionDiscountAmount;

            $couponDiscountAmount = 0;
            if (session()->has('applied_coupon_code')) {
                $couponDiscountAmount = Arr::get($sessionCheckoutData, 'coupon_discount_amount', 0);
            }

            $orderTotal = Cart::instance('cart')->rawTotal() - $promotionDiscountAmount - $couponDiscountAmount;
            $orderTotal = max($orderTotal, 0);

            $isAvailableShipping = EcommerceHelper::isAvailableShipping($products);

            $shipping = [];
            $defaultShippingMethod = $request->input('shipping_method', Arr::get($sessionCheckoutData, 'shipping_method', ShippingMethodEnum::DEFAULT));
            $defaultShippingOption = $request->input('shipping_option', Arr::get($sessionCheckoutData, 'shipping_option'));
            $shippingAmount = 0;

            if ($isAvailableShipping) {
                $origin = EcommerceHelper::getOriginAddress();
                $shippingData = EcommerceHelper::getShippingData($products, $sessionCheckoutData, $origin, $orderTotal, $paymentMethod);

                $shipping = $shippingFeeService->execute($shippingData);

                foreach ($shipping as $key => &$shipItem) {
                    if (get_shipping_setting('free_ship', $key)) {
                        foreach ($shipItem as &$subShippingItem) {
                            Arr::set($subShippingItem, 'price', 0);
                        }
                    }
                }

                if ($shipping) {
                    if (! $defaultShippingMethod) {
                        $defaultShippingMethod = old(
                            'shipping_method',
                            Arr::get($sessionCheckoutData, 'shipping_method', Arr::first(array_keys($shipping)))
                        );
                    }
                    if (! $defaultShippingOption) {
                        $defaultShippingOption = old('shipping_option', Arr::get($sessionCheckoutData, 'shipping_option', $defaultShippingOption));
                    }
                }

                $shippingAmount = Arr::get($shipping, $defaultShippingMethod . '.' . $defaultShippingOption . '.price', 0);

                Arr::set($sessionCheckoutData, 'shipping_method', $defaultShippingMethod);
                Arr::set($sessionCheckoutData, 'shipping_option', $defaultShippingOption);
                Arr::set($sessionCheckoutData, 'shipping_amount', $shippingAmount);

                OrderHelper::setOrderSessionData($token, $sessionCheckoutData);
            }

            if (session()->has('applied_coupon_code')) {
                if (! $request->input('applied_coupon')) {
                    $discount = $applyCouponService->getCouponData(
                        session('applied_coupon_code'),
                        $sessionCheckoutData
                    );
                    if (empty($discount)) {
                        $removeCouponService->execute();
                    } else {
                        $shippingAmount = Arr::get($sessionCheckoutData, 'is_free_shipping') ? 0 : $shippingAmount;
                    }
                } else {
                    $shippingAmount = Arr::get($sessionCheckoutData, 'is_free_shipping') ? 0 : $shippingAmount;
                }
            }

            $sessionCheckoutData['is_available_shipping'] = $isAvailableShipping;

            if (! $sessionCheckoutData['is_available_shipping']) {
                $shippingAmount = 0;
            }
        }

        $data = compact(
            'token',
            'shipping',
            'defaultShippingMethod',
            'defaultShippingOption',
            'shippingAmount',
            'promotionDiscountAmount',
            'couponDiscountAmount',
            'sessionCheckoutData',
            'products',
        );

        $checkoutView = Theme::getThemeNamespace('views.ecommerce.orders.checkout');

        if (view()->exists($checkoutView)) {
            return view($checkoutView, $data);
        }

        return view('plugins/ecommerce::orders.checkout', $data);
    }

    protected function processOrderData(string $token, array $sessionData, Request $request, bool $finished = false): array
    {
        if ($request->has('billing_address_same_as_shipping_address')) {
            $sessionData['billing_address_same_as_shipping_address'] = $request->input('billing_address_same_as_shipping_address');
        }

        if ($request->has('billing_address')) {
            $sessionData['billing_address'] = $request->input('billing_address');
        }

        if ($request->input('address', [])) {
            if (! isset($sessionData['created_account']) && $request->input('create_account') == 1) {
                $validator = Validator::make($request->input(), [
                    'password' => 'required|min:6',
                    'password_confirmation' => 'required|same:password',
                    'address.email' => 'required|max:60|min:6|email|unique:ec_customers,email',
                    'address.name' => 'required|min:3|max:120',
                ]);

                if (! $validator->fails()) {
                    $customer = $this->customerRepository->createOrUpdate([
                        'name' => BaseHelper::clean($request->input('address.name'). ' '. $request->input('address.lname')),
                        'email' => BaseHelper::clean($request->input('address.email')),
                        'phone' => BaseHelper::clean($request->input('address.phone')),
                        'password' => Hash::make($request->input('password')),
                    ]);

                    auth('customer')->attempt([
                        'email' => $request->input('address.email'),
                        'password' => $request->input('password'),
                    ], true);

                    event(new Registered($customer));

                    $sessionData['created_account'] = true;

                    $address = $this->addressRepository->createOrUpdate($request->input('address') + [
                            'customer_id' => $customer->id,
                            'is_default' => true,
                        ]);

                    $request->merge(['address.address_id' => $address->id]);
                    $sessionData['address_id'] = $address->id;
                }
            }

            if ($finished && auth('customer')->check() && (auth('customer')->user()->addresses()->count() == 0 || $request->input('address.address_id') == 'new')) {
                $address = $this->addressRepository->createOrUpdate($request->input('address', []) +
                    ['customer_id' => auth('customer')->id(), 'is_default' => auth('customer')->user()->addresses()->count() == 0]);

                $request->merge(['address.address_id' => $address->id]);
                $sessionData['address_id'] = $address->id;
            }
        }

        $address = null;

        if ($request->input('address.address_id') && $request->input('address.address_id') !== 'new') {
            $address = $this->addressRepository->findById($request->input('address.address_id'));
            if (! empty($address)) {
                $sessionData['address_id'] = $address->id;
                $sessionData['created_order_address_id'] = $address->id;
            }
        } elseif (auth('customer')->check() && ! Arr::get($sessionData, 'address_id')) {
            $address = $this->addressRepository->getFirstBy([
                'customer_id' => auth('customer')->id(),
                'is_default' => true,
            ]);

            if ($address) {
                $sessionData['address_id'] = $address->id;
            }
        }

        if (Arr::get($sessionData, 'address_id') && Arr::get($sessionData, 'address_id') !== 'new') {
            $address = $this->addressRepository->findById(Arr::get($sessionData, 'address_id'));

            if ($address) {
                $address->fill($request->input('address', []));
                $address->name= $request->input('address.name'). ' ' . $request->input('address.lname');
                $address->save();
            } else {
                $address = $this->addressRepository->createOrUpdate($request->input('address', []));
                $address->name= $request->input('address.name'). ' ' . $request->input('address.lname');
                $address->save();
            }
        }

        $addressData = [
            'billing_address_same_as_shipping_address' => Arr::get($sessionData, 'billing_address_same_as_shipping_address', true),
            'billing_address' => Arr::get($sessionData, 'billing_address', []),
        ];

        if (! empty($address)) {
            $addressData = [
                'name' => $address->name,
                'phone' => $address->phone,
                'email' => $address->email,
                'country' => $address->country,
                'state' => $address->state,
                'city' => $address->city,
                'area' => $address->area,
                'address' => $address->address,
                'building' => $address->building,
                'floor' => $address->floor,
                'zip_code' => $address->zip_code,
                'address_id' => $address->id,
            ];
        } elseif ((array)$request->input('address', [])) {
            $addressData = (array)$request->input('address', []);
        }

        foreach ($addressData as $key => $addressItem) {
            if (! is_string($addressItem)) {
                continue;
            }

            $addressData[$key] = BaseHelper::clean($addressItem);
        }

        if ($addressData && ! empty($addressData['name']) && (EcommerceHelper::isPhoneFieldOptionalAtCheckout() || ! empty($addressData['phone'])) && ! empty($addressData['address'])) {
            $addressData['billing_address_same_as_shipping_address'] = Arr::get($sessionData, 'billing_address_same_as_shipping_address', true);
            $addressData['billing_address'] = Arr::get($sessionData, 'billing_address');
        }

        $sessionData = array_merge($sessionData, $addressData);

        if (is_plugin_active('marketplace')) {
            $products = Cart::instance('cart')->products();

            $sessionData = apply_filters(
                HANDLE_PROCESS_ORDER_DATA_ECOMMERCE,
                $products,
                $token,
                $sessionData,
                $request
            );

            OrderHelper::setOrderSessionData($token, $sessionData);

            return $sessionData;
        }

        if (! isset($sessionData['created_order'])) {
            $currentUserId = 0;
            if (auth('customer')->check()) {
                $currentUserId = auth('customer')->id();
            }

            $request->merge([
                'amount' => Cart::instance('cart')->rawTotal(),
                'user_id' => $currentUserId,
                'shipping_method' => $request->input('shipping_method', ShippingMethodEnum::DEFAULT),
                'shipping_option' => $request->input('shipping_option'),
                'shipping_amount' => 0,
                'tax_amount' => Cart::instance('cart')->rawTax(),
                'sub_total' => Cart::instance('cart')->rawSubTotal(),
                'coupon_code' => session()->get('applied_coupon_code'),
                'discount_amount' => 0,
                'status' => OrderStatusEnum::PENDING,
                'is_finished' => false,
                'token' => $token,
            ]);

            $order = $this->orderRepository->getFirstBy(compact('token'));

            $order = $this->createOrderFromData($request->input(), $order);

            $sessionData['created_order'] = true;
            $sessionData['created_order_id'] = $order->id;
        }

        if (! empty($address)) {
            $addressData['order_id'] = $sessionData['created_order_id'];
        } elseif ((array)$request->input('address', [])) {
            $addressData = array_merge(
                ['order_id' => $sessionData['created_order_id']],
                (array)$request->input('address', [])
            );
        }

        if ($addressData && ! empty($addressData['name']) && (EcommerceHelper::isPhoneFieldOptionalAtCheckout() || ! empty($addressData['phone'])) && ! empty($addressData['address'])) {
            if (! isset($sessionData['created_order_address'])) {
                $createdOrderAddress = $this->createOrderAddress(
                    $addressData,
                    Arr::get($addressData, 'order_id')
                );
                if ($createdOrderAddress) {
                    $sessionData['created_order_address'] = true;
                    $sessionData['created_order_address_id'] = $createdOrderAddress->id;
                }
            } elseif (! empty($sessionData['created_order_id'])) {
                $this->createOrderAddress($addressData, $sessionData['created_order_id']);
            }
        }

        if (! isset($sessionData['created_order_product'])) {
            $weight = 0;
            foreach (Cart::instance('cart')->content() as $cartItem) {
                $product = $this->productRepository->findById($cartItem->id);
                if ($product) {
                    if ($product->weight) {
                        $weight += $product->weight * $cartItem->qty;
                    }
                }
            }

            $weight = EcommerceHelper::validateOrderWeight($weight);

            $this->orderProductRepository->deleteBy(['order_id' => $sessionData['created_order_id']]);

            foreach (Cart::instance('cart')->content() as $cartItem) {
                $product = $this->productRepository->findById($cartItem->id);

                $data = [
                    'order_id' => $sessionData['created_order_id'],
                    'product_id' => $cartItem->id,
                    'product_name' => $cartItem->name,
                    'product_image' => $product->original_product->image,
                    'qty' => $cartItem->qty,
                    'weight' => $weight,
                    'price' => $cartItem->price,
                    'tax_amount' => $cartItem->tax,
                    'options' => [],
                    'product_type' => $product ? $product->product_type : null,
                ];

                if ($cartItem->options->extras) {
                    $data['options'] = $cartItem->options->extras;
                }

                if ($cartItem->options['options']) {
                    $data['product_options'] = $cartItem->options['options'];
                }

                $this->orderProductRepository->create($data);
            }

            $sessionData['created_order_product'] = Cart::instance('cart')->getLastUpdatedAt();
        }

        OrderHelper::setOrderSessionData($token, $sessionData);

        return $sessionData;
    }

    protected function createOrderAddress(array $data, ?int $orderId = null)
    {
        if ($orderId) {
            $this->storeOrderBillingAddress($data, $orderId);

            return $this->orderAddressRepository->createOrUpdate($data, ['order_id' => $orderId, 'type' => OrderAddressTypeEnum::SHIPPING]);
        }

        $validator = Validator::make($data, EcommerceHelper::getCustomerAddressValidationRules());

        if ($validator->fails()) {
            return false;
        }

        $this->storeOrderBillingAddress($data);

        return $this->orderAddressRepository->create($data);
    }

    protected function storeOrderBillingAddress(array $data, ?int $orderId = null)
    {
        if (isset($data['billing_address_same_as_shipping_address']) && ! $data['billing_address_same_as_shipping_address']) {
            $billingAddressData = $data['billing_address'];
            $billingAddressData['order_id'] = $orderId ?: Arr::get($data, 'order_id');
            $billingAddressData['type'] = OrderAddressTypeEnum::BILLING;

            $this->orderAddressRepository->createOrUpdate($billingAddressData, ['order_id' => $orderId, 'type' => OrderAddressTypeEnum::BILLING]);
        } else {
            $this->orderAddressRepository->deleteBy([
                'order_id' => $orderId,
                'type' => OrderAddressTypeEnum::BILLING,
            ]);
        }
    }

    public function postSaveInformation(
        string $token,
        SaveCheckoutInformationRequest $request,
        BaseHttpResponse $response,
        HandleApplyCouponService $applyCouponService,
        HandleRemoveCouponService $removeCouponService
    ) {
        if (! EcommerceHelper::isCartEnabled()) {
            abort(404);
        }

        if ($token !== session('tracked_start_checkout')) {
            $order = $this->orderRepository->getFirstBy(['token' => $token, 'is_finished' => false]);

            if (! $order) {
                return $response->setNextUrl(route('public.index'));
            }
        }

        if ($paymentMethod = $request->input('payment_method')) {
            session()->put('selected_payment_method', $paymentMethod);
        }

        if (is_plugin_active('marketplace')) {
            $sessionData = array_merge(OrderHelper::getOrderSessionData($token), $request->input('address'));

            $sessionData = apply_filters(
                PROCESS_POST_SAVE_INFORMATION_CHECKOUT_ECOMMERCE,
                $sessionData,
                $request,
                $token
            );
        } else {
            $sessionData = array_merge(OrderHelper::getOrderSessionData($token), $request->input('address'));
            OrderHelper::setOrderSessionData($token, $sessionData);
            if (session()->has('applied_coupon_code')) {
                $discount = $applyCouponService->getCouponData(session('applied_coupon_code'), $sessionData);
                if (empty($discount)) {
                    $removeCouponService->execute();
                }
            }
        }

        $sessionData = $this->processOrderData($token, $sessionData, $request);

        return $response->setData($sessionData);
    }

    public function postCheckout(
        string $token,
        CheckoutRequest $request,
        BaseHttpResponse $response,
        HandleShippingFeeService $shippingFeeService,
        HandleApplyCouponService $applyCouponService,
        HandleRemoveCouponService $removeCouponService,
        HandleApplyPromotionsService $handleApplyPromotionsService
    ) {
        if (! EcommerceHelper::isCartEnabled()) {
            abort(404);
        }

        if (! EcommerceHelper::isEnabledGuestCheckout() && ! auth('customer')->check()) {
            return $response->setNextUrl(route('customer.login'));
        }

        if (! Cart::instance('cart')->count()) {
            return $response
                ->setError()
                ->setMessage(__('No products in cart'));
        }

        $products = Cart::instance('cart')->products();

        $digitalProducts = EcommerceHelper::countDigitalProducts($products);
        if ($digitalProducts && ! auth('customer')->check()) {
            return $response
                ->setError()
                ->setNextUrl(route('customer.login'))
                ->setMessage(__('Your shopping cart has digital product(s), so you need to sign in to continue!'));
        }

        if (EcommerceHelper::getMinimumOrderAmount() > Cart::instance('cart')->rawSubTotal()) {
            return $response
                ->setError()
                ->setMessage(__('Minimum order amount is :amount, you need to buy more :more to place an order!', [
                    'amount' => format_price(EcommerceHelper::getMinimumOrderAmount()),
                    'more' => format_price(EcommerceHelper::getMinimumOrderAmount() - Cart::instance('cart')
                            ->rawSubTotal()),
                ]));
        }

        $sessionData = OrderHelper::getOrderSessionData($token);

        $sessionData = $this->processOrderData($token, $sessionData, $request, true);

        foreach ($products as $product) {
            if ($product->isOutOfStock()) {
                return $response
                    ->setError()
                    ->setMessage(__('Product :product is out of stock!', ['product' => $product->original_product->name]));
            }
        }

        $paymentMethod = $request->input('payment_method', session('selected_payment_method'));
        if ($paymentMethod) {
            session()->put('selected_payment_method', $paymentMethod);
        }

        if (is_plugin_active('marketplace')) {
            return apply_filters(
                HANDLE_PROCESS_POST_CHECKOUT_ORDER_DATA_ECOMMERCE,
                $products,
                $request,
                $token,
                $sessionData,
                $response
            );
        }

        $isAvailableShipping = EcommerceHelper::isAvailableShipping($products);

        $shippingMethodInput = $request->input('shipping_method', ShippingMethodEnum::DEFAULT);

        $promotionDiscountAmount = $handleApplyPromotionsService->execute($token);
        $couponDiscountAmount = Arr::get($sessionData, 'coupon_discount_amount');

        $shippingAmount = 0;

        $shippingData = [];
        if ($isAvailableShipping) {
            $orderTotal = Cart::instance('cart')->rawTotal() - $promotionDiscountAmount - $couponDiscountAmount;
            $origin = EcommerceHelper::getOriginAddress();
            $shippingData = EcommerceHelper::getShippingData($products, $sessionData, $origin, $orderTotal, $paymentMethod);

            $shippingMethodData = $shippingFeeService->execute(
                $shippingData,
                $shippingMethodInput,
                $request->input('shipping_option')
            );

            $shippingMethod = Arr::first($shippingMethodData);
            if (! $shippingMethod) {
                throw ValidationException::withMessages([
                    'shipping_method' => trans('validation.exists', ['attribute' => trans('plugins/ecommerce::shipping.shipping_method')]),
                ]);
            }

            $shippingAmount = Arr::get($shippingMethod, 'price', 0);

            if (get_shipping_setting('free_ship', $shippingMethodInput)) {
                $shippingAmount = 0;
            }
        }

        if (session()->has('applied_coupon_code')) {
            $discount = $applyCouponService->getCouponData(session('applied_coupon_code'), $sessionData);
            if (empty($discount)) {
                $removeCouponService->execute();
            } else {
                $shippingAmount = Arr::get($sessionData, 'is_free_shipping') ? 0 : $shippingAmount;
            }
        }

        $currentUserId = 0;
        if (auth('customer')->check()) {
            $currentUserId = auth('customer')->id();
        }

        $amount = Cart::instance('cart')->rawTotal() + (float)$shippingAmount - $promotionDiscountAmount - $couponDiscountAmount;

        $request->merge([
            'amount' => $amount ?: 0,
            'currency' => $request->input('currency', strtoupper(get_application_currency()->title)),
            'user_id' => $currentUserId,
            'shipping_method' => $isAvailableShipping ? $shippingMethodInput : '',
            'shipping_option' => $isAvailableShipping ? $request->input('shipping_option') : null,
            'shipping_amount' => (float)$shippingAmount,
            'tax_amount' => Cart::instance('cart')->rawTax(),
            'sub_total' => Cart::instance('cart')->rawSubTotal(),
            'coupon_code' => session()->get('applied_coupon_code'),
            'discount_amount' => $promotionDiscountAmount + $couponDiscountAmount,
            'status' => OrderStatusEnum::PENDING,
            'token' => $token,
        ]);

        $order = $this->orderRepository->getFirstBy(compact('token'));

        $order = $this->createOrderFromData($request->input(), $order);

        $this->orderHistoryRepository->createOrUpdate([
            'action' => 'create_order_from_payment_page',
            'description' => __('Order was created from checkout page'),
            'order_id' => $order->id,
        ]);

        if ($isAvailableShipping) {
            app(ShipmentInterface::class)->createOrUpdate([
                'order_id' => $order->id,
                'user_id' => 0,
                'weight' => $shippingData ? Arr::get($shippingData, 'weight') : 0,
                'cod_amount' => ($order->payment->id && $order->payment->status != PaymentStatusEnum::COMPLETED) ? $order->amount : 0,
                'cod_status' => ShippingCodStatusEnum::PENDING,
                'type' => $order->shipping_method,
                'status' => ShippingStatusEnum::PENDING,
                'price' => $order->shipping_amount,
                'rate_id' => $shippingData ? Arr::get($shippingMethod, 'id', '') : '',
                'shipment_id' => $shippingData ? Arr::get($shippingMethod, 'shipment_id', '') : '',
                'shipping_company_name' => $shippingData ? Arr::get($shippingMethod, 'company_name', '') : '',
            ]);
        }

        $discount = $this->discountRepository
            ->getModel()
            ->where('code', session()->get('applied_coupon_code'))
            ->where('type', 'coupon')
            ->where('start_date', '<=', Carbon::now())
            ->where(function ($query) {
                /**
                 * @var Builder $query
                 */
                return $query
                    ->whereNull('end_date')
                    ->orWhere('end_date', '>', Carbon::now());
            })
            ->first();

        if (! empty($discount)) {
            $discount->total_used++;
            $this->discountRepository->createOrUpdate($discount);
        }

        $this->orderProductRepository->deleteBy(['order_id' => $order->id]);

        foreach (Cart::instance('cart')->content() as $cartItem) {
            $product = $this->productRepository->findById($cartItem->id);

            $data = [
                'order_id' => $order->id,
                'product_id' => $cartItem->id,
                'product_name' => $cartItem->name,
                'product_image' => $product->original_product->image,
                'qty' => $cartItem->qty,
                'weight' => $shippingData ? Arr::get($shippingData, 'weight') : 0,
                'price' => $cartItem->price,
                'tax_amount' => $cartItem->tax,
                'options' => [],
                'product_type' => $product ? $product->product_type : null,
            ];

            if ($cartItem->options->extras) {
                $data['options'] = $cartItem->options->extras;
            }

            if ($cartItem->options['options']) {
                $data['product_options'] = $cartItem->options['options'];
            }

            $this->orderProductRepository->create($data);
        }

        $request->merge([
            'order_id' => $order->id,
        ]);

        $paymentData = [
            'error' => false,
            'message' => false,
            'amount' => (float)format_price($order->amount, null, true),
            'currency' => strtoupper(get_application_currency()->title),
            'type' => $request->input('payment_method'),
            'charge_id' => null,
        ];

        $paymentData = apply_filters(FILTER_ECOMMERCE_PROCESS_PAYMENT, $paymentData, $request);
        if($paymentData['type'] == 'ngenius' || $paymentData['type'] == 'apple_pay'){
            cache()->put('ngenius_method', $paymentData['type'], 60 * 60);
            return redirect()->route('ngenius.accessories.id',['id'=>$order->id]);
        }

        if ($checkoutUrl = Arr::get($paymentData, 'checkoutUrl')) {
            return $response
                ->setError($paymentData['error'])
                ->setNextUrl($checkoutUrl)
                ->setData(['checkoutUrl' => $checkoutUrl])
                ->withInput()
                ->setMessage($paymentData['message']);
        }

        if ($paymentData['error'] || ! $paymentData['charge_id']) {
            return $response
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL($token))
                ->withInput()
                ->setMessage($paymentData['message'] ?: __('Checkout error!'));
        }

        return $response
            ->setNextUrl(PaymentHelper::getRedirectURL($token))
            ->setMessage(__('Checkout successfully!'));
    }

    public function getCheckoutSuccess(string $token, BaseHttpResponse $response)
    {
        if (! EcommerceHelper::isCartEnabled()) {
            abort(404);
        }

        $order = $this->orderRepository->getFirstBy(['token' => $token], [], ['address', 'products']);

        if (! $order) {
            abort(404);
        }

        if (! $order->payment_id) {
            return $response
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL())
                ->setMessage(__('Payment failed!'));
        }

        if (is_plugin_active('marketplace')) {
            return apply_filters(PROCESS_GET_CHECKOUT_SUCCESS_IN_ORDER, $token, $response);
        }

        if (! $order->is_finished) {
            event(new OrderPlacedEvent($order));

            $order->is_finished = true;

            if (EcommerceHelper::isOrderAutoConfirmedEnabled()) {
                $order->is_confirmed = true;
            }

            $order->save();

            OrderHelper::decreaseProductQuantity($order);

            OrderHelper::clearSessions($token);

            if (EcommerceHelper::isOrderAutoConfirmedEnabled()) {
                OrderHistory::create([
                    'action' => 'confirm_order',
                    'description' => trans('plugins/ecommerce::order.order_was_verified_by'),
                    'order_id' => $order->id,
                    'user_id' => 0,
                ]);
            }
        }

        $products = collect([]);

        $productsIds = $order->products->pluck('product_id')->all();

        if (! empty($productsIds)) {
            $products = get_products([
                'condition' => [
                    ['ec_products.id', 'IN', $productsIds],
                ],
                'select' => [
                    'ec_products.id',
                    'ec_products.images',
                    'ec_products.name',
                    'ec_products.price',
                    'ec_products.sale_price',
                    'ec_products.sale_type',
                    'ec_products.start_date',
                    'ec_products.end_date',
                    'ec_products.sku',
                    'ec_products.order',
                    'ec_products.created_at',
                    'ec_products.is_variation',
                ],
                'with' => [
                    'variationProductAttributes',
                ],
            ]);
        }

        return view('plugins/ecommerce::orders.thank-you', compact('order', 'products'));
    }

    public function postApplyCoupon(
        ApplyCouponRequest $request,
        HandleApplyCouponService $handleApplyCouponService,
        BaseHttpResponse $response
    ) {
        if (! EcommerceHelper::isCartEnabled()) {
            abort(404);
        }
        $result = [
            'error' => false,
            'message' => '',
        ];
        if (is_plugin_active('marketplace')) {
            $result = apply_filters(HANDLE_POST_APPLY_COUPON_CODE_ECOMMERCE, $result, $request);
        } else {
            $result = $handleApplyCouponService->execute($request->input('coupon_code'));
        }

        if ($result['error']) {
            return $response
                ->setError()
                ->withInput()
                ->setMessage($result['message']);
        }

        $couponCode = $request->input('coupon_code');

        return $response
            ->setMessage(__('Applied coupon ":code" successfully!', ['code' => $couponCode]));
    }

    public function postRemoveCoupon(
        Request $request,
        HandleRemoveCouponService $removeCouponService,
        BaseHttpResponse $response
    ) {
        if (! EcommerceHelper::isCartEnabled()) {
            abort(404);
        }

        if (is_plugin_active('marketplace')) {
            $products = Cart::instance('cart')->products();
            $result = apply_filters(HANDLE_POST_REMOVE_COUPON_CODE_ECOMMERCE, $products, $request);
        } else {
            $result = $removeCouponService->execute();
        }

        if ($result['error']) {
            if ($request->ajax()) {
                return $result;
            }

            return $response
                ->setError()
                ->setData($result)
                ->setMessage($result['message']);
        }

        return $response
            ->setMessage(__('Removed coupon :code successfully!', ['code' => session('applied_coupon_code')]));
    }

    public function getCheckoutRecover(string $token, Request $request, BaseHttpResponse $response)
    {
        if (! EcommerceHelper::isCartEnabled()) {
            abort(404);
        }

        if (! EcommerceHelper::isEnabledGuestCheckout() && ! auth('customer')->check()) {
            return $response->setNextUrl(route('customer.login'));
        }

        if (is_plugin_active('marketplace')) {
            return apply_filters(PROCESS_GET_CHECKOUT_RECOVER_ECOMMERCE, $token, $request);
        }

        $order = $this->orderRepository
            ->getFirstBy([
                'token' => $token,
                'is_finished' => false,
            ], [], ['products', 'address']);

        if (! $order) {
            abort(404);
        }

        if (session()->has('tracked_start_checkout') && session('tracked_start_checkout') == $token) {
            $sessionCheckoutData = OrderHelper::getOrderSessionData($token);
        } else {
            session(['tracked_start_checkout' => $token]);
            $sessionCheckoutData = [
                'name' => $order->address->name,
                'email' => $order->address->email,
                'phone' => $order->address->phone,
                'address' => $order->address->address,
                'country' => $order->address->country,
                'state' => $order->address->state,
                'city' => $order->address->city,
                'zip_code' => $order->address->zip_code,
                'shipping_method' => $order->shipping_method,
                'shipping_option' => $order->shipping_option,
                'shipping_amount' => $order->shipping_amount,
            ];
        }

        Cart::instance('cart')->destroy();
        foreach ($order->products as $orderProduct) {
            $request->merge(['qty' => $orderProduct->qty]);

            $product = $this->productRepository->findById($orderProduct->product_id);
            if ($product) {
                OrderHelper::handleAddCart($product, $request);
            }
        }

        OrderHelper::setOrderSessionData($token, $sessionCheckoutData);

        return $response->setNextUrl(route('public.checkout.information', $token))
            ->setMessage(__('You have recovered from previous orders!'));
    }

    protected function createOrderFromData(array $data, ?Order $order): Order|null|false
    {
        $data['is_finished'] = false;

        if ($order) {
            $order->fill($data);
            $order = $this->orderRepository->createOrUpdate($order);
        } else {
            $order = $this->orderRepository->createOrUpdate($data);
        }

        if (! $order->referral()->count()) {
            $referrals = app(FootprinterInterface::class)->getFootprints();

            if ($referrals) {
                $order->referral()->create($referrals);
            }
        }

        return $order;
    }
}
