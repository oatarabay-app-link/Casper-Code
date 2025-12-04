<?php


namespace App\Http\Controllers\Backend\API;


use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Notifications\Frontend\Auth\UserNeedsConfirmation;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use App\Repositories\Backend\Auth\UserRepository;
use Illuminate\Support\Facades\Password;
class UsersController extends Controller
{
    use SendsPasswordResetEmails;

    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    public function affiliate_ref($aff_ref, Request $request)
    {

        //$aff_ref = $request->query('aff_ref');
        $user = $request->user();
        $result = array();
        if (is_numeric($aff_ref)) {
            if ($user->affliate_ref == "") {
                $user->affliate_ref = $aff_ref;
                $user->save();
                $result["data"] = "success";
            } else {
                $result["data"] = "fail";
            }
            $result["code"] = "success";
        } else {
            $result["data"] = "fail";
            $result["code"] = "success";
        }


        return response()->json($result, 200);

    }

    /*
     * Routes are
     *
     */
    public function user_profile(Request $request)
    {

        $result = array();
        $user = $request->user();


        //$user_sub =  $user->subscription->subscriptions->name;
        //dd($user->subscription->subscriptions->subscription_name);


        $data = array(

            "id" => $user->uuid,
            "login" => $user->email,
            "phone" => $user->phone,
            "blocked" => $user->is_blocked,
            "createTime" => Carbon::parse($user->create_time)->timestamp,
            "firstName" => $user->first_name,
            "lastName" => $user->last_name,
            "description" => $user->description,
            "userRoleType" => $user->roles_label, //Accessor Method
            "subscriptionData" => array(
                "subscriptionId" => $user->subscription->subscriptions->uuid,
                "subscriptionName" => $user->subscription->subscriptions->subscription_name,
                "monthlyPrice" => $user->subscription->subscriptions->monthly_price,
                "periodPrice" => $user->subscription->subscriptions->period_price,
                "periodLength" => $user->subscription->subscriptions->period_length,
                "trafficSize" => $user->subscription->subscriptions->traffic_size,
                "rateLimit" => $user->subscription->subscriptions->rate_limit,
                "maxConnections" => $user->subscription->subscriptions->max_connections,
                "numServers" => 0,
                "numCountries" => 0,
                "availableForAndroid" => (bool)$user->subscription->subscriptions->available_for_android,
                "availableForIos" => (bool)$user->subscription->subscriptions->available_for_ios,
                "createTime" => Carbon::parse($user->subscription->subscriptions->create_time)->timestamp,
                "isDefault" => (bool)$user->subscription->subscriptions->is_default,
                "protocols" => [
                    "PPTP",
                    "OPEN_VPN",
                    "SSTP",
                    "L2TP",
                    "IKEV2"
                ],
                "discount" => 0.00,
                "monthlyPriceDiscount" => $user->subscription->subscriptions->monthly_price,
                "productId" => $user->subscription->subscriptions->product_id,
            ),
            "subscription" => array(
                "subscriptionId" => $user->subscription->subscriptions->uuid,
                "subscriptionStartDate" => Carbon::parse($user->subscription->subscription_start_date)->timestamp,
                //"subscriptionEndDate" =>(Carbon::parse($user->getExpiryDate())->timestamp * 1000),//Carbon::parse($user->subscription->subscription_end_date)->timestamp,

                "vpnPassword" => $user->subscription->vpn_pass
            ),
            "role" => $user->roles_label, //Accessor Method

        );

        $result["data"] = $data;
        $result["code"] = "success";


        return response()->json($result, 200);

    }



    public function register_user(Request $request)
    {

        //Logging the Request to Log file
        \Log::debug($request);
        // Getting the Request to Decode According to Casper VPN App requirements
        $json = $request->getContent();
        $json = json_decode($json);
        // varibales to reutrn in response
        $code="fail";
        $data=null;





        $data=array(
            "email" => $json->login,
            "password" => $json->password,
            "first_name" => "",
            "last_name"  => "",
            "roles" => array("subscriber")
        );

        $user = User::where('email', '=', $json->login)->first();
        if ($user === null) {
            // user doesn't exist then create it
            $user = $this->userRepository->create($data);
            // Send the User Confirmation Email.
            //$user->notify(new UserNeedsConfirmation($user->confirmation_code)); //it seems to be automatically sending an email on user creation
            //Attach any Affiliate that is present.
            if (isset($json->affiliateRef)) {
                $user->affliate_ref = $json->affiliateRef;
            }
            $user->save();
            $code ="success";
            $data= $user->uuid;





            \Log::info("User Registered  ".$json->login . " ID: " . $user->id);

        }else {

            \Log::info("Tried to register user that already exists".$json->login);
            $code ="vpn.user-service.duplicate_name"; // Android App is coded to look for this string
            $data= "";
        }







        $result=array(
            "data" => $data,
            "code" => $code

        );
        return response()->json($result, 200);
    }


    public function register_user_confirmed(Request $request)
    {

        // Getting the Request to Decode According to Casper VPN App requirements
        $json = $request->getContent();
        $json = json_decode($json);

        $data=array(
            "email" => $json->login,
            "password" => $json->password,
            "first_name" => "",
            "last_name"  => "",
            "roles" => array("subscriber"),
            "confirmed" => 1
        );

        $user = $this->userRepository->create($data);

        $user->affliate_ref = $json->affiliateRef;

        $user->save();


        $result=array(
            "data" => $user->uuid,
            "code" => "success"

        );

        //todo add first registration subscription.

        return response()->json($result, 200);
    }

    public function resend_confirm_email($email,Request $request)
    {

        // Sending information to log files

        \Log::info("Sending Confirmation Email to " .  $email );
        \Log::debug("Sending Confirmation Email to " .  $email, array ($request) );
        // Declaring the Response varibales;
        $code="";
        $data="";
       $user = $this->userRepository->getByColumn($email, 'email');
        if ($user !=null){
            $user->notify(new UserNeedsConfirmation($user->confirmation_code));
            $data="success";
            $code="success";
        }else{
            $data="fail";
            $code="fail";
        }
        $result=array(
            "data" => $data,
            "code" => $code
        );
        return response()->json($result, 200);
    }

    public function confirm_email(Request $request)
    {
        $json = $request->getContent();
        $json = json_decode($json);

        $user = $this->userRepository->confirm($json->check);

    }


    function reset_password ($email, Request $request){
        //todo this function is not properly sending email  please check.

        \Log::info("Sending Reset Password Email to " .  $email );
        \Log::debug("Sending Reset Password Email to " .  $email, array ($request) );
        // Declaring the Response varibales;
        $code="";
        $data="";
        $user = $this->userRepository->getByColumn($email, 'email');
        if ($user !=null){

            $response = $this->broker()->sendResetLink(
                array("email"=> $email)
            );
            $response == Password::RESET_LINK_SENT
                ? $this->sendResetLinkResponse($request, $response)
                : $this->sendResetLinkFailedResponse($request, $response);

            \Log::debug( $response );


            $data="success";
            $code="success";
        }else{
            $data="fail";
            $code="fail";
        }
        $result=array(
            "data" => $data,
            "code" => $code
        );
        return response()->json($result, 200);

    }




}
