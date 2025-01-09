<?php

namespace NaeemAwan\PredefinedLists\Http\Controllers;

use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\Helper;
use Botble\Base\Events\AdminNotificationEvent;
use Botble\Base\Supports\AdminNotificationItem;
use Botble\Ecommerce\Events\ProductViewed;
use Botble\Ecommerce\Http\Resources\ProductVariationResource;
use Botble\Ecommerce\Models\Brand;
use NaeemAwan\PredefinedLists\Models\PredefinedList;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Ecommerce\Models\ProductTag;
use Botble\Ecommerce\Repositories\Interfaces\BrandInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductAttributeSetInterface;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\PredefinedCategoryInterface;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\PredefinedListInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductTagInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductVariationInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductVariationItemInterface;
use NaeemAwan\PredefinedLists\Services\GetProductService;
use Botble\SeoHelper\SeoOpenGraph;
use Botble\Slug\Repositories\Interfaces\SlugInterface;
use Carbon\Carbon;
use EcommerceHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use ProductCategoryHelper;
use RvMedia;
use SeoHelper;
use EmailHandler;
use SlugHelper;
use Theme;
use OrderHelper;
use NaeemAwan\PredefinedLists\Models\BoatEnquiry;

class PublicProductController
{
    protected PredefinedListInterface $productRepository;

    protected PredefinedCategoryInterface $productCategoryRepository;

    public function __construct(
        PredefinedListInterface $productRepository,
        PredefinedCategoryInterface $productCategoryRepository,
    ) {
        $this->productRepository = $productRepository;
        $this->productCategoryRepository = $productCategoryRepository;
    }

    public function getProducts(Request $request, GetProductService $productService, BaseHttpResponse $response)
    {
        if (! EcommerceHelper::productFilterParamsValidated($request)) {
            return $response->setNextUrl(route('public.customize-boat'));
        }

        $query = BaseHelper::stringify($request->input('q'));

        $with = [];

        if ($query && ! $request->ajax()) {
            $products = $productService->getProduct($request, null, null, $with);

            SeoHelper::setTitle(__('Search result for ":query"', compact('query')));

            Theme::breadcrumb()
                ->add(__('Home'), route('public.index'))
                ->add(__('Search'), route('public.customize-boat'));

            SeoHelper::meta()->setUrl(route('public.customize-boat'));

            return Theme::scope(
                'ecommerce.search',
                compact('products', 'query'),
                'plugins/ecommerce::themes.search'
            )->render();
        }

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Build a Boat'), route('public.customize-boat'));

        if(isset($request->cat_id)){
            $products = $productService->getProduct($request, $request->cat_id, null, $with);
        }else{
            $products = $productService->getProduct($request, null, null, $with);
        }

        if ($request->ajax()) {
            return $this->ajaxFilterProductsResponse($products, $request, $response);
        }

        SeoHelper::setTitle(__('Build a boat'))->setDescription(__('Build a boat'));

        do_action(PRODUCT_MODULE_SCREEN_NAME);

        return Theme::scope(
            'ecommerce.boats',
            compact('products'),
            'plugins/ecommerce::themes.boats'
        )->render();
    }

    public function getProduct($id,Request $request)
    {
        $product = $this->productRepository->findOrFail($id);

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Build a Boat'), route('public.customize-boat'))
            ->add($product->ltitle, route('public.customize-boat.id',$product->id));
            
        return Theme::scope('ecommerce.boat',compact('product'),'plugins/ecommerce::themes.boat')->render();
    }

    public function getTypeContent(Request $request){
        $category=PredefinedList::where('id',$request->value)->first();
        return $category;
    }

    protected function ajaxFilterProductsResponse($products, Request $request, BaseHttpResponse $response, ?ProductCategory $category = null)
    {
        $total = $products->total();
        $message = $total > 1 ? __(':total Products found', compact('total')) : __(
            ':total Product found',
            compact('total')
        );

        $view = Theme::getThemeNamespace('views.ecommerce.includes.boat-items');

        if (! view()->exists($view)) {
            $view = 'plugins/ecommerce::themes.includes.boat-items';
        }


        return $response
            ->setData(view($view, compact('products'))->render());
    }


    public function SubmitBoat(Request $request){
        $enquiry=new BoatEnquiry;
        $enquiry->name=$request->name;
        $enquiry->email=$request->email;
        $enquiry->phone_number=$request->phone_number;
        $enquiry->message=$request->message;
        $enquiry->boat_id=$request->boat_id;
        $enquiry->status='unread';
        $enquiry->total_price=$request->total_price;
        $enquiry->color=isset($request->color) ? $request->color : null;
        $enquiry->motor=isset($request->motor) ? $request->motor : null;
        $enquiry->trailor=isset($request->trailor) ? $request->trailor: null;
        $enquiry->canvas_covers=isset($request->options[1]) ? $request->options[1]: null;
        $enquiry->fishing_locator=isset($request->options[2]) ? $request->options[2]: null;
        $enquiry->general=isset($request->options[3]) ? $request->options[3]: null;
        $enquiry->save();

        $mailer = EmailHandler::setModule(ECOMMERCE_MODULE_SCREEN_NAME)
        ->setVariableValues([
            'customer_name' => $enquiry->name,
            'product_list' => view('plugins/ecommerce::emails.partials.custom_enquiry', compact('enquiry'))
                ->render(),
        ]);
        $mailer->send(
            $mailer->getTemplateContent('custom_enquiry'),
            $mailer->getTemplateSubject('custom_enquiry'),
            $enquiry->email
        );

        $mailer2 = EmailHandler::setModule(ECOMMERCE_MODULE_SCREEN_NAME)
        ->setVariableValues([
            'name' => $enquiry->name,
            'product' => $enquiry->boat->ltitle,
        ]);
        $mailer2->send(
            $mailer->getTemplateContent('custom_enquiry_admin'),
            $mailer->getTemplateSubject('custom_enquiry_admin'),
            theme_option('contact_email'),
        );


        event(new AdminNotificationEvent(
            AdminNotificationItem::make()
                ->title('Buil a boat enquiry submitted')
                ->description(trans('plugins/ecommerce::order.notifications.enquiry', [
                    'customer' => $enquiry->name,
                    'product' => $enquiry->boat->ltitle,
                ]))
                ->action(trans('plugins/ecommerce::order.notifications.view'), route('custom-boat-enquiries.edit', $enquiry->id))
        ));

    }


}
