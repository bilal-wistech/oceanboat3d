<?php

namespace Botble\Ecommerce\Providers;

use Cart;
use ApiHelper;
use SeoHelper;
use SlugHelper;
use EmailHandler;
use SocialService;
use EcommerceHelper;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Botble\Ecommerce\Models\Tax;
use Botble\Ecommerce\Models\Brand;
use Botble\Ecommerce\Models\Order;
use Botble\Payment\Models\Payment;
use Botble\Ecommerce\Models\Option;
use Botble\Ecommerce\Models\Review;
use Botble\Ecommerce\Models\Address;
use Botble\Ecommerce\Models\Invoice;
use Botble\Ecommerce\Models\Product;
use Illuminate\Support\Facades\File;
use Botble\Ecommerce\Models\Currency;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Discount;
use Botble\Ecommerce\Models\Shipment;
use Botble\Ecommerce\Models\Shipping;
use Botble\Ecommerce\Models\Wishlist;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Botble\Ecommerce\Models\FlashSale;
use Illuminate\Foundation\AliasLoader;
use Botble\Ecommerce\Models\ProductTag;
use Illuminate\Support\ServiceProvider;
use Botble\Ecommerce\Facades\CartFacade;
use Botble\Ecommerce\Models\OptionValue;
use Botble\Ecommerce\Models\OrderReturn;
use Botble\Ecommerce\Models\GlobalOption;
use Botble\Ecommerce\Models\OrderAddress;
use Botble\Ecommerce\Models\OrderHistory;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\ProductLabel;
use Botble\Ecommerce\Models\ShippingRule;
use Botble\Ecommerce\Models\StoreLocator;
use Botble\Ecommerce\Models\GroupedProduct;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Routing\Events\RouteMatched;
use Botble\Ecommerce\Facades\CurrencyFacade;
use Botble\Ecommerce\Models\OrderReturnItem;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Ecommerce\Models\ShipmentHistory;
use Botble\Ecommerce\Models\ProductAttribute;
use Botble\Ecommerce\Models\ProductVariation;
use Botble\Ecommerce\Models\ShippingRuleItem;
use Botble\Ecommerce\Models\GlobalOptionValue;
use Botble\Ecommerce\Models\ProductCollection;
use Botble\Ecommerce\Supports\EcommerceWidget;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Ecommerce\Facades\OrderHelperFacade;
use Botble\Ecommerce\Models\ProductAttributeSet;
use Botble\Ecommerce\Facades\InvoiceHelperFacade;
use Botble\Ecommerce\Models\ProductVariationItem;
use Botble\Ecommerce\Facades\EcommerceHelperFacade;
use Botble\Ecommerce\Facades\OrderReturnHelperFacade;
use Botble\Ecommerce\Services\Footprints\Footprinter;
use Botble\Ecommerce\Services\HandleApplyCouponService;
use Botble\Ecommerce\Http\Middleware\RedirectIfCustomer;
use Botble\Ecommerce\Services\Footprints\TrackingFilter;
use Botble\Ecommerce\Services\Footprints\TrackingLogger;
use Botble\Ecommerce\Services\HandleRemoveCouponService;
use Botble\Ecommerce\Commands\SendSavedBoatsEmailCommand;
use Botble\Ecommerce\Facades\ProductCategoryHelperFacade;
use Botble\Ecommerce\Repositories\Eloquent\TaxRepository;
use Botble\Ecommerce\Repositories\Interfaces\TaxInterface;
use Botble\Ecommerce\Http\Middleware\RedirectIfNotCustomer;
use Botble\Ecommerce\Repositories\Caches\TaxCacheDecorator;
use Botble\Ecommerce\Repositories\Eloquent\BrandRepository;
use Botble\Ecommerce\Repositories\Eloquent\OrderRepository;
use Botble\Ecommerce\Repositories\Eloquent\ReviewRepository;
use Botble\Ecommerce\Repositories\Interfaces\BrandInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Ecommerce\Commands\SendAbandonedCartsEmailCommand;
use Botble\Ecommerce\Repositories\Caches\BrandCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\OrderCacheDecorator;
use Botble\Ecommerce\Repositories\Eloquent\AddressRepository;
use Botble\Ecommerce\Repositories\Eloquent\InvoiceRepository;
use Botble\Ecommerce\Repositories\Eloquent\ProductRepository;
use Botble\Ecommerce\Repositories\Interfaces\ReviewInterface;
use Botble\LanguageAdvanced\Supports\LanguageAdvancedManager;
use Botble\Ecommerce\Repositories\Caches\ReviewCacheDecorator;
use Botble\Ecommerce\Repositories\Eloquent\CurrencyRepository;
use Botble\Ecommerce\Repositories\Eloquent\CustomerRepository;
use Botble\Ecommerce\Repositories\Eloquent\DiscountRepository;
use Botble\Ecommerce\Repositories\Eloquent\ShipmentRepository;
use Botble\Ecommerce\Repositories\Eloquent\ShippingRepository;
use Botble\Ecommerce\Repositories\Eloquent\WishlistRepository;
use Botble\Ecommerce\Repositories\Interfaces\AddressInterface;
use Botble\Ecommerce\Repositories\Interfaces\InvoiceInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Ecommerce\Services\Footprints\FootprinterInterface;
use Botble\Ecommerce\Repositories\Caches\AddressCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\InvoiceCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\ProductCacheDecorator;
use Botble\Ecommerce\Repositories\Eloquent\FlashSaleRepository;
use Botble\Ecommerce\Repositories\Interfaces\CurrencyInterface;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Botble\Ecommerce\Repositories\Interfaces\DiscountInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShipmentInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShippingInterface;
use Botble\Ecommerce\Repositories\Interfaces\WishlistInterface;
use Botble\Ecommerce\Repositories\Caches\CurrencyCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\CustomerCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\DiscountCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\ShipmentCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\ShippingCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\WishlistCacheDecorator;
use Botble\Ecommerce\Repositories\Eloquent\ProductTagRepository;
use Botble\Ecommerce\Repositories\Interfaces\FlashSaleInterface;
use Botble\Ecommerce\Http\Middleware\CaptureFootprintsMiddleware;
use Botble\Ecommerce\Repositories\Caches\FlashSaleCacheDecorator;
use Botble\Ecommerce\Repositories\Eloquent\OrderReturnRepository;
use Botble\Ecommerce\Repositories\Interfaces\ProductTagInterface;
use Botble\Ecommerce\Services\Footprints\TrackingFilterInterface;
use Botble\Ecommerce\Services\Footprints\TrackingLoggerInterface;
use Botble\Ecommerce\Repositories\Caches\ProductTagCacheDecorator;
use Botble\Ecommerce\Repositories\Eloquent\GlobalOptionRepository;
use Botble\Ecommerce\Repositories\Eloquent\OrderAddressRepository;
use Botble\Ecommerce\Repositories\Eloquent\OrderHistoryRepository;
use Botble\Ecommerce\Repositories\Eloquent\OrderProductRepository;
use Botble\Ecommerce\Repositories\Eloquent\ProductLabelRepository;
use Botble\Ecommerce\Repositories\Eloquent\ShippingRuleRepository;
use Botble\Ecommerce\Repositories\Eloquent\StoreLocatorRepository;
use Botble\Ecommerce\Repositories\Interfaces\OrderReturnInterface;
use Botble\Ecommerce\Repositories\Caches\OrderReturnCacheDecorator;
use Botble\Ecommerce\Repositories\Interfaces\GlobalOptionInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderAddressInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderHistoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderProductInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductLabelInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShippingRuleInterface;
use Botble\Ecommerce\Repositories\Interfaces\StoreLocatorInterface;
use Botble\Ecommerce\Repositories\Caches\GlobalOptionCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\OrderAddressCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\OrderHistoryCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\OrderProductCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\ProductLabelCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\ShippingRuleCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\StoreLocatorCacheDecorator;
use Botble\Ecommerce\Repositories\Eloquent\GroupedProductRepository;
use Botble\Ecommerce\Repositories\Eloquent\OrderReturnItemRepository;
use Botble\Ecommerce\Repositories\Eloquent\ProductCategoryRepository;
use Botble\Ecommerce\Repositories\Eloquent\ShipmentHistoryRepository;
use Botble\Ecommerce\Repositories\Interfaces\GroupedProductInterface;
use Botble\Ecommerce\Repositories\Caches\GroupedProductCacheDecorator;
use Botble\Ecommerce\Repositories\Eloquent\ProductAttributeRepository;
use Botble\Ecommerce\Repositories\Eloquent\ProductVariationRepository;
use Botble\Ecommerce\Repositories\Eloquent\ShippingRuleItemRepository;
use Botble\Ecommerce\Repositories\Interfaces\OrderReturnItemInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductCategoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShipmentHistoryInterface;
use Botble\Ecommerce\Repositories\Caches\OrderReturnItemCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\ProductCategoryCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\ShipmentHistoryCacheDecorator;
use Botble\Ecommerce\Repositories\Eloquent\ProductCollectionRepository;
use Botble\Ecommerce\Repositories\Interfaces\ProductAttributeInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductVariationInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShippingRuleItemInterface;
use Botble\Ecommerce\Repositories\Caches\ProductAttributeCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\ProductVariationCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\ShippingRuleItemCacheDecorator;
use Botble\Ecommerce\Repositories\Interfaces\ProductCollectionInterface;
use Botble\Ecommerce\Repositories\Caches\ProductCollectionCacheDecorator;
use Botble\Ecommerce\Repositories\Eloquent\ProductAttributeSetRepository;
use Botble\Ecommerce\Repositories\Eloquent\ProductVariationItemRepository;
use Botble\Ecommerce\Repositories\Interfaces\ProductAttributeSetInterface;
use Botble\Ecommerce\Repositories\Caches\ProductAttributeSetCacheDecorator;
use Botble\Ecommerce\Repositories\Interfaces\ProductVariationItemInterface;
use Botble\Ecommerce\Repositories\Caches\ProductVariationItemCacheDecorator;

class EcommerceServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        config([
            'auth.guards.customer' => [
                'driver' => 'session',
                'provider' => 'customers',
            ],
            'auth.providers.customers' => [
                'driver' => 'eloquent',
                'model' => Customer::class,
            ],
            'auth.passwords.customers' => [
                'provider' => 'customers',
                'table' => 'ec_customer_password_resets',
                'expire' => 60,
            ],
        ]);

        /**
         * @var Router $router
         */
        $router = $this->app['router'];

        $router->aliasMiddleware('customer', RedirectIfNotCustomer::class);
        $router->aliasMiddleware('customer.guest', RedirectIfCustomer::class);
        $router->pushMiddlewareToGroup('web', CaptureFootprintsMiddleware::class);

        $this->app->bind(ProductInterface::class, function () {
            return new ProductCacheDecorator(
                new ProductRepository(new Product())
            );
        });

        $this->app->bind(ProductCategoryInterface::class, function () {
            return new ProductCategoryCacheDecorator(
                new ProductCategoryRepository(new ProductCategory())
            );
        });

        $this->app->bind(ProductTagInterface::class, function () {
            return new ProductTagCacheDecorator(
                new ProductTagRepository(new ProductTag())
            );
        });

        $this->app->bind(GlobalOptionInterface::class, function () {
            return new GlobalOptionCacheDecorator(
                new GlobalOptionRepository(new GlobalOption())
            );
        });

        $this->app->bind(BrandInterface::class, function () {
            return new BrandCacheDecorator(
                new BrandRepository(new Brand())
            );
        });

        $this->app->bind(ProductCollectionInterface::class, function () {
            return new ProductCollectionCacheDecorator(
                new ProductCollectionRepository(new ProductCollection())
            );
        });

        $this->app->bind(CurrencyInterface::class, function () {
            return new CurrencyCacheDecorator(
                new CurrencyRepository(new Currency())
            );
        });

        $this->app->bind(ProductAttributeSetInterface::class, function () {
            return new ProductAttributeSetCacheDecorator(
                new ProductAttributeSetRepository(new ProductAttributeSet()),
                ECOMMERCE_GROUP_CACHE_KEY
            );
        });

        $this->app->bind(ProductAttributeInterface::class, function () {
            return new ProductAttributeCacheDecorator(
                new ProductAttributeRepository(new ProductAttribute()),
                ECOMMERCE_GROUP_CACHE_KEY
            );
        });

        $this->app->bind(ProductVariationInterface::class, function () {
            return new ProductVariationCacheDecorator(
                new ProductVariationRepository(new ProductVariation()),
                ECOMMERCE_GROUP_CACHE_KEY
            );
        });

        $this->app->bind(ProductVariationItemInterface::class, function () {
            return new ProductVariationItemCacheDecorator(
                new ProductVariationItemRepository(new ProductVariationItem()),
                ECOMMERCE_GROUP_CACHE_KEY
            );
        });

        $this->app->bind(TaxInterface::class, function () {
            return new TaxCacheDecorator(
                new TaxRepository(new Tax())
            );
        });

        $this->app->bind(ReviewInterface::class, function () {
            return new ReviewCacheDecorator(
                new ReviewRepository(new Review())
            );
        });

        $this->app->bind(ShippingInterface::class, function () {
            return new ShippingCacheDecorator(
                new ShippingRepository(new Shipping())
            );
        });

        $this->app->bind(ShippingRuleInterface::class, function () {
            return new ShippingRuleCacheDecorator(
                new ShippingRuleRepository(new ShippingRule())
            );
        });

        $this->app->bind(ShippingRuleItemInterface::class, function () {
            return new ShippingRuleItemCacheDecorator(
                new ShippingRuleItemRepository(new ShippingRuleItem())
            );
        });

        $this->app->bind(ShipmentInterface::class, function () {
            return new ShipmentCacheDecorator(
                new ShipmentRepository(new Shipment())
            );
        });

        $this->app->bind(ShipmentHistoryInterface::class, function () {
            return new ShipmentHistoryCacheDecorator(
                new ShipmentHistoryRepository(new ShipmentHistory())
            );
        });

        $this->app->bind(OrderInterface::class, function () {
            return new OrderCacheDecorator(
                new OrderRepository(new Order())
            );
        });

        $this->app->bind(OrderHistoryInterface::class, function () {
            return new OrderHistoryCacheDecorator(
                new OrderHistoryRepository(new OrderHistory())
            );
        });

        $this->app->bind(OrderProductInterface::class, function () {
            return new OrderProductCacheDecorator(
                new OrderProductRepository(new OrderProduct())
            );
        });

        $this->app->bind(OrderAddressInterface::class, function () {
            return new OrderAddressCacheDecorator(
                new OrderAddressRepository(new OrderAddress())
            );
        });

        $this->app->bind(OrderReturnInterface::class, function () {
            return new OrderReturnCacheDecorator(
                new OrderReturnRepository(new OrderReturn())
            );
        });

        $this->app->bind(OrderReturnItemInterface::class, function () {
            return new OrderReturnItemCacheDecorator(
                new OrderReturnItemRepository(new OrderReturnItem())
            );
        });

        $this->app->bind(DiscountInterface::class, function () {
            return new DiscountCacheDecorator(
                new DiscountRepository(new Discount())
            );
        });

        $this->app->bind(WishlistInterface::class, function () {
            return new WishlistCacheDecorator(
                new WishlistRepository(new Wishlist())
            );
        });

        $this->app->bind(AddressInterface::class, function () {
            return new AddressCacheDecorator(
                new AddressRepository(new Address())
            );
        });
        $this->app->bind(CustomerInterface::class, function () {
            return new CustomerCacheDecorator(
                new CustomerRepository(new Customer())
            );
        });

        $this->app->bind(GroupedProductInterface::class, function () {
            return new GroupedProductCacheDecorator(
                new GroupedProductRepository(new GroupedProduct())
            );
        });

        $this->app->bind(StoreLocatorInterface::class, function () {
            return new StoreLocatorCacheDecorator(
                new StoreLocatorRepository(new StoreLocator())
            );
        });

        $this->app->bind(FlashSaleInterface::class, function () {
            return new FlashSaleCacheDecorator(
                new FlashSaleRepository(new FlashSale())
            );
        });

        $this->app->bind(ProductLabelInterface::class, function () {
            return new ProductLabelCacheDecorator(
                new ProductLabelRepository(new ProductLabel())
            );
        });

        $this->app->bind(InvoiceInterface::class, function () {
            return new InvoiceCacheDecorator(new InvoiceRepository(new Invoice()));
        });

        $this->app->bind(TrackingFilterInterface::class, function ($app) {
            return $app->make(TrackingFilter::class);
        });

        $this->app->bind(TrackingLoggerInterface::class, function ($app) {
            return $app->make(TrackingLogger::class);
        });

        $this->app->singleton(FootprinterInterface::class, function ($app) {
            return $app->make(Footprinter::class);
        });

        Request::macro('footprint', function () {
            return app(FootprinterInterface::class)->footprint($this);
        });

        $this->setNamespace('plugins/ecommerce')->loadHelpers();

        $loader = AliasLoader::getInstance();
        $loader->alias('Cart', CartFacade::class);
        $loader->alias('OrderHelper', OrderHelperFacade::class);
        $loader->alias('OrderReturnHelper', OrderReturnHelperFacade::class);
        $loader->alias('EcommerceHelper', EcommerceHelperFacade::class);
        $loader->alias('ProductCategoryHelper', ProductCategoryHelperFacade::class);
        $loader->alias('CurrencyHelper', CurrencyFacade::class);
        $loader->alias('InvoiceHelper', InvoiceHelperFacade::class);
    }

    public function boot(): void
    {
        SlugHelper::registerModule(Product::class, 'Products');
        SlugHelper::registerModule(Brand::class, 'Brands');
        SlugHelper::registerModule(ProductCategory::class, 'Product Categories');
        SlugHelper::registerModule(ProductTag::class, 'Product Tags');
        SlugHelper::setPrefix(Product::class, 'products');
        SlugHelper::setPrefix(Brand::class, 'brands');
        SlugHelper::setPrefix(ProductTag::class, 'product-tags');
        SlugHelper::setPrefix(ProductCategory::class, 'product-categories');

        $this
            ->loadAndPublishConfigurations(['permissions'])
            ->loadAndPublishTranslations()
            ->loadRoutes([
                'base',
                'product',
                'tax',
                'review',
                'shipping',
                'order',
                'discount',
                'customer',
                'cart',
                'shipment',
                'wishlist',
                'compare',
                'invoice',
                'invoice-template',
            ])
            ->loadAndPublishConfigurations([
                'general',
                'shipping',
                'order',
                'cart',
                'email',
            ])
            ->loadAndPublishViews()
            ->loadMigrations()
            ->publishAssets();

        if (class_exists('ApiHelper') && ApiHelper::enabled()) {
            ApiHelper::setConfig([
                'model' => Customer::class,
                'guard' => 'customer',
                'password_broker' => 'customers',
                'verify_email' => true,
            ]);
        }

        if (File::exists(storage_path('app/invoices/template.blade.php'))) {
            $this->loadViewsFrom(storage_path('app/invoices'), 'plugins/ecommerce/invoice');
        }

        if (defined('LANGUAGE_MODULE_SCREEN_NAME') && defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME')) {
            LanguageAdvancedManager::registerModule(Product::class, [
                'name',
                'description',
                'content',
            ]);

            if (config('plugins.ecommerce.general.enable_faq_in_product_details', false)) {
                LanguageAdvancedManager::addTranslatableMetaBox('faq_schema_config_wrapper');

                LanguageAdvancedManager::registerModule(Product::class, array_merge(
                    LanguageAdvancedManager::getTranslatableColumns(Product::class),
                    ['faq_schema_config']
                )
                );
            }

            LanguageAdvancedManager::registerModule(ProductCategory::class, [
                'name',
                'description',
            ]);

            LanguageAdvancedManager::registerModule(ProductAttribute::class, [
                'title',
            ]);

            LanguageAdvancedManager::addTranslatableMetaBox('attributes_list');

            LanguageAdvancedManager::registerModule(ProductAttribute::class, array_merge(
                LanguageAdvancedManager::getTranslatableColumns(ProductAttribute::class),
                ['attributes']
            )
            );

            LanguageAdvancedManager::registerModule(ProductAttributeSet::class, [
                'title',
            ]);

            LanguageAdvancedManager::registerModule(Brand::class, [
                'name',
                'description',
            ]);

            LanguageAdvancedManager::registerModule(ProductCollection::class, [
                'name',
                'description',
            ]);

            LanguageAdvancedManager::registerModule(ProductLabel::class, [
                'name',
                'description',
            ]);

            LanguageAdvancedManager::registerModule(FlashSale::class, [
                'name',
                'description',
            ]);

            LanguageAdvancedManager::registerModule(ProductTag::class, [
                'name',
            ]);

            LanguageAdvancedManager::registerModule(GlobalOption::class, [
                'name',
            ]);

            LanguageAdvancedManager::registerModule(Option::class, [
                'name',
            ]);

            LanguageAdvancedManager::registerModule(GlobalOptionValue::class, [
                'option_value',
            ]);

            LanguageAdvancedManager::registerModule(OptionValue::class, [
                'option_value',
            ]);

            LanguageAdvancedManager::addTranslatableMetaBox('product_options_box');

            add_action(LANGUAGE_ADVANCED_ACTION_SAVED, function ($data, $request) {
                switch (get_class($data)) {
                    case Product::class:
                        $variations = $data->variations()->get();

                        foreach ($variations as $variation) {
                            if (!$variation->product->id) {
                                continue;
                            }

                            LanguageAdvancedManager::save($variation->product, $request);
                        }

                        $options = $request->input('options', []) ?: [];

                        if (!$options) {
                            return;
                        }

                        $newRequest = new Request();

                        $newRequest->replace([
                            'language' => $request->input('language'),
                            'ref_lang' => $request->input('ref_lang'),
                        ]);

                        foreach ($options as $item) {
                            $option = Option::find($item['id']);

                            $newRequest->merge(['name' => $item['name']]);

                            if ($option) {
                                LanguageAdvancedManager::save($option, $newRequest);
                            }

                            $newRequest = new Request();

                            $newRequest->replace([
                                'language' => $request->input('language'),
                                'ref_lang' => $request->input('ref_lang'),
                            ]);

                            foreach ($item['values'] as $value) {
                                if (!$value['id']) {
                                    continue;
                                }

                                $optionValue = OptionValue::find($value['id']);

                                $newRequest->merge([
                                    'option_value' => $value['option_value'],
                                ]);

                                if ($optionValue) {
                                    LanguageAdvancedManager::save($optionValue, $newRequest);
                                }
                            }
                        }

                        break;
                    case ProductAttributeSet::class:

                        $attributes = json_decode($request->input('attributes', '[]'), true) ?: [];

                        if (!$attributes) {
                            break;
                        }

                        $request = new Request();
                        $request->replace([
                            'language' => request()->input('language'),
                            'ref_lang' => request()->input('ref_lang'),
                        ]);

                        foreach ($attributes as $item) {
                            $request->merge([
                                'title' => $item['title'],
                            ]);

                            $attribute = $this->app->make(ProductAttributeInterface::class)->findById($item['id']);

                            if ($attribute) {
                                LanguageAdvancedManager::save($attribute, $request);
                            }
                        }

                        break;
                    case GlobalOption::class:

                        $option = GlobalOption::find($request->input('id'));

                        if ($option) {
                            LanguageAdvancedManager::save($option, $request);
                        }

                        $options = $request->input('options', []) ?: [];

                        if (!$options) {
                            return;
                        }

                        $newRequest = new Request();

                        $newRequest->replace([
                            'language' => $request->input('language'),
                            'ref_lang' => $request->input('ref_lang'),
                        ]);

                        foreach ($options as $value) {
                            if (!$value['id']) {
                                continue;
                            }

                            $optionValue = GlobalOptionValue::find($value['id']);

                            $newRequest->merge([
                                'option_value' => $value['option_value'],
                            ]);

                            if ($optionValue) {
                                LanguageAdvancedManager::save($optionValue, $newRequest);
                            }
                        }

                        break;
                }
            }, 1234, 2);
        }

        EmailHandler::addTemplateSettings(ECOMMERCE_MODULE_SCREEN_NAME, config('plugins.ecommerce.email', []));

        $this->app->register(HookServiceProvider::class);

        Event::listen(RouteMatched::class, function () {
            dashboard_menu()
                ->registerItem([
                    'id' => 'cms-plugins-ecommerce',
                    'priority' => 8,
                    'parent_id' => null,
                    'name' => 'plugins/ecommerce::ecommerce.name',
                    'icon' => 'fa fa-shopping-cart',
                    'url' => route('products.index'),
                    'permissions' => ['plugins.ecommerce'],
                ])
                ->registerItem([
                    'id' => 'cms-packages-build-boat',
                    'priority' => 9,
                    'parent_id' => null,
                    'name' => 'Build a boat',
                    'icon' => 'fa fa-bars',
                    'url' => route('predefined-list'),
                    'permissions' => ['plugins.ecommerce'],
                ])
                ->registerItem([
                    'id' => 'cms-packages-products',
                    'priority' => 0,
                    'parent_id' => "cms-packages-build-boat",
                    'name' => 'Boats',
                    'icon' => 'fa fa-list',
                    'url' => route('predefined-list'),
                    'permissions' => ['plugins.ecommerce'],
                ])
                ->registerItem([
                    'id' => 'cms-packages-categories',
                    'priority' => 1,
                    'parent_id' => "cms-packages-build-boat",
                    'name' => 'Categories',
                    'icon' => 'fa fa-list-alt',
                    'url' => route('predefined-categories'),
                    'permissions' => ['plugins.ecommerce'],
                ])
                ->registerItem([
                    'id' => 'cms-packages-custom-boat-enquiries',
                    'priority' => 1,
                    'parent_id' => "cms-packages-build-boat",
                    'name' => 'Enquiries',
                    'icon' => 'fa fa-list-alt',
                    'url' => route('custom-boat-enquiries'),
                    'permissions' => ['plugins.ecommerce'],
                ])
                ->registerItem([
                    'id' => 'cms-packages-custom-boat-views',
                    'priority' => 1,
                    'parent_id' => "cms-packages-build-boat",
                    'name' => 'Boat Views',
                    'icon' => 'fa fa-list-alt',
                    'url' => route('custom-boat-views'),
                    'permissions' => ['plugins.ecommerce'],
                ])
                ->registerItem([
                    'id' => 'cms-packages-custom-boat-discounts',
                    'priority' => 1,
                    'parent_id' => "cms-packages-build-boat",
                    'name' => 'Boat Discounts',
                    'icon' => 'fa fa-list-alt',
                    'url' => route('custom-boat-discounts'),
                    'permissions' => ['plugins.ecommerce'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-ecommerce-report',
                    'priority' => 0,
                    'parent_id' => 'cms-plugins-ecommerce',
                    'name' => 'plugins/ecommerce::reports.name',
                    'icon' => 'far fa-chart-bar',
                    'url' => route('ecommerce.report.index'),
                    'permissions' => ['ecommerce.report.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-flash-sale',
                    'priority' => 0,
                    'parent_id' => 'cms-plugins-ecommerce',
                    'name' => 'plugins/ecommerce::flash-sale.name',
                    'icon' => 'fa fa-bolt',
                    'url' => route('flash-sale.index'),
                    'permissions' => ['flash-sale.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-ecommerce-order',
                    'priority' => 1,
                    'parent_id' => 'cms-plugins-ecommerce',
                    'name' => 'plugins/ecommerce::order.menu',
                    'icon' => 'fa fa-shopping-bag',
                    'url' => route('orders.index'),
                    'permissions' => ['orders.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-ecommerce-invoice',
                    'priority' => 2,
                    'parent_id' => 'cms-plugins-ecommerce',
                    'name' => 'plugins/ecommerce::invoice.name',
                    'icon' => 'fas fa-book',
                    'url' => route('ecommerce.invoice.index'),
                    'permissions' => ['ecommerce.invoice.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-invoice-template',
                    'priority' => 2,
                    'parent_id' => 'cms-plugins-ecommerce',
                    'name' => 'plugins/ecommerce::invoice-template.name',
                    'icon' => 'fas fa-book',
                    'url' => route('invoice-template.index'),
                    'permissions' => ['ecommerce.invoice-template.index'],
                ])
                // ->registerItem([
                //     'id' => 'cms-plugins-ecommerce-incomplete-order',
                //     'priority' => 2,
                //     'parent_id' => 'cms-plugins-ecommerce',
                //     'name' => 'plugins/ecommerce::order.incomplete_order',
                //     'icon' => 'fas fa-shopping-basket',
                //     'url' => route('orders.incomplete-list'),
                //     'permissions' => ['orders.index'],
                // ])
                ->registerItem([
                    'id' => 'cms-plugins-ecommerce-order-return',
                    'priority' => 3,
                    'parent_id' => 'cms-plugins-ecommerce',
                    'name' => 'plugins/ecommerce::order.order_return',
                    'icon' => 'fa fa-cart-arrow-down',
                    'url' => route('order_returns.index'),
                    'permissions' => ['orders.edit'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-ecommerce.product',
                    'priority' => 3,
                    'parent_id' => 'cms-plugins-ecommerce',
                    'name' => 'plugins/ecommerce::products.name',
                    'icon' => 'fa fa-camera',
                    'url' => route('products.index'),
                    'permissions' => ['products.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-product-categories',
                    'priority' => 4,
                    'parent_id' => 'cms-plugins-ecommerce',
                    'name' => 'plugins/ecommerce::product-categories.name',
                    'icon' => 'fa fa-archive',
                    'url' => route('product-categories.index'),
                    'permissions' => ['product-categories.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-product-tag',
                    'priority' => 4,
                    'parent_id' => 'cms-plugins-ecommerce',
                    'name' => 'plugins/ecommerce::product-tag.name',
                    'icon' => 'fa fa-tag',
                    'url' => route('product-tag.index'),
                    'permissions' => ['product-tag.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-product-attribute',
                    'priority' => 5,
                    'parent_id' => 'cms-plugins-ecommerce',
                    'name' => 'plugins/ecommerce::product-attributes.name',
                    'icon' => 'fas fa-glass-martini',
                    'url' => route('product-attribute-sets.index'),
                    'permissions' => ['product-attribute-sets.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-ecommerce-global-options',
                    'priority' => 3,
                    'parent_id' => 'cms-plugins-ecommerce',
                    'name' => 'plugins/ecommerce::product-option.name',
                    'icon' => 'fa fa-database',
                    'url' => route('global-option.index'),
                    'permissions' => ['global-option.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-brands',
                    'priority' => 6,
                    'parent_id' => 'cms-plugins-ecommerce',
                    'name' => 'plugins/ecommerce::brands.name',
                    'icon' => 'fa fa-registered',
                    'url' => route('brands.index'),
                    'permissions' => ['brands.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-product-collections',
                    'priority' => 7,
                    'parent_id' => 'cms-plugins-ecommerce',
                    'name' => 'plugins/ecommerce::product-collections.name',
                    'icon' => 'fa fa-file-excel',
                    'url' => route('product-collections.index'),
                    'permissions' => ['product-collections.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-product-label',
                    'priority' => 8,
                    'parent_id' => 'cms-plugins-ecommerce',
                    'name' => 'plugins/ecommerce::product-label.name',
                    'icon' => 'fas fa-tags',
                    'url' => route('product-label.index'),
                    'permissions' => ['product-label.index'],
                ])
                ->registerItem([
                    'id' => 'cms-ecommerce-review',
                    'priority' => 9,
                    'parent_id' => 'cms-plugins-ecommerce',
                    'name' => 'plugins/ecommerce::review.name',
                    'icon' => 'fa fa-comments',
                    'url' => route('reviews.index'),
                    'permissions' => ['reviews.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-ecommerce-shipping-provider',
                    'priority' => 10,
                    'parent_id' => 'cms-plugins-ecommerce',
                    'name' => 'plugins/ecommerce::shipping.shipping',
                    'icon' => 'fas fa-shipping-fast',
                    'url' => route('shipping_methods.index'),
                    'permissions' => ['shipping_methods.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-ecommerce-shipping-shipments',
                    'priority' => 11,
                    'parent_id' => 'cms-plugins-ecommerce',
                    'name' => 'plugins/ecommerce::shipping.shipments',
                    'icon' => 'fas fa-people-carry',
                    'url' => route('ecommerce.shipments.index'),
                    'permissions' => ['orders.edit'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-ecommerce-discount',
                    'priority' => 12,
                    'parent_id' => 'cms-plugins-ecommerce',
                    'name' => 'plugins/ecommerce::discount.name',
                    'icon' => 'fa fa-gift',
                    'url' => route('discounts.index'),
                    'permissions' => ['discounts.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-ecommerce-customer',
                    'priority' => 13,
                    'parent_id' => 'cms-plugins-ecommerce',
                    'name' => 'plugins/ecommerce::customer.name',
                    'icon' => 'fa fa-users',
                    'url' => route('customers.index'),
                    'permissions' => ['customers.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-ecommerce.basic-settings',
                    'priority' => 998,
                    'parent_id' => 'cms-plugins-ecommerce',
                    'name' => 'plugins/ecommerce::ecommerce.basic_settings',
                    'icon' => 'fas fa-cogs',
                    'url' => route('ecommerce.settings'),
                    'permissions' => ['ecommerce.settings'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-ecommerce.advanced-settings',
                    'priority' => 999,
                    'parent_id' => 'cms-plugins-ecommerce',
                    'name' => 'plugins/ecommerce::ecommerce.advanced_settings',
                    'icon' => 'fas fa-plus',
                    'url' => route('ecommerce.advanced-settings'),
                    'permissions' => ['ecommerce.settings'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-ecommerce.tracking-settings',
                    'priority' => 999,
                    'parent_id' => 'cms-plugins-ecommerce',
                    'name' => 'plugins/ecommerce::ecommerce.setting.tracking_settings',
                    'icon' => 'fa-solid fa-chart-pie',
                    'url' => route('ecommerce.tracking-settings'),
                    'permissions' => ['ecommerce.settings'],
                ]);

            if (EcommerceHelper::isTaxEnabled()) {
                dashboard_menu()->registerItem([
                    'id' => 'cms-plugins-ecommerce-tax',
                    'priority' => 14,
                    'parent_id' => 'cms-plugins-ecommerce',
                    'name' => 'plugins/ecommerce::tax.name',
                    'icon' => 'fas fa-money-check-alt',
                    'url' => route('tax.index'),
                    'permissions' => ['tax.index'],
                ]);
            }

            if (!dashboard_menu()->hasItem('cms-core-tools')) {
                dashboard_menu()->registerItem([
                    'id' => 'cms-core-tools',
                    'priority' => 96,
                    'parent_id' => null,
                    'name' => 'core/base::base.tools',
                    'icon' => 'fas fa-tools',
                    'url' => '',
                    'permissions' => [],
                ]);
            }

            dashboard_menu()
                ->registerItem([
                    'id' => 'cms-core-tools-ecommerce-bulk-import',
                    'priority' => 1,
                    'parent_id' => 'cms-core-tools',
                    'name' => 'plugins/ecommerce::bulk-import.menu',
                    'icon' => 'fas fa-file-import',
                    'url' => route('ecommerce.bulk-import.index'),
                    'permissions' => ['ecommerce.bulk-import.index'],
                ])
                ->registerItem([
                    'id' => 'cms-core-tools-ecommerce-export-products',
                    'priority' => 2,
                    'parent_id' => 'cms-core-tools',
                    'name' => 'plugins/ecommerce::export.products.name',
                    'icon' => 'fas fa-file-export',
                    'url' => route('ecommerce.export.products.index'),
                    'permissions' => ['ecommerce.export.products.index'],
                ]);
        });

        $this->app->booted(function () {
            SeoHelper::registerModule([
                Product::class,
                Brand::class,
                ProductCategory::class,
                ProductTag::class,
            ]);

            $this->app->make(Schedule::class)->command(SendAbandonedCartsEmailCommand::class)->weekly('23:30');
            $this->app->make(Schedule::class)->command(SendSavedBoatsEmailCommand::class)->dailyAt('00:00');
            if (is_plugin_active('payment')) {
                Payment::resolveRelationUsing('order', function ($model) {
                    return $model->belongsTo(Order::class, 'order_id')->withDefault();
                });
            }

            if (defined('SOCIAL_LOGIN_MODULE_SCREEN_NAME') && Route::has('customer.login')) {
                SocialService::registerModule([
                    'guard' => 'customer',
                    'model' => Customer::class,
                    'login_url' => route('customer.login'),
                    'redirect_url' => route('public.index'),
                ]);
            }
        });

        $this->app->register(EventServiceProvider::class);
        $this->app->register(CommandServiceProvider::class);

        Event::listen(['cart.removed', 'cart.stored', 'cart.restored', 'cart.updated'], function ($cart) {
            $coupon = session('applied_coupon_code');
            if ($coupon) {
                $this->app->make(HandleRemoveCouponService::class)->execute();
                if (Cart::count() || ($cart instanceof \Botble\Ecommerce\Cart\Cart && $cart->count())) {
                    $this->app->make(HandleApplyCouponService::class)->execute($coupon);
                }
            }
        });
    }
}
