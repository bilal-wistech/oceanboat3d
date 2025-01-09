<?php
namespace NaeemAwan\PredefinedLists\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\PredefinedListInterface;
use NaeemAwan\PredefinedLists\Tables\PredefinedListTable;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use NaeemAwan\PredefinedLists\Http\Requests\PredefinedListRequest;
use NaeemAwan\PredefinedLists\Forms\PredefinedListForm;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PredefinedListController extends BaseController{
	protected PredefinedListInterface $predefinedListRepository;

	public function __construct(PredefinedListInterface $predefinedListRepository)
    {
        $this->predefinedListRepository = $predefinedListRepository;
    }

    public function index(PredefinedListTable $table, $parent=0)
    {   
        return $table->renderTable(['id' => $parent]);
    }

    public function create(FormBuilder $formBuilder,$parent = 0)
    {
        page_title()->setTitle('Predefined List Create');

        return $formBuilder->create(PredefinedListForm::class, ['parent' => $parent])->setFormOption('url',route('predefined-list.store',$parent))->renderForm();
    }

    public function store(PredefinedListRequest $request, BaseHttpResponse $response)
    {
        $slug = Str::slug($request->ltitle, '-');
        $request=request()->merge(['type' => $slug]);

        $predefinedList = $this->predefinedListRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(ADS_MODULE_SCREEN_NAME, $request, $predefinedList));

        return $response
            ->setPreviousUrl(route('predefined-list.parent',$predefinedList->parent_id))
            ->setNextUrl(route('predefined-list.edit', $predefinedList->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(int $id, FormBuilder $formBuilder, Request $request)
    {
        $predefinedList = $this->predefinedListRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $predefinedList));

        page_title()->setTitle("Edit");

        return $formBuilder->create(PredefinedListForm::class, ['model' => $predefinedList,'parent'=>$predefinedList->parent_id])->setFormOption('url',route('predefined-list.update',$id))->renderForm();
    }

    public function update(int $id, PredefinedListRequest $request, BaseHttpResponse $response)
    {
        $slug = Str::slug($request->ltitle, '-');
        $request=request()->merge(['type' => $slug]);

        $predefinedList = $this->predefinedListRepository->findOrFail($id);

        $predefinedList->fill($request->input());

        $this->predefinedListRepository->createOrUpdate($predefinedList);
        // this will fire the "updated" event even if no fields were updated. This will update the model's updated_at timestamp.
        $predefinedList->touch();

        event(new UpdatedContentEvent(ADS_MODULE_SCREEN_NAME, $request, $predefinedList));

        return $response
            ->setPreviousUrl(route('predefined-list',$predefinedList->parent_id))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Request $request, int $id, BaseHttpResponse $response)
    {
        try {
            $predefinedList = $this->predefinedListRepository->findOrFail($id);

            $this->predefinedListRepository->delete($predefinedList);

            event(new DeletedContentEvent(ADS_MODULE_SCREEN_NAME, $request, $predefinedList));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        foreach ($ids as $id) {
            $predefinedList = $this->predefinedListRepository->findOrFail($id);
            $this->predefinedListRepository->delete($predefinedList);
            event(new DeletedContentEvent(ADS_MODULE_SCREEN_NAME, $request, $predefinedList));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}