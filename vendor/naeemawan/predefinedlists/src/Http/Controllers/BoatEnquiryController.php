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

use NaeemAwan\PredefinedLists\Models\PredefinedList;
use NaeemAwan\PredefinedLists\Models\BoatEnquiry;
use NaeemAwan\PredefinedLists\Models\BoatEnquiryDetail;



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
        // ----------------------------------------------- //

$boat = BoatEnquiry::where(['boat_enquiries.id'=> $id ])
        ->join('predefined_list as p', 'p.id', '=', 'boat_enquiries.boat_id') 
        ->select('boat_enquiries.*', 'p.ltitle')    
        ->with('details')    
        ->first();

        if (! $boat) {
            abort(404);
        }

        //dd($boat->toArray());

        foreach( $boat->details as $details){    
            
            $opt = PredefinedList::where(['id'=> $details->option_id])->select('predefined_list.ltitle')->first()->toArray();
            $details['ltitle'] =$opt['ltitle'];
        }

        $result = BoatEnquiryDetail::join('predefined_list as c', 'boat_enquiry_details.subcat_slug', '=', 'c.type')
        ->join('predefined_list as p', 'c.parent_id', '=', 'p.id')
        ->whereIn('boat_enquiry_details.id',$boat->details->pluck('id')->toArray())
        ->orderBy('p.sort_order','ASC')
        ->orderBy('c.sort_order','ASC')
        ->select('c.id', 'boat_enquiry_details.option_id', 'c.ltitle','c.image', 'boat_enquiry_details.subcat_slug')
        ->with('enquiry_option')
        ->get();

//dd($boat_enquiry,$boat->toArray(), $result->toArray());

$boat_enquiry = $boat;
// ================================================ //

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