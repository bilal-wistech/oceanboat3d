<?php

namespace Botble\Theme\Providers;

use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Theme\Commands\ThemeActivateCommand;
use Botble\Theme\Commands\ThemeAssetsPublishCommand;
use Botble\Theme\Commands\ThemeAssetsRemoveCommand;
use Botble\Theme\Commands\ThemeOptionCheckMissingCommand;
use Botble\Theme\Commands\ThemeRemoveCommand;
use Botble\Theme\Commands\ThemeRenameCommand;
use Botble\Theme\Contracts\Theme as ThemeContract;
use Botble\Theme\Theme;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Botble\Theme\Repositories\Caches\ParallelSliderCacheDecorator;
use Botble\Theme\Repositories\Eloquent\ParallelSliderRepository;
use Botble\Theme\Repositories\Interfaces\ParallelSliderInterface;
use Botble\Theme\Models\ParallelSlider;

class ThemeServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->setNamespace('packages/theme')
            ->loadHelpers();

        $this->app->bind(ThemeContract::class, Theme::class);

        $this->app->bind(ParallelSliderInterface::class, function () {
            return new ParallelSliderCacheDecorator(new ParallelSliderRepository(new ParallelSlider()));
        });

        $this->commands([
            ThemeActivateCommand::class,
            ThemeRemoveCommand::class,
            ThemeAssetsPublishCommand::class,
            ThemeOptionCheckMissingCommand::class,
            ThemeAssetsRemoveCommand::class,
            ThemeRenameCommand::class,
        ]);
    }

    public function boot(): void
    {
        $this
            ->loadAndPublishConfigurations(['general', 'permissions'])
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->publishAssets();

        Event::listen(RouteMatched::class, function () {
            dashboard_menu()
                ->registerItem([
                    'id' => 'cms-core-appearance',
                    'priority' => 996,
                    'parent_id' => null,
                    'name' => 'packages/theme::theme.appearance',
                    'icon' => 'fa fa-paint-brush',
                    'url' => '#',
                    'permissions' => [],
                ]);

            if ($this->app['config']->get('packages.theme.general.display_theme_manager_in_admin_panel', true)) {
                dashboard_menu()
                    ->registerItem([
                        'id' => 'cms-core-theme',
                        'priority' => 1,
                        'parent_id' => 'cms-core-appearance',
                        'name' => 'packages/theme::theme.name',
                        'icon' => null,
                        'url' => route('theme.index'),
                        'permissions' => ['theme.index'],
                    ]);
            }

            dashboard_menu()
                ->registerItem([
                    'id' => 'cms-core-theme-option',
                    'priority' => 4,
                    'parent_id' => 'cms-core-appearance',
                    'name' => 'packages/theme::theme.theme_options',
                    'icon' => null,
                    'url' => route('theme.options'),
                    'permissions' => ['theme.options'],
                ])
                ->registerItem([
                    'id' => 'cms-core-parallel-slider',
                    'priority' => 5,
                    'parent_id' => 'cms-core-appearance',
                    'name' => 'Parallel Slider',
                    'icon' => null,
                    'url' => route('theme.parallel-slider'),
                    'permissions' => ['theme.options'],
                ])
                ->registerItem([
                    'id' => 'cms-core-appearance-custom-css',
                    'priority' => 5,
                    'parent_id' => 'cms-core-appearance',
                    'name' => 'packages/theme::theme.custom_css',
                    'icon' => null,
                    'url' => route('theme.custom-css'),
                    'permissions' => ['theme.custom-css'],
                ]);

            if (config('packages.theme.general.enable_custom_js')) {
                dashboard_menu()
                    ->registerItem([
                        'id' => 'cms-core-appearance-custom-js',
                        'priority' => 6,
                        'parent_id' => 'cms-core-appearance',
                        'name' => 'packages/theme::theme.custom_js',
                        'icon' => null,
                        'url' => route('theme.custom-js'),
                        'permissions' => ['theme.custom-js'],
                    ]);
            }

            if (config('packages.theme.general.enable_custom_html')) {
                dashboard_menu()
                    ->registerItem([
                        'id' => 'cms-core-appearance-custom-html',
                        'priority' => 6,
                        'parent_id' => 'cms-core-appearance',
                        'name' => 'packages/theme::theme.custom_html',
                        'icon' => null,
                        'url' => route('theme.custom-html'),
                        'permissions' => ['theme.custom-html'],
                    ]);
            }

            admin_bar()
                ->registerLink(trans('packages/theme::theme.name'), route('theme.index'), 'appearance', 'theme.index')
                ->registerLink(trans('packages/theme::theme.theme_options'), route('theme.options'), 'appearance', 'theme.options');
        });

        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
        });

        $this->app->register(ThemeManagementServiceProvider::class);
    }
}
