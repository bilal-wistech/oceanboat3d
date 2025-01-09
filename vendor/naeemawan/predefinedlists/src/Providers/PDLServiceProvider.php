<?php

namespace NaeemAwan\PredefinedLists\Providers;

use Illuminate\Support\ServiceProvider;
use NaeemAwan\PredefinedLists\Models\BoatView;
use NaeemAwan\PredefinedLists\Models\BoatEnquiry;
use NaeemAwan\PredefinedLists\Models\BoatDiscount;
use NaeemAwan\PredefinedLists\Models\PredefinedList;
use NaeemAwan\PredefinedLists\Models\PredefinedCategory;
use NaeemAwan\PredefinedLists\Observers\PredefinedListObserver;
use NaeemAwan\PredefinedLists\Repositories\Eloquent\BoatViewRepository;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\BoatViewInterface;
use NaeemAwan\PredefinedLists\Repositories\Caches\BoatViewCacheDecorator;
use NaeemAwan\PredefinedLists\Repositories\Eloquent\BoatEnquiryRepository;
use NaeemAwan\PredefinedLists\Repositories\Eloquent\PDLDiscountRepository;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\BoatEnquiryInterface;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\PDLDiscountInterface;
use NaeemAwan\PredefinedLists\Repositories\Caches\BoatEnquiryCacheDecorator;
use NaeemAwan\PredefinedLists\Repositories\Caches\PDLDiscountCacheDecorator;
use NaeemAwan\PredefinedLists\Repositories\Eloquent\PredefinedListRepository;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\PredefinedListInterface;
use NaeemAwan\PredefinedLists\Repositories\Caches\PredefinedListCacheDecorator;
use NaeemAwan\PredefinedLists\Repositories\Eloquent\PredefinedCategoryRepository;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\PredefinedCategoryInterface;
use NaeemAwan\PredefinedLists\Repositories\Caches\PredefinedCategoryCacheDecorator;

class PDLServiceProvider extends ServiceProvider
{
  public function boot()
  {
    PredefinedList::observe(PredefinedListObserver::class);

    $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
    $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');
    $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'pdlists');
    $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'pdlists');
    $this->publishes([
      __DIR__ . '/../../config/predefinedlists.php' => config_path('predefinedlists.php'),
    ], 'pdlists-config');
    $this->publishes([
      __DIR__ . '/../../resources/lang' => resource_path('lang/vendor/pdlists'),
    ], 'pdlists-lang');
    $this->publishes([
      __DIR__ . '/../../database/migrations/' => database_path('migrations')
    ], 'pdlists-migrations');
  }

  /**
   * Make config publishment optional by merging the config from the package.
   *
   * @return  void
   */
  public function register()
  {
    $this->app->bind(PredefinedListInterface::class, function () {
      return new PredefinedListCacheDecorator(new PredefinedListRepository(new PredefinedList()));
    });
    $this->app->bind(PredefinedCategoryInterface::class, function () {
      return new PredefinedCategoryCacheDecorator(new PredefinedCategoryRepository(new PredefinedCategory()));
    });
    $this->app->bind(BoatEnquiryInterface::class, function () {
      return new BoatEnquiryCacheDecorator(new BoatEnquiryRepository(new BoatEnquiry()));
    });
    $this->app->bind(BoatViewInterface::class, function () {
      return new BoatViewCacheDecorator(new BoatViewRepository(new BoatView()));
    });
    $this->app->bind(PDLDiscountInterface::class, function () {
      return new PDLDiscountCacheDecorator(new PDLDiscountRepository(new BoatDiscount()));
    });
    $this->mergeConfigFrom(
      __DIR__ . '/../../config/predefinedlists.php',
      'predefinedlists'
    );
  }
}
