<?php

namespace Botble\Ecommerce\Providers;

use Botble\Ecommerce\Commands\SendAbandonedCartsEmailCommand;
use Botble\Ecommerce\Commands\SendSavedBoatsEmailCommand;
use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->commands([
            SendAbandonedCartsEmailCommand::class,
        ]);
        $this->commands([
            SendSavedBoatsEmailCommand::class,
        ]);
    }
}
