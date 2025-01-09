<?php

namespace NaeemAwan\PredefinedLists\Repositories\Caches;

use Botble\Support\Repositories\Caches\CacheAbstractDecorator;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\BoatViewInterface;
use Illuminate\Database\Eloquent\Collection;

class BoatViewCacheDecorator extends CacheAbstractDecorator implements BoatViewInterface
{
    public function getAll(): Collection
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
