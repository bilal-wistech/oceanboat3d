<?php

namespace NaeemAwan\PredefinedLists\Repositories\Eloquent;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\BoatViewInterface;
use Illuminate\Database\Eloquent\Collection;

class BoatViewRepository extends RepositoriesAbstract implements BoatViewInterface
{
    public function getAll(): Collection
    {
    
        $data = $this->model
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->notExpired()
            ->with(['metadata']);

        return $this->applyBeforeExecuteQuery($data)->get();
    }
}
