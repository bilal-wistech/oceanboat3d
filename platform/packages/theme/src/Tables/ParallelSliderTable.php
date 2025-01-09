<?php

namespace Botble\Theme\Tables;

use Auth;
use BaseHelper;
use Botble\Theme\Models\ParallelSlider;
use Botble\Theme\Repositories\Interfaces\ParallelSliderInterface;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use RvMedia;
use Yajra\DataTables\DataTables;

class ParallelSliderTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = false;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, ParallelSliderInterface $ParallelSliderRepository)
    {
        $this->repository = $ParallelSliderRepository;
        parent::__construct($table, $urlGenerator);

        if (! Auth::user()->hasAnyPermission(['parallel-slider.edit', 'parallel-slider.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('image', function ($item) {
                return Html::image(
                    RvMedia::getImageUrl($item->image, 'thumb', false, RvMedia::getDefaultImage()),
                    $item->name,
                    ['width' => 50]
                );
            })
            ->editColumn('name', function ($item) {
                if (! Auth::user()->hasPermission('parallel-slider.edit')) {
                    return BaseHelper::clean($item->title);
                }

                return Html::link(route('parallel-slider.edit', $item->id), BaseHelper::clean($item->title));
            })
            ->editColumn('description', function ($item) {
                return $item->description;
            })
            ->editColumn('action_title', function ($item) {
                return $item->action_title;
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('status', function ($item) {
                return $item->status==1 ? 'Enable' : 'Disable';
            });
            $data = $data->addColumn('operations', function ($item) {
                return $this->getOperations('parallel-slider.edit', 'parallel-slider.destroy', $item);
        });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository->getModel()->select([
            'id',
            'title',
            'description',
            'image',
            'action_title',
            'status',
        ]);
        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'title' => [
                'name' => 'name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-start',
            ],
            'description' => [
                'name' => 'description',
                'title' => 'Description',
                'class' => 'text-start',
                'searchable'=>false,
            ],
            'image' => [
                'name' => 'image',
                'title' => 'Image',
                'class' => 'text-start',
                'searchable'=>false,
            ],
            'action_title' => [
                'name' => 'action_title',
                'title' => 'Action Button',
                'class' => 'text-start',
            ],
            'status' => [
                'name' => 'status',
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
                'searchable'=>false,
            ],
        ];
    }

    public function buttons(): array
    {
        $buttons=[];
        $buttons = $this->addCreateButton(route('parallel-slider.create'), 'parallel-slider.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, ParallelSlider::class);
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('parallel-slider.deletes'), 'parallel-slider.destroy', parent::bulkActions());
    }
}
