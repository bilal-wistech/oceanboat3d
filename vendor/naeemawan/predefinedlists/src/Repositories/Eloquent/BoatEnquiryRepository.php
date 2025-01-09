<?php

namespace NaeemAwan\PredefinedLists\Repositories\Eloquent;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\BoatEnquiryInterface;
use Illuminate\Database\Eloquent\Collection;

class BoatEnquiryRepository extends RepositoriesAbstract implements BoatEnquiryInterface
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
