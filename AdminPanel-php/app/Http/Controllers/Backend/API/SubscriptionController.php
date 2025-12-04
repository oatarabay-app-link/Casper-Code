<?php


namespace App\Http\Controllers\Backend\API;


use App\Http\Controllers\Controller;
use App\Models\Traits\Uuid;
use App\Payments_Check;
use App\UserSubscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \App\Subscription;
use \Google;


/**
 * Class PaymentController
 * @package App\Http\Controllers\Backend\API
 */
class SubscriptionController extends Controller
{

    /*
     * @Route /api/subscriptions
     * @Returns JSON Reporse for Availble Subscriptions
     */
    public function list_subscriptions(Request $request)
    {

        $subscriptions = Subscription::all();
        $result = array();
        $result["code"] = "success";
        $subs = array();
        foreach ($subscriptions as $sub) {
            $item = array(
                'subscriptionId' => $sub->uuid,
                'subscriptionName' => $sub->subscription_name,
                'monthlyPrice' => $sub->monthly_price,
                'periodPrice' => $sub->period_price,
                'currency_type' => $sub->currency_type,
                'trafficSize' => $sub->traffic_size,
                'rateLimit' => $sub->rate_limit,
                'maxConnections' => $sub->max_connections,
                'availableForAndroid' => $sub->available_for_android== 0 ? false:true,
                'availableForIos' => $sub->available_for_ios== 0 ? false:true,
                'create_time' => Carbon::parse( $sub->create_time)->timestamp,
                'isDefault' => $sub->is_default== 0 ? false:true,
                'periodLength' => $sub->period_length,
                'order_num' => $sub->order_num,
                'productId' => $sub->product_id,
                'numServers' => null,
                "numCountries" => null,
                "discount" => "0.00",
                "monthlyPriceDiscount" => $sub->monthly_price,
                "protocols" => [
                    "L2TP",
                    "PPTP",
                    "SSTP",
                    "OPEN_VPN",
                    "IKEV2"
                ],
            );
             array_push($subs,$item);

        }
        $result['data']=$subs;
        return response(json_encode($result, JSON_PRETTY_PRINT), 200)
            ->header('Content-Type', 'application/json');

    }

}