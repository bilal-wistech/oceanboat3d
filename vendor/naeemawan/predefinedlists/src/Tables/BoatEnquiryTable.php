<?php

namespace NaeemAwan\PredefinedLists\Tables;

use Auth;
use BaseHelper;
use NaeemAwan\PredefinedLists\Models\BoatEnquiry;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\BoatEnquiryInterface;
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

class BoatEnquiryTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = false;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, BoatEnquiryInterface $BoatEnquiryRepository)
    {
        $this->repository = $BoatEnquiryRepository;
        parent::__construct($table, $urlGenerator);

        if (! Auth::user()->hasAnyPermission(['custom-boat-enquiries.edit', 'custom-boat-enquiries.edit'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function ($item) {
                return $item->customer->name;
            })
            ->editColumn('email', function ($item) {
                return $item->customer->email;
            })
            ->editColumn('phone_number', function ($item) {
                return $item->customer->phone;
            })
            ->editColumn('total_price', function ($item) {
                return $item->total_price;
            })
            ->editColumn('boat_id', function ($item) { 
                return BaseHelper::clean($item->boat->ltitle);
            })
            ->editColumn('status', function ($item) {
                return $item->status->toHtml();
            })
            ->editColumn('is_finished', function ($item) {
                return $item->is_finished 
                    ? '<span class="label label-success">Paid</span>' 
                    : '<span class="label label-danger">Saved Boat</span>';
            })
            ->rawColumns(['is_finished']) // Ensure the HTML is rendered            
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            });
            $data = $data->addColumn('operations', function ($item) {
                return $this->getOperations('custom-boat-enquiries.edit', 'custom-boat-enquiries.destroy', $item);
        });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository->getModel()->select([
            'id',
            'user_id',
            'message',
            'boat_id',
            'total_price',
            'status',
            'is_finished',
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
            'boat_id' => [
                'name' => 'boat_id',
                'title' => 'Boat Name',
                'class' => 'text-start',
                'searchable'=>false,
            ],
            'name' => [
                'name' => 'name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-start',
            ],
            'email' => [
                'name' => 'email',
                'title' => 'Email',
                'class' => 'text-start',
            ],
            'phone_number' => [
                'name' => 'phone_number',
                'title' => 'Phone Number',
                'class' => 'text-start',
            ],
            'total_price' => [
                'name' => 'total_price',
                'title' => 'Total Price',
                'class' => 'text-start',
            ],
            'status' => [
                'name' => 'status',
                'title' => trans('core/base::tables.status'),
                'searchable'=>false,
            ],
            'is_finished' => [
                'name' => 'is_finished',
                'title' => trans('core/base::tables.is_finished'),
                'searchable'=>false,
            ]
        ];
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('custom-boat-enquiries.deletes'), 'custom-boat-enquiries.destroy', parent::bulkActions());
    }
}
