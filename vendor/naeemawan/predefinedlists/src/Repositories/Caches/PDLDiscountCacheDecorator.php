<?php

namespace NaeemAwan\PredefinedLists\Repositories\Caches;

use Botble\Support\Repositories\Caches\CacheAbstractDecorator;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\PDLDiscountInterface;
use Illuminate\Database\Eloquent\Collection;

class PDLDiscountCacheDecorator extends CacheAbstractDecorator implements PDLDiscountInterface
{
    public function getAll(): Collection
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
