<?php
namespace NaeemAwan\PredefinedLists\Http\Controllers;

use Illuminate\Http\Request;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use NaeemAwan\PredefinedLists\Models\BoatEnquiry;
use NaeemAwan\PredefinedLists\Forms\BoatEnquiryForm;
use NaeemAwan\PredefinedLists\Models\PredefinedList;
use NaeemAwan\PredefinedLists\Tables\BoatViewsTable;
use NaeemAwan\PredefinedLists\Tables\BoatEnquiryTable;
use NaeemAwan\PredefinedLists\Models\BoatEnquiryDetail;
use NaeemAwan\PredefinedLists\Http\Requests\PredefinedListRequest;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\BoatEnquiryInterface;

class BoatEnquiryController extends BaseController
{
    protected PredefinedListInterface $predefinedListRepository;

    public function __construct(BoatEnquiryInterface $BoatEnquiryRepository)
    {
        $this->BoatEnquiryRepository = $BoatEnquiryRepository;
    }

    public function index(BoatEnquiryTable $table)
    {
        return $table->renderTable();
    }
    public function botViews(BoatViewsTable $table)
    {
        return $table->renderTable();
    }

    public function edit(int $id, FormBuilder $formBuilder, Request $request)
    {
        $query = BoatEnquiry::join('predefined_list as p', 'p.id', '=', 'boat_enquiries.boat_id')
            ->select('boat_enquiries.*', 'p.ltitle')
            ->with('details');

        if (!auth()->user()->super_user) {
            $query->where(['boat_enquiries.id' => $id, 'boat_enquiries.user_id' => auth('customer')->id()]);
        } else {
            $query->where('boat_enquiries.id', $id);
        }

        $boat_enquiry = $query->first();

        if (!$boat_enquiry) {
            abort(404);
        }

        foreach ($boat_enquiry->details as $details) {
            $opt = PredefinedList::where('id', $details->option_id)
                ->select(
                    'predefined_list.id',
                    'predefined_list.ltitle',
                    'predefined_list.color',
                    'predefined_list.is_standard_option',
                    'predefined_list.file',
                    'predefined_list.type'
                )
                ->first()
                ->toArray();

            $details['ltitle'] = $opt['ltitle'];
            $details['color'] = $opt['color'];
            $details['is_standard_option'] = $opt['is_standard_option'];
            $details['file'] = $opt['file'];
            $details['type'] = $opt['type'];
        }

        $result = BoatEnquiryDetail::join('predefined_list as c', 'boat_enquiry_details.subcat_slug', '=', 'c.type')
            ->join('predefined_list as p', 'c.parent_id', '=', 'p.id')
            ->whereIn('boat_enquiry_details.id', $boat_enquiry->details->pluck('id')->toArray())
            ->orderBy('p.sort_order', 'ASC')
            ->orderBy('c.sort_order', 'ASC')
            ->select(
                'c.id',
                'c.ltitle',
                'c.image',
                'c.color',
                'c.is_standard_option',
                'boat_enquiry_details.option_id',
                'boat_enquiry_details.subcat_slug',
                'boat_enquiry_details.has_discount',
                'boat_enquiry_details.discount_code',
                'boat_enquiry_details.discount_amount'
            )
            ->with('enquiry_option')
            ->get();

        event(new BeforeEditContentEvent($request, $boat_enquiry));

        page_title()->setTitle("View Enquiry");

        return $formBuilder->create(BoatEnquiryForm::class, ['model' => $boat_enquiry])
            ->setFormOption('url', route('custom-boat-enquiries.update', $id))
            ->renderForm();
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