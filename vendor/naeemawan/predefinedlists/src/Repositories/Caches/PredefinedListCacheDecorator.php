<?php

namespace NaeemAwan\PredefinedLists\Repositories\Caches;

use Botble\Support\Repositories\Caches\CacheAbstractDecorator;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\PredefinedListInterface;
use Illuminate\Database\Eloquent\Collection;

class PredefinedListCacheDecorator extends CacheAbstractDecorator implements PredefinedListInterface
{
    public function getAll(): Collection
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
    public function filterProducts(array $params = [])
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
