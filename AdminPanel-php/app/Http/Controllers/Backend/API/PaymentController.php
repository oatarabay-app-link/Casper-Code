<?php


namespace App\Http\Controllers\Backend\API;


use App\Http\Controllers\Controller;
use App\Models\Traits\Uuid;
use App\Payments_Check;
use App\UserSubscription;
use App\UserSubscriptionExtension;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \App\Subscription;
use \Google;



/**
 * Class PaymentController
 * @package App\Http\Controllers\Backend\API
 */
class PaymentController extends Controller
{

    /*
     * @Route: /payment/twocheckout/code/generate
     * @Requires:  Auth Token
     * @Params: { subscriptionId }
     * @Logic:  Create a Entry In Payment Check Table  as REQUESTED and returns the json.
     * @Returns: JSON Response
     */
    public function generate_payment(Request $request)
    {
        \Log::info("Logginig the incoming request");
        \Log::debug("Generating code for", array($request));
        $json = $request->getContent();
        $json = json_decode($json);
        $susbcription = Subscription::where("uuid", "=", $json->subscriptionId)->first();
        //print_r($susbcription->id);
        $user = $request->user();

        $payment_check = [
            'uuid' => \Ramsey\Uuid\Uuid::uuid4(),//Uuid::generate(4),
            'create_date' => Carbon::now(),
            'subscription_uuid' => $susbcription->uuid,
            'user_uuid' => $user->uuid,
            'user_id' => $user->id,
            'user_email' => $user->email,
            'subscription_id' => $susbcription->id,
            'token' => "",
            'status' => "REQUESTED"
        ];
        $pc = Payments_Check::create($payment_check);
        $result = [
            "data" => [
                "code" => $pc->uuid,
                "checkoutUrl" => "https://caspervpn.com/Order.html?email=" . $user->email . "&code=" . $pc->uuid . "&subscriptionId=" . $susbcription->uuid
            ],
            "code" => "success"
        ];
        \Log::debug("Generated code for", array($result));
        return response(json_encode($result, JSON_PRETTY_PRINT), 200)
            ->header('Content-Type', 'application/json');
    }

    /*
     * @Route: /payment/twocheckout/code/getPayment
     * @Description: In Old code Called GetPaymentInfo / getLastPaymentReceipt / Brings out the data for IOS.
     * Checks if the payment id is not equal for 55555555 which seems to be a hold of android.
     * Calling for route of getPayment  with a get method
     * Get Payment Info
     * @Params Auth token and Request Body
     *
     */
    public function get_payment(Request $request)
    {
        $user = $request->user();
        $last_payment = Payments_Check::where("user_uuid", "=", $user->uuid)
            ->where("status", "=", "COMPLETED") // We are looking for a confirmed Payment.
            ->orderBy("create_date", "desc")
            ->first();

        $payment = $last_payment->payment;

        if ($payment->payment_id != "55555555") {

            //todo  in the old code this loop until it find the payment_method in details to ios we need to implement that here
            $details = json_decode($payment->details);
            if ($details->payment->pay_method != "ios") exit();
            if (isset($details->payment->appleAPI)) {
                $details->payment->appleAPI = json_decode($details->payment->appleAPI);
            }


          //  print_r($details);


            $result = [
                "data" => [
                    "id" => $payment->uuid,
                    "subscriptionId" => $payment->subscription_uuid,
                    "periodInMonths" => $payment->period_in_months,
                    "paymentId" => $payment->payment_id,
                    "date" => $payment->create_date,
                    "status" => $payment->status,
                    "sum" => $payment->payment_sum,
                    "details" => $details,
                    "checkCode" => $payment->check_code,
                ],

                "code" => "success"
            ];

            return response(json_encode($result, JSON_PRETTY_PRINT), 200)
                ->header('Content-Type', 'application/json');

        }


    }


    public function get_last_payment_by_uuid($uuid)
    {

        $last_payment = Payments_Check::where("user_uuid", "=", $uuid)
            ->where("status", "=", "COMPLETED")
            ->orderBy("create_date", "desc")
            ->first();

        $payment = $last_payment->payment;

        if ($payment->payment_id != "55555555") {

            $details = json_decode($payment->details);
            if ($details->payment->pay_method != "ios") exit(); // Need to Fix this function
            if (isset($details->payment->appleAPI)) {
                $details->payment->appleAPI = json_decode($details->payment->appleAPI);
            }


            //  print_r($details);


            $result = [
                "data" => [
                    "id" => $payment->uuid,
                    "subscriptionId" => $payment->subscription_uuid,
                    "periodInMonths" => $payment->period_in_months,
                    "paymentId" => $payment->payment_id,
                    "date" => $payment->create_date,
                    "status" => $payment->status,
                    "sum" => $payment->payment_sum,
                    "details" => $details,
                    "checkCode" => $payment->check_code,
                ],

                "code" => "success"
            ];

            return response(json_encode($result, JSON_PRETTY_PRINT), 200)
                ->header('Content-Type', 'application/json');

        }


    }

    //master function to process checkout and process payment
    /*
     *
     *  Route  for GET  api/payment/twocheckout/checkoutApps?email=shahzadm7slabs@gmail.com&merchant_product_id=84399a16-ee63-42aa-93e5-a51d36b88960&custom_check_code=f27dec23-7687-49db-bbb8-aeaf12694783&pay_method=android&product_id=1&product_description=Watch_Videos&order_number=55555555&currency_code=USD&expiry_date=1571070550&app_version=55.8
     *
     *
     */

    public function checkoutApps(Request $request)
        // Paymentinfo is  request here refereeing from old code
    {
        \Log::info("Logginig the incoming request for Checkout");
        //\Log::debug("Logging Checkout Request code for ", array($request));
        // Check if the check-code exists
        if (isset($request->custom_check_code)) {
            //Logging Data
            \Log::info('Looking for check_code : '. $request->custom_check_code ,["check_code"=>$request->custom_check_code]);
            $payment_check = Payments_Check::where("uuid", "=", $request->custom_check_code)->first();
            if (($payment_check == null) || ($payment_check->status != "REQUESTED")) {
                //echo("Check code not found!. Or Not Expecting");  // Add Exception here with JSON Output
                \Log::info('Cannot Start Checkout with the Check code : '. $request->custom_check_code ,["check_code"=>$request->custom_check_code]);
                return response ("No Payment Request Found",200)
                    ->header('Content-Type', 'application/json');
            }
        }else{

            return response("No Payment Request Found",200)
                ->header('Content-Type', 'application/json');

            \Log::info('Check Code Not found Exiting ');
        }
        // Check if Subscription Exists
        $susbcription = Subscription::where("product_id", "=", $request->product_id)->first();
        if ($susbcription == null) {
            //return("Subscription for payment not found!");  // Add Exception here // Add Exception here with JSON Output

            return response ("Subscription for payment not found! ",200)
                ->header('Content-Type', 'application/json');
        }
        if (isset($request->pay_method)) {
            if (($request->pay_method!="ios") && ($request->pay_method!="android")){
                //return ("PaymentM Error!" . $request->pay_method); //todo  Add Exception here // Add Exception here with JSON Output // Send to Log as well
                return response ("Payment Method Not Recognized ",200)
                    ->header('Content-Type', 'application/json');
            }else{
                \Log::debug("Payment Method Found");
            }
        }
        //TwocheckoutHttpPaymentService.java:456
        // Get Users of the Request
        //todo getting the user from request is not working since the middleware is not availbe in the request.

        $user = $request->user();
        if ($user==null){

            return response ("No User ",200)
                ->header('Content-Type', 'application/json');

        }
        //Get User Subscription
        $user_subscription = UserSubscription::where("user_id", "=", $user->id)
            ->orderby("id","DESC")
            ->first();
        if ($user_subscription) {
            //Logging Data
            \Log::debug('Found User : ' . $user->id, ["id" => $user->id]);
            // Code is looking for Subscription End Date
        }else{
            return response ("No User Susbscription ",200)
                ->header('Content-Type', 'application/json');
        }

        //Check the product which is being purchased
        if (($request->product_description == "Watch_Videos") || ($request->product_description == "Share_Us_Google") || ($request->product_description == "Share_Us_Google")){

            \Log::debug("Product Description for Free Premium : " . $request->product_description );
            \Log::debug("Creating Entries for Free Premium on the User Subscription : " . $request->product_description );
            \Log::debug("Getting Expiry Date : " . $request->expiry_date );
            //todo the expiry date from the request here is coming wrong. because of the length of digits so we handle the 3 hours here from the user expiry date
            $user_end_date = Carbon::parse( $user->getExpiryDate());
            $expiry =$user_end_date; // Just to hold the value to use down later
            \Log::debug("User Expiry Date According to System : " . $user_end_date);
            // Check if the Expiry date is greater than or less than current now
            $current_date = Carbon::now();
            \Log::debug("Current Date According to System : " . $current_date);
            if ($user_end_date < $current_date ) {
                // IF   Current is gtreater than what we have
                $user_end_date = $current_date;
            }
            // Adding 3 Hours to timestamp
            $user_end_date= $user_end_date->add(3,"hour");
            $ue = new \App\UserSubscriptionExtension();
            $ue->expiry_date =$user_end_date;
            $ue->note = "Added Three hours for " . $request->product_description . " by system";
            $ue->added_by = $ue->user_id =$user->id;
            $ue->subscription_id = $user_subscription->id;
            $ue->days = 0;//$expiry->diffInDays($user_end_date);
            $ue->user_id =$user->id;
            $ue->save();
            \Log::debug("Added Three hours to the user");
            //todo Set the Payment Check to COMPLETED
            $payment_check->status = "COMPLETED";
            $payment_check->save();
            \Log::debug("Updated Payment Check");

            $result = [
                "data" => "Payment Success",
                "code" => "success"
            ];
            \Log::debug("Payment Successfull for Free premium,");
            return response(json_encode($result, JSON_PRETTY_PRINT), 200)
                ->header('Content-Type', 'application/json');
            //
        }else{

            // Processing for the Packages

            \Log::debug("Product Description : " . $request->product_description );
            \Log::debug("Getting Expiry Date From Request : " . $request->expiry_date );
            //todo the expiry date from the request here is coming wrong. because of the length of digits so we handle the 3 hours here from the user expiry date
            $user_end_date = Carbon::parse( $user->getExpiryDate());
            $expiry =$user_end_date; // Just to hold the value to use down later
            \Log::debug("User Expiry Date According to System : " . $user_end_date);
            // Check if the Expiry date is greater than or less than current now
            $current_date = Carbon::now();
            \Log::debug("Current Date According to System : " . $current_date);
            if ($user_end_date < $current_date ) {
                // If   Current date is greater than what we have.
                $user_end_date = $current_date;
            }
            // Check the payment method and verify the receipt.
            if ($request->pay_method!="android"){
                // Start Getting the details
                // For Android Payment method
                /*
                 * Details Required
                 * 1. Email -- Already Present
                 * 2. merchanct_product_id -- Already Present as subscription
                 * 3. custom_check_code  -- Already Present
                 * 4. pay_method -- Already Present thats why we are here
                 * 5. product_id  -- Already Present not required in this context just a duplicate it is
                 * 6. product_description -- Already Present as
                 * 7. order_number  -- Requied to be stored in Database
                 * 8. currency_code --  Payment Currency - I am not sure about this bbut i think it is the currency that the product was charged in
                 * 9. total - I am unsure of this total as well as it donot make sense in LBP
                 * 10. isrecurrening -- Is the subscription is recurring or not.
                 * 11. payment_sum -- total payment  5.9  i am also unsure about this
                 * 12. app_version -- app version
                 * 13. google_subscription_token -- most required thing
                 *
                 *
                 * Steps to perfom
                 *
                 *
                 *  Check Google Token and Save the Expiry Date and Response from Google
                 *  Update Subscription Expiry Date.
                 *  Write a Proper Log info
                 *  Save the information in the payment info
                 *  Update the User Expiry Date
                 *  Set Custom Check code to  COMPLETED
                 *  Return response.
                 */


                // Step Check Google Receipt / Token and get the infromation


                $g_info = $this->google_verify_purchase($request->merchanct_product_id,$request->google_subscription_token);
                if ($g_info){

                    // Payment state  1 equal to Payment Received So we have to confirm that the payment is received
                     if ($g_info->paymentState==1){
                         // Check subscription start date and end date from Google.
                         $g_start_date= $g_info->startTimeMillis;
                         $g_end_date= $g_info->expiryTimeMillis;


                     }







                }else{

                    \Log::debug("Problem with Google Confirmation");
                    return response(json_encode("G Problem", JSON_PRETTY_PRINT), 200)
                        ->header('Content-Type', 'application/json');

                }



                //Step 5
                $payment_check->status = "COMPLETED";
                $payment_check->save();




//                if ($request->product_description == "1_Month"){
//
//
//
//
//                }
            }






        }

        return ("Success");


        //todo Get Subscription End Date I think that can be brought through the $user model
//        $isValid = false;   //varibale pres3ent in old code
//        $flagsubscription=false;  //Boolean varibale present in old code
//        $ValidateApple = True; // This is a configuration varibale to use  we will move it to configuration
//        $ValidateGoogle =True; // This is a configuration varibale to use  we will move it to configuration
//
//        // Processing if the payment method was IOS
//
//        if (($request->pay_method=="ios") && ( $ValidateApple == True)){
//
//            // IOS PROCESSING CODE HERE TO CHECK
//            // Check if the $reuqest / Payment Info has the isrecurring true
//            // Since  this function is a getrequest what we do we will fill up the request data with payment information of the last payment request.
//            // check if the $request has is recurrening = true
//            $payment_receipt =""; //Setting an empty varibale to bring in global context to function
//            if (isset($request->isrecurring)){ //Checking if we have the recurring parameter
//                if ($request->isrecurring=="true"){
//                    // Load the Last Completed Payment from the User. and get the receipt data.
//                    \Log::info('User : '.$user->id . " Recurring Set to True");
//                    $payment_receipt=get_last_payment_by_uuid($user->uuid);
//                }else{
//
//                    \Log::info('User : '.$user->id . " Recurring Set to False");
//                }
//            }else{
//                \Log::info('User : '.$user->id . " Recurring Not Set");
//            }
//            if(isset($request->receipt_data)){
//                //Check if we have the receipt data present in parameters of request
//                \Log::info('User : '.$user->id . " Receipt Data is Set, IOS");
//            }else{
//                \Log::info('User : '.$user->id . " Receipt Data not Set, IOS");
//            }
//
//            $flagsubscription = true;
//            $isValid =""; //CallAppleCheck(userEndDate, paymentInfo); //todo check the payment via calling apple.
//
//        } elseif(($request->pay_method=="android") && ( $ValidateGoogle == True)
//              && ($request->product_description!="Watch_Videos")
//              && ($request->product_description !="Fill_Survey")) {
//
//            // Please check if we have a valid google token that is not expired.
//            $GoogleTokenExpires = ""; // Temporary Variable
//            // I have replaced the Oauth Token with the Service account API here
//            // We will be using the Google Client for All operations
//
//
//
//        }

    }


    /*
     * Takes the subscription id and token and get the data from the google
     * in an attempt to verify the purchase.
     *  Return the Receipt or return null
     *
     *      */
    public function google_verify_purchase($subid,$token){
        // Convert the UUID for subsid to extact google subs name
        switch ($subid){
            case "84399a16-ee63-42aa-93e5-a51d36b88960":
                $subid = "caspervpn.1month.subscription";
                break;
            case "72fd82bd-b0b7-419c-87f1-c2f4d6a4770f":
                $subid = "caspervpn.6month.subscription";
                break;
            case "ce734e1d-210f-40a8-831d-fdb504918f1b":
                $subid = "caspervpn.1year.subscription";
                break;
            case "12c3fc2a-4915-43c3-a992-388b38aa02e3":
                $subid = "caspervpn.lifetime.subscription"; //todo correct the strings from google play console.
                break;
            default:
                break;
        }
        $client= new \Google_Client();
        $service_account_config = ("D:\\Work\\CasperVPN\\AdminPanel\\admin_panel\\api-5954894522699629267-424023-8598f41da41b.json");  //todo replace with the URL
        $client->setAuthConfig($service_account_config);
        $client->setApplicationName("Google Play Android Developer");
        $client->addScope("https://www.googleapis.com/auth/androidpublisher");
        $service = new \Google_Service_AndroidPublisher($client);
        try{
            $purchase = $service->purchases_subscriptions->get("caspervpn.com",$subid,$token);
            return ($purchase);
        }catch (\Exception $ex){
            // Token not found or and Error Occured
            return null;
        }
    }


    /*
     * Private function to get user end date here we check
     * if there is any extension running so we add the days
     * from the payment on the top of that.
     *
     */
    private function get_user_end_date($user_id){



    }

}