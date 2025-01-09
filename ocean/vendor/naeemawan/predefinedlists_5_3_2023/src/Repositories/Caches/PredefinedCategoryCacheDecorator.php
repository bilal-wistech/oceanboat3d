<?php

namespace NaeemAwan\PredefinedLists\Repositories\Caches;

use Botble\Support\Repositories\Caches\CacheAbstractDecorator;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\PredefinedCategoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PredefinedCategoryCacheDecorator extends CacheAbstractDecorator implements PredefinedCategoryInterface
{
    public function getAll(): Collection
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
