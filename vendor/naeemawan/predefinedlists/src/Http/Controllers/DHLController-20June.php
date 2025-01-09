<?php
namespace NaeemAwan\PredefinedLists\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use StdClass;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderHistory;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Payment\Repositories\Interfaces\PaymentInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderHistoryInterface;
use EcommerceHelper;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Base\Http\Responses\BaseHttpResponse;
use OrderHelper;
use EmailHandler;
use Route;
use Auth;
use DateTime;
use Botble\Ecommerce\Events\OrderConfirmedEvent;
use Botble\Payment\Models\Payment;

class DHLController extends BaseController{
    protected $API_KEY = 'YXBFMndUMHBEN3FSMno6RCQzdFokMGZIQDFkTEA3ag==';
    protected $url = 'https://express.api.dhl.com/mydhlapi/test';
    protected $accountnumber = '454997251';
    protected OrderInterface $orderRepository;
    protected OrderHistoryInterface $orderHistoryRepository;
    protected PaymentInterface $paymentRepository;

    public function __construct(
        PaymentInterface $paymentRepository,
        OrderHistoryInterface $orderHistoryRepository,
        OrderInterface $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderHistoryRepository = $orderHistoryRepository;
        $this->paymentRepository = $paymentRepository;
    }

    public function rates(Request $request){
        
        $date=new DateTime;
        $date->modify('+5 days');
        if ($date->format('w') === '0') {
            $date->modify('+1 day'); // Move to the next date
        }
        $formattedDate = $date->format('Y-m-d');
        
        $details = 'accountNumber='.$this->accountnumber.'&weight='.$request->weight.'&length='.$request->length.'&width='.$request->width.'&height='.$request->height.'&plannedShippingDate='.$formattedDate.'&isCustomsDeclarable=false&unitOfMeasurement=metric&originCountryCode='.get_ecommerce_setting('store_country').'&originPostalCode='.get_ecommerce_setting('store_postal_code').'&originCityName='.get_ecommerce_setting('store_city').'&destinationCountryCode='.$request->country.'&destinationPostalCode='.urlencode($request->postal_code).'&destinationCityName='.urlencode($request->city).'';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url.'/rates?'.$details,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
              'Authorization: Basic '.$this->API_KEY,
              'Cookie: BIGipServer~WSB~pl_wsb-express-cbj.dhl.com_443=326904007.64288.0000'
            ),
        ));
        $output = json_decode(curl_exec($curl));
        curl_close($curl);
        if(isset($output->products)){
            $shipping['price'] = $output->products[0]->totalPrice[0]->price;
            $shipping['currency'] = $output->products[0]->totalPrice[0]->priceCurrency;
            $shipping['product_code']=$output->products[0]->productCode;
            $shipping['local_product_code']=$output->products[0]->localProductCode;
        }
        else{
            $shipping['message'] = "Shipping amount is not available at this moment";
        }
        return response()->json($shipping);
    }

    public function shipment(Request $request,BaseHttpResponse $response){
        $order = $this->orderRepository->findOrFail($request->input('order_id'));
        $request->validate([
            'time'   => 'required_if:pickup,1',
        ]);

        $date=new DateTime;
        $date->modify('+5 days');
        if ($date->format('w') === '0') {
            $date->modify('+1 day'); // Move to the next date
        }
        $formattedDate = $date->format('Y-m-d\TH:i:s \G\M\TP');

        $dimensions=getPackageDimensions($order->weight);

        if($request->pickup){
            $shipment=array(
                "plannedPickupDateAndTime" => $formattedDate,
                "closeTime" => $request->time,
                "accounts" => [array(
                    "typeCode" => "shipper",
                    "number" => $this->accountnumber
                )],
                "customerDetails" => array(
                    "shipperDetails" => array(
                        "postalAddress" => array(
                            "postalCode" => get_ecommerce_setting('store_postal_code'),
                            "cityName" => get_ecommerce_setting('store_city'),
                            "countryCode" => get_ecommerce_setting('store_country'),
                            "addressLine1" => get_ecommerce_setting('store_address')
                        ),
                        "contactInformation" => array(
                            "email" => "nooruliman.rizwan@gmail.com", //website ka email lyngy yahan
                            "phone" => "+971508415576", //website ka phone lyngy yahan
                            "companyName" => "Ocean Boats",
                            "fullName" => Auth::user()->first_name
                        )
                    ),
                    "receiverDetails" => array(
                        "postalAddress" => array(
                            "postalCode" => $order->shippingAddress->postal_code,
                            "cityName" => $order->shippingAddress->city,
                            "countryCode" => $order->shippingAddress->country,
                            "addressLine1" => $order->shippingAddress->address
                        ),
                        "contactInformation" => array(
                            "email" => $order->user->email ?: $order->address->email,
                            "phone" => $order->user->phone ?: $order->address->phone,
                            "companyName" => "Ocean Boats",
                            "fullName" => $order->user->name ?: $order->address->name
                        )
                    )
                ),
                "shipmentDetails" => [array(
                    "packages" => [array(
                        "weight" => (float)$order->weight,
                        "dimensions" => array(
                            "length" => $dimensions[0]/10,
                            "width" => $dimensions[1]/10,
                            "height" => $dimensions[2]/10
                        )
                    )],
                    "productCode" => $order->product_code,
                    "localProductCode" => $order->local_product_code,
                    "isCustomsDeclarable" => false,
                    "unitOfMeasurement" => "metric"
                )]
            );
            $url = $this->url.'/pickups';
        }else{
            $shipment=array(
                "plannedShippingDateAndTime" => $formattedDate,
                "pickup" => array(
                    "isRequested" => false
                ),
                "productCode" => $order->product_code,
                "localProductCode" => $order->local_product_code,
                "accounts" => [array(
                    "typeCode" => "shipper",
                    "number" => $this->accountnumber
                )],
                "customerDetails" => array(
                    "shipperDetails" => array(
                        "postalAddress" => array(
                            "postalCode" => get_ecommerce_setting('store_postal_code'),
                            "cityName" => get_ecommerce_setting('store_city'),
                            "countryCode" => get_ecommerce_setting('store_country'),
                            "addressLine1" => get_ecommerce_setting('store_address')
                        ),
                        "contactInformation" => array(
                            "email" => "nooruliman.rizwan@gmail.com", //website ka email lyngy yahan
                            "phone" => "+971508415576", //website ka phone lyngy yahan
                            "companyName" => "Ocean Boats",
                            "fullName" => Auth::user()->first_name
                        )
                    ),
                    "receiverDetails" => array(
                        "postalAddress" => array(
                            "postalCode" => $order->shippingAddress->postal_code,
                            "cityName" => $order->shippingAddress->city,
                            "countryCode" => $order->shippingAddress->country,
                            "addressLine1" => $order->shippingAddress->address
                        ),
                        "contactInformation" => array(
                            "email" => $order->user->email ?: $order->address->email,
                            "phone" => $order->user->phone ?: $order->address->phone,
                            "companyName" => "Ocean Boats",
                            "fullName" => $order->user->name ?: $order->address->name
                        )
                    )
                ),
                "content" => array(
                    "packages" => [array(
                        "weight" => (float)$order->weight,
                        "dimensions" => array(
                            "length" => $dimensions[0]/10,
                            "width" => $dimensions[1]/10,
                            "height" => $dimensions[2]/10
                        )
                    )],
                    "isCustomsDeclarable" => false,
                    "unitOfMeasurement" => "metric",
                    "description" => "shipment"
                ),
                "shipmentNotification" => [array(
                    "typeCode" => "email",
                    "receiverId" => Auth::user()->email, // yahan admin ki email ay gi
                    "languageCode" => "eng",
                    "languageCountryCode" => get_ecommerce_setting('store_country'),
                    "bespokeMessage" => "message to be included in the shipment"
                )]
            );
            $url = $this->url.'/shipments';
        }
        $json = json_encode($shipment);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$json,
            CURLOPT_HTTPHEADER => array(
              'Content-Type: application/json',
              'Authorization: Basic '.$this->API_KEY,
            ),
        ));
        $output = json_decode(curl_exec($curl));
        curl_close($curl);
        if(isset($output->shipmentTrackingNumber) || isset($output->dispatchConfirmationNumbers)){
            $order->dhl_tracking_number = isset($output->shipmentTrackingNumber) ? $output->shipmentTrackingNumber : null;
            $order->dhl_dispatch_number = isset($output->dispatchConfirmationNumbers) ? $output->dispatchConfirmationNumbers[0] : null;
            $order->dhl_shipping_status = isset($output->shipmentTrackingNumber) ? 'Ready for shipment' : 'Ready for pickup';
            $this->orderRepository->createOrUpdate($order);
            return $response->setMessage('Created successfully!');
        }else{
            return $response
                ->setError()
                ->setMessage($output->detail);
        }


    }

    public function track($id,BaseHttpResponse $response){
        $order = $this->orderRepository->findOrFail($id);

        if($order->dhl_tracking_number!=null){

            $curl = curl_init();
            curl_setopt_array($curl, array(
                // CURLOPT_URL => $this->url.'/shipments/'.$order->dhl_tracking_number.'/tracking',
                CURLOPT_URL => $this->url.'/shipments/2234893640/tracking',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic '.$this->API_KEY,
                    'Cookie: BIGipServer~WSB~pl_wsb-express-cbj.dhl.com_443=326904007.64288.0000'
                ),
            ));
            $output = json_decode(curl_exec($curl));
            curl_close($curl);
            if(isset($output->shipments)){
                $lastEvent = end( $output->shipments[0]->events);
                $order->dhl_shipping_status = isset($lastEvent->description) ? $lastEvent->description : null;
                $this->orderRepository->createOrUpdate($order);
                session()->flash('success_msg', 'Order tracked!');
                $response->setMessage("Order Tracked");
                $apiData = json_encode($output->shipments[0]);
                session()->put('apiData', $apiData);
                return redirect()->back();
            }else{
                session()->flash('error_msg', $output->detail);
                $response->setError()->setMessage($output->detail);
                return redirect()->back();
            }

        }else{
            return redirect()->back();
        }
    }
}