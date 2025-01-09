<?php

namespace Botble\Theme\Repositories\Caches;

use Botble\Support\Repositories\Caches\CacheAbstractDecorator;
use Botble\Theme\Repositories\Interfaces\ParallelSliderInterface;
use Illuminate\Database\Eloquent\Collection;

class ParallelSliderCacheDecorator extends CacheAbstractDecorator implements ParallelSliderInterface
{
    public function getAll(): Collection
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
