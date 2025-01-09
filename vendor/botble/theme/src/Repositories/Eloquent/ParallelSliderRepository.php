<?php

namespace Botble\Theme\Repositories\Eloquent;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Botble\Theme\Repositories\Interfaces\ParallelSliderInterface;
use Illuminate\Database\Eloquent\Collection;

class ParallelSliderRepository extends RepositoriesAbstract implements ParallelSliderInterface
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
