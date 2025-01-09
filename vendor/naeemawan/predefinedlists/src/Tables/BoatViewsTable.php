<?php

namespace NaeemAwan\PredefinedLists\Tables;

use Auth;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\BoatViewInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\DataTables;

class BoatViewsTable extends TableAbstract
{
    protected $hasActions = false;
    protected $hasFilter = false;
    protected $hasOperations = false;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, BoatViewInterface $BoatViewRepository)
    {
        $this->repository = $BoatViewRepository;

        parent::__construct($table, $urlGenerator);

        // Ensure operations and actions are disabled
        if (!Auth::user()->hasAnyPermission(['custom-boat-enquiries.edit', 'custom-boat-enquiries.edit'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('entity_id', function ($item) {
                return $item->option->ltitle ?? '';
            })
            ->editColumn('entity_type', function ($item) {
                return ucfirst($item->entity_type);
            })
            ->editColumn('total_count', function ($item) {
                return $item->total_count;
            })
            ->editColumn('total_price', function ($item) {
                return $item->total_price;
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository->getModel()->select([
            'id',
            'entity_id',
            'entity_type',
            'total_count',
        ])->orderBy('total_count', 'desc');
        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        $columns = [
            'id' => [
                'name' => 'id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'entity_id' => [
                'name' => 'entity_id',
                'title' => 'Name',
                'class' => 'text-start',
                'searchable' => true,
            ],
            'entity_type' => [
                'name' => 'entity_type',
                'title' => 'Type',
                'class' => 'text-start',
            ],
            'total_count' => [
                'name' => 'total_count',
                'title' => 'Count',
                'class' => 'text-start',
            ],
        ];

        return $columns;
    }

    public function bulkActions(): array
    {
        return []; // No bulk actions
    }
}
