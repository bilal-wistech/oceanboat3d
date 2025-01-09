<?php

namespace Botble\Theme\Repositories\Eloquent;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Botble\Theme\Repositories\Interfaces\VideoBackgroundInterface;
use Illuminate\Database\Eloquent\Collection;

class VideoBackgroundRepository extends RepositoriesAbstract implements VideoBackgroundInterface
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
