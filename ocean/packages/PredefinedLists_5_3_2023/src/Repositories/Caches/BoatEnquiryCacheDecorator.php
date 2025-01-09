<?php

namespace NaeemAwan\PredefinedLists\Repositories\Caches;

use Botble\Support\Repositories\Caches\CacheAbstractDecorator;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\BoatEnquiryInterface;
use Illuminate\Database\Eloquent\Collection;

class BoatEnquiryCacheDecorator extends CacheAbstractDecorator implements BoatEnquiryInterface
{
    public function getAll(): Collection
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
