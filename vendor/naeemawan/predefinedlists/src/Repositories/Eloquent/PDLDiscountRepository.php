<?php

namespace NaeemAwan\PredefinedLists\Repositories\Eloquent;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\PDLDiscountInterface;
use Illuminate\Database\Eloquent\Collection;

class PDLDiscountRepository extends RepositoriesAbstract implements PDLDiscountInterface
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
