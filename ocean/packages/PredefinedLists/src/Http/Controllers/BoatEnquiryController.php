<?php
namespace NaeemAwan\PredefinedLists\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\BoatEnquiryInterface;
use NaeemAwan\PredefinedLists\Tables\BoatEnquiryTable;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use NaeemAwan\PredefinedLists\Http\Requests\PredefinedListRequest;
use NaeemAwan\PredefinedLists\Forms\BoatEnquiryForm;
use Illuminate\Http\Request;

class BoatEnquiryController extends BaseController{
	protected PredefinedListInterface $predefinedListRepository;

	public function __construct(BoatEnquiryInterface $BoatEnquiryRepository)
    {
        $this->BoatEnquiryRepository = $BoatEnquiryRepository;
    }

    public function index(BoatEnquiryTable $table)
    {
        return $table->renderTable();
    }

    public function edit(int $id, FormBuilder $formBuilder, Request $request)
    {
        $boat_enquiry = $this->BoatEnquiryRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $boat_enquiry));

        page_title()->setTitle("View Enquiry");

        return $formBuilder->create(BoatEnquiryForm::class, ['model' => $boat_enquiry])->setFormOption('url',route('custom-boat-enquiries.update',$id))->renderForm();
    }

    public function update(int $id, Request $request, BaseHttpResponse $response)
    {
        $boat_enquiry = $this->BoatEnquiryRepository->findOrFail($id);

        $boat_enquiry->fill($request->input());

        $this->BoatEnquiryRepository->createOrUpdate($boat_enquiry);

        event(new UpdatedContentEvent(CONTACT_MODULE_SCREEN_NAME, $request, $boat_enquiry));

        return $response
            ->setPreviousUrl(route('custom-boat-enquiries'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Request $request, int $id, BaseHttpResponse $response)
    {
        try {
            $boat_enquiry = $this->BoatEnquiryRepository->findOrFail($id);

            $this->BoatEnquiryRepository->delete($boat_enquiry);

            event(new DeletedContentEvent(ADS_MODULE_SCREEN_NAME, $request, $boat_enquiry));

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
            $boat_enquiry = $this->BoatEnquiryRepository->findOrFail($id);
            $this->BoatEnquiryRepository->delete($boat_enquiry);
            event(new DeletedContentEvent(ADS_MODULE_SCREEN_NAME, $request, $boat_enquiry));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}