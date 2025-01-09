<?php

namespace Botble\Theme\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Theme\Repositories\Interfaces\ParallelSliderInterface;
use Botble\Theme\Tables\ParallelSliderTable;
use Botble\Base\Forms\FormBuilder;
use Botble\Theme\Forms\ParallelSliderForm;
use Botble\Theme\Http\Requests\ParallelSliderRequest;
use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Illuminate\Http\Request;


class ParallelSliderController extends BaseController{

	protected ParallelSliderInterface $ParallelSliderRepository;

	public function __construct(ParallelSliderInterface $ParallelSliderRepository)
    {
        $this->ParallelSliderRepository = $ParallelSliderRepository;
    }

	public function index(ParallelSliderTable $table){
		return $table->renderTable();
	}

	public function create(FormBuilder $formBuilder,$parent = 0)
    {
        page_title()->setTitle('New Parallel Slider');

        return $formBuilder->create(ParallelSliderForm::class)->setFormOption('url',route('parallel-slider.store'))->renderForm();
    }

    public function store(ParallelSliderRequest $request, BaseHttpResponse $response)
    {
        $parallelslider = $this->ParallelSliderRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(ADS_MODULE_SCREEN_NAME, $request, $parallelslider));

        return $response
            ->setPreviousUrl(route('theme.parallel-slider'))
            ->setNextUrl(route('parallel-slider.edit', $parallelslider->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }


    public function edit(int $id, FormBuilder $formBuilder, Request $request)
    {
        $parallelslider = $this->ParallelSliderRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $parallelslider));

        page_title()->setTitle("Edit");

        return $formBuilder->create(ParallelSliderForm::class, ['model' => $parallelslider])->setFormOption('url',route('parallel-slider.update',$id))->renderForm();
    }

    public function update(int $id, ParallelSliderRequest $request, BaseHttpResponse $response)
    {
        $parallelslider = $this->ParallelSliderRepository->findOrFail($id);

        $parallelslider->fill($request->input());

        $this->ParallelSliderRepository->createOrUpdate($parallelslider);

        event(new UpdatedContentEvent(ADS_MODULE_SCREEN_NAME, $request, $parallelslider));

        return $response
            ->setPreviousUrl(route('theme.parallel-slider'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Request $request, int $id, BaseHttpResponse $response)
    {
        try {
            $parallelslider = $this->ParallelSliderRepository->findOrFail($id);

            $this->ParallelSliderRepository->delete($parallelslider);

            event(new DeletedContentEvent(ADS_MODULE_SCREEN_NAME, $request, $parallelslider));

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
            $parallelslider = $this->ParallelSliderRepository->findOrFail($id);
            $this->ParallelSliderRepository->delete($parallelslider);
            event(new DeletedContentEvent(ADS_MODULE_SCREEN_NAME, $request, $parallelslider));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

}