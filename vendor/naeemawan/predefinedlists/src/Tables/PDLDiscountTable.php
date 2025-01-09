<?php

namespace NaeemAwan\PredefinedLists\Tables;

use Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Routing\UrlGenerator;
use NaeemAwan\PredefinedLists\Models\BoatDiscount;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\PDLDiscountInterface;

class PDLDiscountTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = false;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, PDLDiscountInterface $pdlDiscountRepository)
    {
        $this->repository = $pdlDiscountRepository;

        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['custom-boat-discounts.edit', 'custom-boat-discounts.edit'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }

    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('id', function ($item) {
                return $item->id;
            })
            ->editColumn('code', function ($item) {
                return $item->code;
            })
            ->editColumn('list_id', function ($item) {
                return $item->list->ltitle ?? '';
            })
            ->editColumn('accessory_id', function ($item) {
                return $item->accessory->ltitle ?? '-';
            })
            ->editColumn('discount', function ($item) {
                if ($item->discount_type == 'amount') {
                    return format_price($item->discount);
                } else {
                    return ($item->discount) . '%';
                }
            })
            ->editColumn('discount_type', function ($item) {
                return $item->discount_type;
            })
            ->editColumn('valid_from', function ($item) {
                return $item->valid_from;
            })
            ->editColumn('valid_to', function ($item) {
                return $item->valid_to;
            })
            ->editColumn('never_expires', function ($item) {
                return $item->never_expires;
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            });
        $data = $data->addColumn('operations', function ($item) {
            return $this->getOperations('custom-boat-discounts.edit', 'custom-boat-discounts.destroy', $item);
        });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository->getModel()->select([
            'id',
            'code',
            'list_id',
            'accessory_id',
            'discount',
            'discount_type',
            'valid_from',
            'valid_to',
            'never_expires',
            'created_at'
        ])->orderBy('created_at', 'desc');
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
            'code' => [
                'name' => 'code',
                'title' => 'Code',
                'class' => 'text-start',
                'searchable' => true,
            ],
            'list_id' => [
                'name' => 'list_id',
                'title' => 'Boat',
                'class' => 'text-start',
            ],
            'accessory_id' => [
                'name' => 'accessory_id',
                'title' => 'Accessory',
                'class' => 'text-start',
            ],
            'discount' => [
                'name' => 'discount',
                'title' => 'Discount',
                'class' => 'text-start',
            ],
            'discount_type' => [
                'name' => 'discount_type',
                'title' => 'Discount Type',
                'class' => 'text-start',
            ],
            'valid_from' => [
                'name' => 'valid_from',
                'title' => 'Valid From',
                'class' => 'text-start',
            ],
            'valid_to' => [
                'name' => 'valid_to',
                'title' => 'Valid To',
                'class' => 'text-start',
            ],
            'never_expires' => [
                'name' => 'never_expires',
                'title' => 'Never Expires',
                'class' => 'text-start',
            ],
        ];

        return $columns;
    }
    public function buttons(): array
    {
        $buttons = [];
        $buttons = $this->addCreateButton(route('custom-boat-discounts.create'), 'custom-boat-discounts.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, BoatDiscount::class);
    }
    public function bulkActions(): array
    {
        return []; //$this->addDeleteAction(route('custom-boat-enquiries.deletes'), 'custom-boat-enquiries.destroy', parent::bulkActions());
    }
}
