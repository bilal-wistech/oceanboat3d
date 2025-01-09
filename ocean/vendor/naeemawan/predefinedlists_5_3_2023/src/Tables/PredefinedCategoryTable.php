<?php

namespace NaeemAwan\PredefinedLists\Tables;

use Auth;
use BaseHelper;
use NaeemAwan\PredefinedLists\Models\PredefinedCategory;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\PredefinedCategoryInterface;
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

class PredefinedCategoryTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = false;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, PredefinedCategoryInterface $predefinedcategoryRepository)
    {
        $this->repository = $predefinedcategoryRepository;
        parent::__construct($table, $urlGenerator);

        if (! Auth::user()->hasAnyPermission(['predefined-categories.edit', 'predefined-categories.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function ($item) {
                if (! Auth::user()->hasPermission('predefined-categories.edit')) {
                    return BaseHelper::clean($item->name);
                }

                return Html::link(route('predefined-categories.edit', $item->id), BaseHelper::clean($item->name));
            })
            ->editColumn('description', function ($item) {
                return $item->description;
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('status', function ($item) {
                return $item->status==1 ? 'Enable' : 'Disable';
            });
            $data = $data->addColumn('operations', function ($item) {
                return $this->getOperations('predefined-categories.edit', 'predefined-categories.destroy', $item);
        });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository->getModel()->select([
            'id',
            'name',
            'description',
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
            'name' => [
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
        $buttons = $this->addCreateButton(route('predefined-categories.create'), 'predefined-categories.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, PredefinedLists::class);
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('predefined-categories.deletes'), 'predefined-categories.destroy', parent::bulkActions());
    }
}
