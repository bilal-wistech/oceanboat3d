<?php

namespace NaeemAwan\PredefinedLists\Tables;

use Auth;
use BaseHelper;
use NaeemAwan\PredefinedLists\Models\PredefinedList;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\PredefinedListInterface;
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

class PredefinedListTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = false;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, PredefinedListInterface $predefinedlistRepository)
    {
        $this->repository = $predefinedlistRepository;
        parent::__construct($table, $urlGenerator);

        if (! Auth::user()->hasAnyPermission(['predefined-list.edit', 'predefined-list.destroy'])) {
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
                    RvMedia::getImageUrl($item->main_image, 'thumb', false, RvMedia::getDefaultImage()),
                    $item->name,
                    ['width' => 50]
                );
            })
            ->editColumn('name', function ($item) {
                if (! Auth::user()->hasPermission('predefined-list.edit')) {
                    return BaseHelper::clean($item->ltitle);
                }

                return Html::link(route('predefined-list.edit', $item->id), BaseHelper::clean($item->ltitle));
            })
            ->editColumn('category', function ($item) {
                if($item->detail!=null){
                    return Html::link(route('predefined-categories.edit', $item->detail->category_id), BaseHelper::clean($item->detail->category->name));
                }
                return $item->type;
            })
            ->editColumn('sub_options', function ($item) {
                return $item->subOptionsCount();
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('status', function ($item) {
                return $item->status==1 ? 'Enable' : 'Disable';
            });
            $data = $data->addColumn('operations', function ($item) {
                if(currentPDLLevel($this->getOption('id'),1)<=config('predefinedlists.levels') && !(currentPDLLevel($this->getOption('id'),1)==2)){
                    return $this->getOperations('predefined-list.edit', 'predefined-list.destroy', $item,"<a href='".route('predefined-list.parent',$item->id)."' class='btn btn-icon btn-sm btn-info'><i class='fa fa-list'></i></a>");
                }elseif(currentPDLLevel($this->getOption('id'),1)==2){
                    return $this->getOperations(null,null,$item,"<a href='".route('predefined-list.parent',$item->id)."' class='btn btn-icon btn-sm btn-info'><i class='fa fa-list'></i></a>");
                }
                else{
                    return $this->getOperations('predefined-list.edit', 'predefined-list.destroy', $item);
                }
        });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository->getModel()->where('parent_id',$this->getOption('id'))->select([
            'predefined_list.id',
            'predefined_list.ltitle',
            'predefined_list.main_image',
            'predefined_list.descp',
            'predefined_list.parent_id',
            'predefined_list.type',
            'predefined_list.status',
        ]);
        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            'id' => [
                'name' => 'predefined_list.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'image' => [
                'name' => 'predefined_list.main_image',
                'title' => trans('core/base::tables.image'),
                'width' => '70px',
            ],
            'name' => [
                'name' => 'predefined_list.ltitle',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-start',
            ],
            'category' => [
                'name' => 'predefined_list.category',
                'title' => 'Category',
                'class' => 'text-start',
                'searchable'=>false,
            ],
            'sub_options' => [
                'name' => 'predefined_list.sub_options',
                'title' => 'Sub Options',
                'class' => 'text-start',
                'searchable'=>false,
            ],
            'status' => [
                'name' => 'predefined_list.status',
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
                'searchable'=>false,
            ],
        ];
    }

    public function buttons(): array
    {
        $buttons=[];
        if(!(currentPDLLevel($this->getOption('id'),1)==2)){
            $buttons = $this->addCreateButton(route('predefined-list.create.parent',$this->getOption('id')), 'predefined-list.create');
        }

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, PredefinedLists::class);
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('predefined-list.deletes'), 'predefined-list.destroy', parent::bulkActions());
    }

    public function getFilters(): array
    {
        return $this->getBulkChanges();
    }

    public function getBulkChanges(): array
    {
        return [
            'predefined_list.ltitle' => [
                'title' => trans('core/base::tables.name'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'predefined_list.status' => [
                'title' => trans('core/base::tables.status'),
                'type' => 'select',
                'choices' => getStatusArr(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
        ];
    }
}
