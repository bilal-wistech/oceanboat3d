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
use NaeemAwan\PredefinedLists\Forms\PDLDiscountForm;
use NaeemAwan\PredefinedLists\Tables\PDLDiscountTable;
use NaeemAwan\PredefinedLists\Http\Requests\PDLDiscountRequest;
use NaeemAwan\PredefinedLists\Http\Requests\PredefinedCategoryRequest;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\PDLDiscountInterface;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\PredefinedListInterface;

class PDLDiscountController extends BaseController
{
    protected PDLDiscountInterface $pdlDiscountRepository;
    protected PredefinedListInterface $predefinedListRepository;

    public function __construct(PDLDiscountInterface $pdlDiscountRepository, PredefinedListInterface $predefinedListRepository)
    {
        $this->pdlDiscountRepository = $pdlDiscountRepository;
        $this->predefinedListRepository = $predefinedListRepository;
    }

    public function index(PDLDiscountTable $table)
    {
        return $table->renderTable();
    }
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle('Predefine List Discount Create');

        return $formBuilder->create(PDLDiscountForm::class)->setFormOption('url', route('custom-boat-discounts.store'))->renderForm();
    }

    public function store(PDLDiscountRequest $request, BaseHttpResponse $response)
    {
        // Extracting the list_id values from the request
        $input = $request->all();
        $listIds = $input['list_id'];
        unset($input['list_id']); // Remove list_id from input since we'll handle it separately

        $discounts = [];
        foreach ($listIds as $listId) {
            $input['list_id'] = $listId; // Set the current list_id
            $discounts[] = $this->pdlDiscountRepository->createOrUpdate($input); // Create or update the discount
        }

        foreach ($discounts as $pdlDiscount) {
            event(new CreatedContentEvent(ADS_MODULE_SCREEN_NAME, $request, $pdlDiscount));
        }

        return $response
            ->setPreviousUrl(route('custom-boat-discounts'))
            ->setNextUrl(route('custom-boat-discounts.create'))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }



    public function edit(int $id, FormBuilder $formBuilder, Request $request)
    {
        $pdlDiscount = $this->pdlDiscountRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $pdlDiscount));

        page_title()->setTitle("Edit");

        return $formBuilder->create(PDLDiscountForm::class, ['model' => $pdlDiscount])->setFormOption('url', route('custom-boat-discounts.update', $id))->renderForm();
    }

    public function update(int $id, PDLDiscountRequest $request, BaseHttpResponse $response)
    {
        // Extracting the list_id values from the request
        $input = $request->all();
        $listIds = $input['list_id'];
        unset($input['list_id']); // Remove list_id from input since we'll handle it separately

        // Find and delete the existing discount records for the given id
        $this->pdlDiscountRepository->deleteBy(['discount_id' => $id]);

        // Create new discount records for the updated list of list_id values
        $discounts = [];
        foreach ($listIds as $listId) {
            $input['list_id'] = $listId;
            $discounts[] = $this->pdlDiscountRepository->createOrUpdate($input);
        }

        foreach ($discounts as $pdlDiscount) {
            event(new UpdatedContentEvent(ADS_MODULE_SCREEN_NAME, $request, $pdlDiscount));
        }

        return $response
            ->setPreviousUrl(route('custom-boat-discounts', $id))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }
    public function destroy(Request $request, int $id, BaseHttpResponse $response)
    {
        try {
            $pdlDiscount = $this->pdlDiscountRepository->findOrFail($id);

            $this->pdlDiscountRepository->delete($pdlDiscount);

            event(new DeletedContentEvent(ADS_MODULE_SCREEN_NAME, $request, $pdlDiscount));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }
    public function getAccessories(Request $request)
    {
        $boatIds = $request->input('boats', []);

        if (empty($boatIds)) {
            return response()->json(['success' => false, 'message' => 'No boats selected.']);
        }

        $accessories = [];
        foreach ($boatIds as $boatId) {
            $boat = $this->predefinedListRepository->findById($boatId);

            if (!$boat) {
                return response()->json(['success' => false, 'message' => "Boat with ID $boatId not found."]);
            }

            $boat_cats = $boat->childitems_display();

            foreach ($boat_cats as $boat_cat) {
                $boat_cat_data = [
                    'id' => $boat_cat->id,
                    'ltitle' => $boat_cat->ltitle,
                    'sub_categories' => []
                ];
            
                foreach ($boat_cat->childitems() as $boat_sub_cat) {
                    $boat_sub_cat_data = [
                        'id' => $boat_sub_cat->id,
                        'ltitle' => $boat_sub_cat->ltitle,
                        'sub_sub_categories' => []
                    ];
            
                    foreach ($boat_sub_cat->childitems() as $boat_sub_sub_cat) {
                        $boat_sub_sub_cat_data = [
                            'id' => $boat_sub_sub_cat->id,
                            'ltitle' => $boat_sub_sub_cat->ltitle,
                        ];
            
                        $boat_sub_cat_data['sub_sub_categories'][] = $boat_sub_sub_cat_data;
                    }
            
                    $boat_cat_data['sub_categories'][] = $boat_sub_cat_data;
                }
            
                $accessories[] = $boat_cat_data;
            }
            
        }

        return response()->json([
            'success' => true,
            'accessories' => $accessories,
        ]);
    }

}