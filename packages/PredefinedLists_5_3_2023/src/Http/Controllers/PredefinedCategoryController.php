<?php
namespace NaeemAwan\PredefinedLists\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\PredefinedCategoryInterface;
use NaeemAwan\PredefinedLists\Tables\PredefinedCategoryTable;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use NaeemAwan\PredefinedLists\Http\Requests\PredefinedCategoryRequest;
use NaeemAwan\PredefinedLists\Forms\PredefinedCategoryForm;
use Illuminate\Http\Request;

class PredefinedCategoryController extends BaseController{
	protected PredefinedCategoryInterface $predefinedcategoryRepository;

	public function __construct(PredefinedCategoryInterface $predefinedcategoryRepository)
    {
        $this->predefinedcategoryRepository = $predefinedcategoryRepository;
    }

    public function index(PredefinedCategoryTable $table)
    {
        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle('Predefined Category Create');

        return $formBuilder->create(PredefinedCategoryForm::class)->setFormOption('url',route('predefined-categories.store'))->renderForm();
    }

    public function store(PredefinedCategoryRequest $request, BaseHttpResponse $response)
    {
        $predefinedcategory = $this->predefinedcategoryRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(ADS_MODULE_SCREEN_NAME, $request, $predefinedcategory));

        return $response
            ->setPreviousUrl(route('predefined-categories'))
            ->setNextUrl(route('predefined-categories.edit', $predefinedcategory->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(int $id, FormBuilder $formBuilder, Request $request)
    {
        $predefinedcategory = $this->predefinedcategoryRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $predefinedcategory));

        page_title()->setTitle("Edit");

        return $formBuilder->create(PredefinedCategoryForm::class, ['model' => $predefinedcategory])->setFormOption('url',route('predefined-categories.update',$id))->renderForm();
    }

    public function update(int $id, PredefinedCategoryRequest $request, BaseHttpResponse $response)
    {
        $predefinedcategory = $this->predefinedcategoryRepository->findOrFail($id);

        $predefinedcategory->fill($request->input());

        $this->predefinedcategoryRepository->createOrUpdate($predefinedcategory);

        event(new UpdatedContentEvent(ADS_MODULE_SCREEN_NAME, $request, $predefinedcategory));

        return $response
            ->setPreviousUrl(route('predefined-categories',$predefinedcategory->parent_id))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Request $request, int $id, BaseHttpResponse $response)
    {
        try {
            $predefinedcategory = $this->predefinedcategoryRepository->findOrFail($id);

            $this->predefinedcategoryRepository->delete($predefinedcategory);

            event(new DeletedContentEvent(ADS_MODULE_SCREEN_NAME, $request, $predefinedcategory));

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
            $predefinedcategory = $this->predefinedcategoryRepository->findOrFail($id);
            $this->predefinedcategoryRepository->delete($predefinedcategory);
            event(new DeletedContentEvent(ADS_MODULE_SCREEN_NAME, $request, $predefinedcategory));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}