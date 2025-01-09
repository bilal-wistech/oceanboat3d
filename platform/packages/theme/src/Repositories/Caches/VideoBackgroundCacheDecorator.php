<?php

namespace Botble\Theme\Repositories\Caches;

use Botble\Support\Repositories\Caches\CacheAbstractDecorator;
use Botble\Theme\Repositories\Interfaces\VideoBackgroundInterface;
use Illuminate\Database\Eloquent\Collection;

class VideoBackgroundCacheDecorator extends CacheAbstractDecorator implements VideoBackgroundInterface
{
    public function getAll(): Collection
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
