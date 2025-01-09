<?php

namespace Botble\Theme\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Theme\Repositories\Interfaces\VideoBackgroundInterface;
use Botble\Theme\Tables\VideoBackgroundTable;
use Botble\Base\Forms\FormBuilder;
use Botble\Theme\Forms\VideoBackgroundForm;
use Botble\Theme\Http\Requests\VideoBackgroundRequest;
use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Illuminate\Http\Request;


class VideoBackgroundController extends BaseController{

	protected VideoBackgroundInterface $VideoBackgroundRepository;

	public function __construct(VideoBackgroundInterface $VideoBackgroundRepository)
    {
        $this->VideoBackgroundRepository = $VideoBackgroundRepository;
    }

	public function index(VideoBackgroundTable $table){
		return $table->renderTable();
	}

	public function create(FormBuilder $formBuilder,$parent = 0)
    {
        page_title()->setTitle('New Parallel Slider');

        return $formBuilder->create(VideoBackgroundForm::class)->setFormOption('url',route('video-background.store'))->renderForm();
    }

    public function store(VideoBackgroundRequest $request, BaseHttpResponse $response)
    {
        $videoBackground = $this->VideoBackgroundRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(ADS_MODULE_SCREEN_NAME, $request, $videoBackground));

        return $response
            ->setPreviousUrl(route('theme.video-background'))
            ->setNextUrl(route('video-background.edit', $videoBackground->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }


    public function edit(int $id, FormBuilder $formBuilder, Request $request)
    {
        $videoBackground = $this->VideoBackgroundRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $videoBackground));

        page_title()->setTitle("Edit");

        return $formBuilder->create(VideoBackgroundForm::class, ['model' => $videoBackground])->setFormOption('url',route('video-background.update',$id))->renderForm();
    }

    public function update(int $id, VideoBackgroundRequest $request, BaseHttpResponse $response)
    {
        $videoBackground = $this->VideoBackgroundRepository->findOrFail($id);

        $videoBackground->fill($request->input());

        $this->VideoBackgroundRepository->createOrUpdate($videoBackground);

        event(new UpdatedContentEvent(ADS_MODULE_SCREEN_NAME, $request, $videoBackground));

        return $response
            ->setPreviousUrl(route('theme.video-background'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Request $request, int $id, BaseHttpResponse $response)
    {
        try {
            $videoBackground = $this->VideoBackgroundRepository->findOrFail($id);

            $this->VideoBackgroundRepository->delete($videoBackground);

            event(new DeletedContentEvent(ADS_MODULE_SCREEN_NAME, $request, $videoBackground));

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
            $videoBackground = $this->VideoBackgroundRepository->findOrFail($id);
            $this->VideoBackgroundRepository->delete($videoBackground);
            event(new DeletedContentEvent(ADS_MODULE_SCREEN_NAME, $request, $videoBackground));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

}