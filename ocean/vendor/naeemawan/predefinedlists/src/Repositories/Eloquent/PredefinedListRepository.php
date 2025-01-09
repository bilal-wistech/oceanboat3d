<?php

namespace NaeemAwan\PredefinedLists\Repositories\Eloquent;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\PredefinedListInterface;
use Illuminate\Database\Eloquent\Collection;
use NaeemAwan\PredefinedLists\Models\PredefinedListDetail;

class PredefinedListRepository extends RepositoriesAbstract implements PredefinedListInterface
{
    public function getAll(): Collection
    {
        $data = $this->model
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->notExpired()
            ->with(['metadata']);

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    public function filterProducts(array $params = [])
    {
    	// dd($params);

        $this->model = $this->originalModel;

        // Filter product by categories
        $params['categories'] = array_filter($params['categories']);
        if ($params['categories']) {
            $this->model = $this->model
                ->whereIn('id',function($query) use ($params){
                    $query->select('list_id')->from((new PredefinedListDetail)->getTable())->whereIn('category_id',$params['categories']);
                });
        }

       return $this->advancedGet($params);
    }
}
