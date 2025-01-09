<?php
namespace NaeemAwan\PredefinedLists\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use NaeemAwan\PredefinedLists\Models\BoatEnquiry;
use StdClass;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderHistory;
use EcommerceHelper;
use OrderHelper;
use EmailHandler;
use Route;
use Botble\Payment\Models\Payment;

class NgeniusPaymentController extends BaseController{

    protected $API_KEY ='ZTk5ZmNlZDMtNDI3Yy00MDBlLWI4ODEtZmEwYzliNDJjMzQ3OjY4NjQzZTNiLTk5YmEtNGQ3OC05YmJhLTU3YjdjNGEwMmE1NA==';

    protected $outlet_id ='be792cc5-0d7f-412a-85ab-857ee93c7f85';

    protected $url = 'https://api-gateway.ngenius-payments.com';


    public function createtransaction($id){
        $url = $this->url;

        $outlet = $this->outlet_id;

        $postData = new StdClass(); 
        $postData->action = "PURCHASE";
        $postData->amount = new StdClass();
        $postData->amount->currencyCode = get_application_currency()['title'];
        $postData->merchantAttributes = new StdClass();
        $postData->merchantAttributes->cancelUrl = url('/');
        $postData->merchantAttributes->skipConfirmationPage = true;
        $postData->billingAddress = new StdClass();

        $route=Route::current()->uri;

        if($route == 'transaction/{id}'){
            $order = BoatEnquiry::where('id',$id)->first();
            if($order->paypage_url!=null && !$order->is_finished){
                return redirect($order->paypage_url);
            }

            $postData->merchantAttributes->redirectUrl = url('/transaction/success');
            $postData->amount->value = get_ecommerce_setting('down_payment') * 100;
            $postData->merchantOrderReference = 'OBCE-'.$order->id;
            $postData->emailAddress = $order->customer->email;
            $name = explode(" ", $order->customer->name);
        }else{
            $order = Order::where('id',$id)->first();

            $postData->merchantAttributes->redirectUrl = url('/accessories/success');
            $postData->amount->value = $order->amount * 100;
            $postData->merchantOrderReference = 'OBAC-'.$order->id;
            $postData->emailAddress = $order->user->email ?: $order->address->email;
            $name = explode(" ", $order->user->name ?: $order->address->name);
        }

        $postData->billingAddress->firstName= isset($name[0])? $name[0] : ' ';
        $postData->billingAddress->lastName=isset($name[1])? $name[1] : ' ';

        if(cache()->has('access_token')){
            $token=cache()->get('access_token');
        }else{
            $this->accesstoken();
            $token = cache()->get('access_token');
        }
        $json = json_encode($postData);
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url."/transactions/outlets/".$outlet."/orders");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Authorization: Bearer ".$token, 
        "Content-Type: application/vnd.ni-payment.v2+json",
        "Accept: application/vnd.ni-payment.v2+json"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

        $output = json_decode(curl_exec($ch));
        if(isset($output->_id)){
            $order_reference = $output->reference;
            $order_paypage_url = $output->_links->payment->href;
            $order_id = $output->_id;

            if($route == 'transaction/{id}'){
                $order->order_id = $order_id;
                $order->paypage_url = $order_paypage_url;
            }
            $order->reference = $order_reference;
            $order->save();

            return redirect($order_paypage_url);
        }elseif($output->status==401){
            $this->accesstoken();
            $this->createtransaction($order->id);
        }else{
            cache()->put('failure', 1, 60 * 5);
            return redirect()->route('customer.overview');
        }
    }

    public function accesstoken(){
        // dd("under working");
        $url = $this->url;
        $apikey = $this->API_KEY;
        
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url."/identity/auth/access-token"); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "accept: application/vnd.ni-identity.v1+json",
            "authorization: Basic ".$apikey,
            "content-type: application/vnd.ni-identity.v1+json"
        )); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_POSTFIELDS,  '{ "realmName": "networkinternational" }'); 
        $output = json_decode(curl_exec($ch)); 
        $access_token = $output->access_token;
        cache()->put('access_token', $access_token, 60 * 5);
    }


    public function success(Request $request){
        $order = BoatEnquiry::where('reference',$request->ref)->first();
        $url = $this->url;

        $outlet = $this->outlet_id;
        $token = cache()->get('access_token');

        if(cache()->has('access_token')){
            $token=cache()->get('access_token');
        }else{
            $this->accesstoken();
            $token = cache()->get('access_token');
        }

        $curl = curl_init();
        
        curl_setopt_array($curl, array(
        CURLOPT_URL => $url."/transactions/outlets/".$outlet."/orders/".$request->ref,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$token,
            'Content-Type: application/vnd.ni-payment.v2+json',
            'Accept: application/vnd.ni-payment.v2+json',
            'Cookie: _abck=CEE05B874B78F9457623781C7B66FBF5~-1~YAAQBB0gF43Yb0iMAQAA3Yt8VwtknMaMv0rYhqxP+hvgGAhdYwLRcdBMPVBQgWQLK+kD+jstLqSbHCvL8yioBN2I77XPt62myjaA6VvKshQIVFIiWcAtfPoHdHUe7zju7pnKD0colWc83lTtF9k1uyBsi56J/s6sqdN6AFPg0TJi3VIukb1WW0hvgGRcmaOswUCHo+nd4FtKAc0oXuS2nIzidieTRH64DSD1cx7U4lhrQWBsLgrtzg0fMZU7xbHrAdTQjfY7yLqTEVMXPaL+hI4WM3LofQIc+G27Rs8L+A48i2JGoplCSZmdUD9p5jVfAm4SRhkLRHkZFh8BlxEkrkI16g+uzU1wxLFyWgPNtmH/rqv4u98bW6KeTu6GtpZ18u+iMbs9PQQ7TDZR+QbA2LY=~-1~-1~-1; bm_sz=0D01BFE376555F82799DA468869971FD~YAAQBB0gF47Yb0iMAQAA3Yt8VxZZx5318bh9SyY7NqgBlsx2PMIJxze3mXTysPwvt1ezEPOkb2NQUo+VeNLt5Mpx1vg94VxTYBizUD4a/GL0rdOWvXVIyhyf20cJ56TZ60t0y+P8n8Qby44njIwCWAY5nllSzqn+gyEUA16s16bqZw/PyST0cB7ArIAm2rORnvH3g5j9wd+WbTqu1gprmoAvOnDS1BZ8E67nEVBR6Ox1sucUD8obxQvmE26wzHKjhXsD3CKU7kbvKB2JdcIYjLpXASMCtT12tiIIhm9uZuDMrzkyxzWd/TAQGeoa~3421493~3163699'
          ),
         ));

        $output = json_decode(curl_exec($curl));
        if($output){
        if(isset($output->_id)){
            if($output->_embedded->payment[0]->state == 'FAILED'){
                cache()->put('failure', 1, 60 * 5);
                return redirect()->route('customer.overview');
            }
            if($output->_embedded->payment[0]->state == 'PURCHASED'){
                $order->is_finished = 1;
                $order->paid_amount = $output->amount->value / 100;
                $order->save();

                $mailer = EmailHandler::setModule(ECOMMERCE_MODULE_SCREEN_NAME)
                ->setVariableValues([
                    'store_address' => get_ecommerce_setting('store_address'),
                    'store_phone' => get_ecommerce_setting('store_phone'),
                    'customer_name' => $order->customer->name,
                    'product_list' => view('plugins/ecommerce::emails.partials.custom_enquiry', compact('order'))
                        ->render(),
                ]);
                $mailer->send(
                    $mailer->getTemplateContent('custom_enquiry'),
                    $mailer->getTemplateSubject('custom_enquiry'),
                    $order->customer->email
                );

                $mailer2 = EmailHandler::setModule(ECOMMERCE_MODULE_SCREEN_NAME)
                ->setVariableValues([
                    'name' => $order->customer->name,
                    'product' => $order->boat->ltitle,
                ]);
                $mailer2->send(
                    $mailer->getTemplateContent('custom_enquiry_admin'),
                    $mailer->getTemplateSubject('custom_enquiry_admin'),
                    get_admin_email()->toArray()
                );


                cache()->put('payment_success', 1, 60 * 5);
                return redirect()->route('customer.overview');
            }
        }
        elseif($output->status==401){
            $this->accesstoken();
            $this->createtransaction($order->id);
        }
        }else{
            cache()->put('failure', 1, 60 * 5);
            return redirect()->route('customer.overview');
        }
    }

    public function accessoriessuccess(Request $request){
        $order = Order::where('reference',$request->ref)->first();
        $url = $this->url;

        $outlet = $this->outlet_id;
        $token = cache()->get('access_token');

        if(cache()->has('access_token')){
            $token=cache()->get('access_token');
        }else{
            $this->accesstoken();
            $token = cache()->get('access_token');
        }

        $curl = curl_init();
        
        curl_setopt_array($curl, array(
        CURLOPT_URL => $url."/transactions/outlets/".$outlet."/orders/".$request->ref,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$token,
            'Content-Type: application/vnd.ni-payment.v2+json',
            'Accept: application/vnd.ni-payment.v2+json',
            'Cookie: _abck=CEE05B874B78F9457623781C7B66FBF5~-1~YAAQBB0gF43Yb0iMAQAA3Yt8VwtknMaMv0rYhqxP+hvgGAhdYwLRcdBMPVBQgWQLK+kD+jstLqSbHCvL8yioBN2I77XPt62myjaA6VvKshQIVFIiWcAtfPoHdHUe7zju7pnKD0colWc83lTtF9k1uyBsi56J/s6sqdN6AFPg0TJi3VIukb1WW0hvgGRcmaOswUCHo+nd4FtKAc0oXuS2nIzidieTRH64DSD1cx7U4lhrQWBsLgrtzg0fMZU7xbHrAdTQjfY7yLqTEVMXPaL+hI4WM3LofQIc+G27Rs8L+A48i2JGoplCSZmdUD9p5jVfAm4SRhkLRHkZFh8BlxEkrkI16g+uzU1wxLFyWgPNtmH/rqv4u98bW6KeTu6GtpZ18u+iMbs9PQQ7TDZR+QbA2LY=~-1~-1~-1; bm_sz=0D01BFE376555F82799DA468869971FD~YAAQBB0gF47Yb0iMAQAA3Yt8VxZZx5318bh9SyY7NqgBlsx2PMIJxze3mXTysPwvt1ezEPOkb2NQUo+VeNLt5Mpx1vg94VxTYBizUD4a/GL0rdOWvXVIyhyf20cJ56TZ60t0y+P8n8Qby44njIwCWAY5nllSzqn+gyEUA16s16bqZw/PyST0cB7ArIAm2rORnvH3g5j9wd+WbTqu1gprmoAvOnDS1BZ8E67nEVBR6Ox1sucUD8obxQvmE26wzHKjhXsD3CKU7kbvKB2JdcIYjLpXASMCtT12tiIIhm9uZuDMrzkyxzWd/TAQGeoa~3421493~3163699'
          ),
        ));

        $output = json_decode(curl_exec($curl));
        if($output){
        if(isset($output->_id)){

            if($output->_embedded->payment[0]->state == 'FAILED'){
                cache()->put('failure', 1, 60 * 5);
                return redirect('/');
            }
            if($output->_embedded->payment[0]->state == 'PURCHASED' || $output->_embedded->payment[0]->state == 'PARTIALLY_REFUNDED'){

                if (EcommerceHelper::isOrderAutoConfirmedEnabled()) {
                    OrderHistory::create([
                        'action' => 'confirm_order',
                        'description' => trans('plugins/ecommerce::order.order_was_verified_by'),
                        'order_id' => $order->id,
                        'user_id' => 0,
                    ]);
                }

                $payment= New Payment;
                $payment->currency = strtoupper(get_application_currency()->title);
                $payment->amount = (float)format_price($order->amount, null, true);
                $payment->customer_id = $order->user_id;
                $payment->payment_channel = cache()->get('ngenius_method');
                $payment->status = 'completed';
                $payment->order_id = $order->id;
                $payment->customer_type = 'Botble\Ecommerce\Models\Customer';
                $payment->charge_id = $order->reference;
                $payment->save();

                $order->is_finished = true;
                $order->is_confirmed = true;
                $order->payment_id = $payment->id;
                $order->save();

                OrderHelper::decreaseProductQuantity($order);

                OrderHelper::clearSessions($token);

                $mailer = EmailHandler::setModule(ECOMMERCE_MODULE_SCREEN_NAME);
                if ($mailer->templateEnabled('order_confirm')) {
                    OrderHelper::setEmailVariables($order);
                    $mailer->sendUsingTemplate(
                        'order_confirm',
                        $order->user->email ?: $order->address->email
                    );
                }

                $mailer2 = EmailHandler::setModule(ECOMMERCE_MODULE_SCREEN_NAME);
                if ($mailer2->templateEnabled('admin_new_order')) {
                    OrderHelper::setEmailVariables($order);
                    $mailer2->sendUsingTemplate('admin_new_order', get_admin_email()->toArray());
                }

                $products = collect([]);

                $productsIds = $order->products->pluck('product_id')->all();

                if (! empty($productsIds)) {
                    $products = get_products([
                        'condition' => [
                            ['ec_products.id', 'IN', $productsIds],
                        ],
                        'select' => [
                            'ec_products.id',
                            'ec_products.images',
                            'ec_products.name',
                            'ec_products.price',
                            'ec_products.sale_price',
                            'ec_products.sale_type',
                            'ec_products.start_date',
                            'ec_products.end_date',
                            'ec_products.sku',
                            'ec_products.order',
                            'ec_products.created_at',
                            'ec_products.is_variation',
                        ],
                        'with' => [
                            'variationProductAttributes',
                        ],
                        ]);
                    }

                    return view('plugins/ecommerce::orders.thank-you', compact('order', 'products'));
            }

        }elseif($output->status==401){
            $this->accesstoken();
            $this->createtransaction($order->id);
        }
        }else{
            cache()->put('failure', 1, 60 * 5);
            session()->flash('error_msg', 'Something went wrong. Your payment is not accepted!');
            return redirect('/');
        }
    }


	
}
