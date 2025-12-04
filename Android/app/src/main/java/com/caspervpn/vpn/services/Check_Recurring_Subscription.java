package com.caspervpn.vpn.services;

import android.app.Service;
import android.content.Intent;
import android.os.IBinder;
import androidx.annotation.Nullable;

import com.caspervpn.vpn.R;
import com.caspervpn.vpn.classes.Payment;
import com.caspervpn.vpn.common.Commun;
import com.caspervpn.vpn.common.DataConnection;
import com.caspervpn.vpn.helper.MyApplication;
import com.caspervpn.vpn.screens.Landing_Page;
import com.caspervpn.vpn.util.IabHelper;
import com.caspervpn.vpn.util.IabResult;
import com.caspervpn.vpn.util.Inventory;
import com.caspervpn.vpn.util.Purchase;
import com.google.gson.Gson;

import org.json.JSONObject;

import java.util.Map;

import static com.caspervpn.vpn.common.Configuration.LICENSE;
import static com.caspervpn.vpn.common.Configuration.PAYMENT_CLASS_ID;
import static com.caspervpn.vpn.common.Configuration.Recurring1MAction;
import static com.caspervpn.vpn.common.Configuration.Recurring1YAction;
import static com.caspervpn.vpn.common.Configuration.Recurring6MAction;
import static com.caspervpn.vpn.common.Configuration.SKU_ONE_MONTH;
import static com.caspervpn.vpn.common.Configuration.SKU_ONE_YEAR;
import static com.caspervpn.vpn.common.Configuration.SKU_SIX_MONTHS;
import static com.caspervpn.vpn.common.Configuration.SUBSCRIPTION_CLASS_ID;
import static com.caspervpn.vpn.common.Configuration.SubscriptionsCategory;
import static com.caspervpn.vpn.common.Configuration.payment;
import static com.caspervpn.vpn.common.Configuration.userprofile;

public class Check_Recurring_Subscription extends Service {

    IabHelper mHelper;
    Commun commun;
    Service MyActivity;
    boolean IsOneMonthSubscribed = false;
    boolean IsSixMonthsSubscribed = false;
    boolean IsOneYearSubscribed = false;
    boolean IsAutoRenewEnabled = false;
    boolean IsSubscribed = false;
    private DataConnection conn;
    private Purchase OneMonth, SixMonths, OneYear;

    @Override
    public void onCreate() {

        Init();
        if(userprofile != null)
            if (!userprofile.getSubscription().getSubscriptionId().equals("12c3fc2a-4915-43c3-a992-388b38aa02e3")) { //not lifetime
                SetUpBilling();
                commun.Log("### Check_Recurring_Subscription Opened - Init - SetUpBilling ###");
            }
    }

    private void Init(){

        MyActivity = this;
        commun = new Commun(MyActivity);
        conn = new DataConnection(this);

    }

    private void SetUpBilling()
    {
        try
        {
            mHelper = new IabHelper(this, LICENSE);

            mHelper.enableDebugLogging(true);

            mHelper.startSetup(new IabHelper.OnIabSetupFinishedListener()
            {
                public void onIabSetupFinished(IabResult result)
                {
                    // Have we been disposed of in the meantime? If so, quit.
                    if (mHelper == null)
                    {
                        ShowConnectionDialogLoading("mHelper == null");
                        return;
                    }

                    if (!result.isSuccess())
                    {
                        commun.Log("Problem setting up in-app billing: " + result);
                        ShowConnectionDialogLoading("!result.isSuccess()");
                        return;
                    }

                    // IAB is fully set up. Now, let's get an inventory of stuff we own.
                    try
                    {
                        mHelper.queryInventoryAsync(mGotInventoryListener);
                    }
                    catch (IabHelper.IabAsyncInProgressException e)
                    {
                        commun.Log("Error querying inventory. Another async operation in progress. : " + e.toString());
                    }
                }
            });
        }
        catch (Exception ex){
            ex.printStackTrace();
            commun.Log(ex.toString());
        }
    }

    IabHelper.QueryInventoryFinishedListener mGotInventoryListener = new IabHelper.QueryInventoryFinishedListener()
    {
        public void onQueryInventoryFinished(IabResult result, Inventory inventory)
        {
            // Have we been disposed of in the meantime? If so, quit.
            if (mHelper == null)
            {
                ShowConnectionDialogLoading("mHelper = null");
                return;
            }

            if (result.isFailure())
            {
                commun.Log("Failed to query inventory: " + result);
                return;
            }

            // First find out which subscription is auto renewing
            Purchase oneMonth = inventory.getPurchase(SKU_ONE_MONTH);
            Purchase sixMonths = inventory.getPurchase(SKU_SIX_MONTHS);
            Purchase oneYear = inventory.getPurchase(SKU_ONE_YEAR);

            IsOneMonthSubscribed = oneMonth != null;
            IsSixMonthsSubscribed = sixMonths != null;
            IsOneYearSubscribed = oneYear != null;

            IsSubscribed = ((oneMonth != null && verifyDeveloperPayload(SKU_ONE_MONTH, oneMonth))
                    || (sixMonths != null && verifyDeveloperPayload(SKU_SIX_MONTHS, sixMonths))
                    || (oneYear != null && verifyDeveloperPayload(SKU_ONE_YEAR, oneYear)));

            IsAutoRenewEnabled = ((oneMonth != null && oneMonth.isAutoRenewing())
                    || (sixMonths != null && sixMonths.isAutoRenewing())
                    || (oneYear != null && oneYear.isAutoRenewing()));

            commun.SaveClassToPreference(oneMonth, "oneM");
            commun.SaveClassToPreference(sixMonths, "sixM");
            commun.SaveClassToPreference(oneYear, "oneY");

            LoadApp();
        }
    };

    private void LoadApp(){
        try {
            commun.Log("LOADAPP");
            Purchase oneMonth = new Gson().fromJson(commun.LoadClassFromPreference("oneM"), Purchase.class);
            Purchase sixMonths = new Gson().fromJson(commun.LoadClassFromPreference("sixM"), Purchase.class);
            Purchase oneYear = new Gson().fromJson(commun.LoadClassFromPreference("oneY"), Purchase.class);

            // Intercom attributes
            // if user subscribed = false + canceled = true => he didn't subscribe
            // if user subscribed = true + canceled = false => he subscribed and didn't cancel the subscription yet OR he canceled and he re-subscribed
            // if user subscribed = true + canceled = true => he subscribed and then he canceled
            if (IsAutoRenewEnabled)
                commun.updateIntercomAttributeBoolean("Canceled Subscription", false);
            else commun.updateIntercomAttributeBoolean("Canceled Subscription", true);

            //check if subscription need to be renew

            if (userprofile != null) {
                long subscriptionEndDate = userprofile.getSubscription().getSubscriptionEndDate();
                commun.Log("LOADAPP subscriptionEndDate : " + subscriptionEndDate);
                if (!commun.IsActive()) {
                    commun.Log("IsOneMonthSubscribed: " + IsSubscribed);
                    commun.Log("IsSubscribed: " + IsOneMonthSubscribed);
                    commun.Log("IsSixMonthsSubscribed: " + IsSixMonthsSubscribed);
                    commun.Log("IsOneYearSubscribed: " + IsOneYearSubscribed);
                    commun.Log("IsAutoRenewEnabled: " + IsAutoRenewEnabled);

                    if (IsSubscribed && IsOneMonthSubscribed) {
                        OneMonth = oneMonth;
                        GetPayment(1);
                    } else if (IsSubscribed && IsSixMonthsSubscribed) {
                        SixMonths = sixMonths;
                        GetPayment(2);
                    } else if (IsSubscribed && IsOneYearSubscribed) {
                        OneYear = oneYear;
                        GetPayment(3);
                    } else UpdateAppExpiryDate();
                } else UpdateAppExpiryDate();
            }
        }catch (Exception ex){
            ex.printStackTrace();
            commun.Log(ex.toString());
        }
    }

    boolean verifyDeveloperPayload(String itemUniqueId, Purchase purchase)
    {
        try {
            String responsePayload = purchase.getDeveloperPayload();
            String computedPayload = getUserEmailFromAndroidAccounts() + itemUniqueId;

            return responsePayload != null && responsePayload.equals(computedPayload);
        }catch (Exception e) {
            commun.Log(e.getMessage());
            String responsePayload = purchase.getDeveloperPayload();
            return responsePayload != null;
        }
    }

    private String getUserEmailFromAndroidAccounts()
    {
        if(userprofile != null)
            return userprofile.getLogin();
        else
            return MyActivity.getResources().getString(R.string.TimeoutMessage);
    }

    //region ondestory
    @Override
    public void onDestroy()
    {
        super.onDestroy();

        try { // toufic 31-5-18
            //It's important to dispose of the helper here!
            if (mHelper != null) {
                mHelper.disposeWhenFinished();
                mHelper = null;
            }
        }catch (Exception ex){
            commun.Log(ex.toString());
        }
    }

    @Nullable
    @Override
    public IBinder onBind(Intent intent) {
        return null;
    }
    //endregion


    //region Update Expiry date
    private void GetPayment(final int month)
    {
        commun.Log("####################### GETPAY ####################### ");
        Thread thread = new Thread(new Runnable()
        {
            @Override
            public void run()
            {
                try
                {
                    String subscriptionId = userprofile.getSubscription().getSubscriptionId();
                    switch (month){
                        case 1: subscriptionId = "84399a16-ee63-42aa-93e5-a51d36b88960";
                            break;
                        case 2: subscriptionId = "72fd82bd-b0b7-419c-87f1-c2f4d6a4770f";
                            break;
                        case 3: subscriptionId = "ce734e1d-210f-40a8-831d-fdb504918f1b";
                            break;
                    }

                    JSONObject obj = new JSONObject();
                    obj.put("subscriptionId", subscriptionId);

                    conn.GetData(PAYMENT_CLASS_ID, "2", "payment/twocheckout/code/generate", "POST", obj.toString(), true, MyActivity);
                }
                catch (Exception e)
                {
                    commun.Log(e.toString());
                    ShowConnectionDialogLoading("GetPayment " + e.toString());
                    e.printStackTrace();
                }
            }
        });

        thread.start();
    }

    public void OnGetPaymentResult(String result)
    {
        commun.Log("####################### GETPAYRES ####################### ");
        if (result != null)
        {
            try
            {
                JSONObject j = new JSONObject(result);
                JSONObject userdata  = j.getJSONObject("data");

                String code = userdata.getString("code");
                String checkoutUrl = userdata.getString("checkoutUrl");

                Payment payment1 = new Payment(userprofile.getSubscription().getSubscriptionId(), checkoutUrl, code);
                payment = payment1;

                Update_Expiry_Date();
            }
            catch (Exception e) {
                commun.Log(e.getMessage());
                ShowConnectionDialogLoading("OnGetPaymentResult " + e.toString());
            }
        }else
            ShowConnectionDialogLoading("OnGetPaymentResult result = null ");
    }

    private void Update_Expiry_Date()
    {
        commun.Log("####################### UPD ####################### ");
        Thread thread = new Thread(new Runnable()
        {
            @Override
            public void run()
            {
                try
                {
                    String email, merchant_product_id, custom_check_code, pay_method, order_number, currency_code, google_subscription_token;
                    String code_generate_url = payment.getCheckouturl();
                    Map<String, String> Params = commun.readParamsIntoMap(code_generate_url);

                    email  = userprofile.getLogin();
                    merchant_product_id = Params.get("subscriptionId");
                    custom_check_code = Params.get("code");
                    pay_method = "android";
                    currency_code = "LBP";
                    String product_id = "1";
                    String product_description = "1M";
                    order_number = "0000";
                    String payment_sum = "5.9";
                    Long value = Math.round(Double.parseDouble(payment_sum));
                    String recurringAction = Recurring1MAction;
                    String isrecurring = "true";
                    String app_version = MyActivity.getPackageManager().getPackageInfo(getPackageName(), 0).versionName;
                    google_subscription_token = "";

                    if(IsOneMonthSubscribed)
                    {
                        product_id = "1";
                        product_description = "1_Month";
                        order_number = OneMonth.getOrderId();
                        payment_sum = "5.9";
                        value = Math.round(Double.parseDouble(payment_sum));
                        recurringAction = Recurring1MAction;
                        google_subscription_token = OneMonth.getToken();
                    }
                    else if(IsSixMonthsSubscribed)
                    {
                        product_id = "2";
                        product_description = "6_Months";
                        order_number = SixMonths.getOrderId();
                        payment_sum = "29.4";
                        value = Math.round(Double.parseDouble(payment_sum));
                        recurringAction = Recurring6MAction;
                        google_subscription_token = SixMonths.getToken();
                    }
                    else if(IsOneYearSubscribed)
                    {
                        product_id = "3";
                        product_description = "1_Year";
                        order_number = OneYear.getOrderId();
                        payment_sum = "46.8";
                        value = Math.round(Double.parseDouble(payment_sum));
                        recurringAction = Recurring1YAction;
                        google_subscription_token = OneYear.getToken();
                    }

                    //Toufic 3/1/2018 -- google analytics --
                    if(userprofile != null) MyApplication.getInstance().trackEventValue(SubscriptionsCategory, recurringAction, userprofile.getId(), value);
                    //Toufic 3/1/2018

                    String url = "payment/twocheckout/checkoutApps?email=" + email + "&merchant_product_id=" + merchant_product_id + "&custom_check_code=" + custom_check_code + "&pay_method=" + pay_method + "&product_id=" + product_id + "&product_description=" + product_description + "&order_number=" + order_number + "&currency_code=" + currency_code + "&total=" + payment_sum + "&isrecurring=" + isrecurring + "&payment_sum=" + payment_sum + "&app_version=" + app_version  + "&google_subscription_token=" + google_subscription_token;

                    conn.GetData(PAYMENT_CLASS_ID, "3", url , "GET", null, false, MyActivity);
                }
                catch (Exception e) {
                    commun.Log(e.toString());
                    ShowConnectionDialogLoading("Update_Expiry_Date " + e.toString());
                    e.printStackTrace();
                }
            }
        });
        thread.start();
    }

    public void OnUpdateExpiryDateResult(String result)
    {
        if (result != null)
        {
            try
            {
                JSONObject j = new JSONObject(result);
                String userdata  = j.getString("data");
                if (userdata.equals("Payment Success"))
                {
                    commun.Log("####################### RENEW Success ####################### ");
                }
                else  // payment error
                {
                    commun.Log("####################### RENEW Failed ####################### ");
                }

                UpdateAppExpiryDate();

            }
            catch (Exception e) {
                commun.Log(e.getMessage());
                ShowConnectionDialogLoading("OnUpdateExpiryDateResult " + e.toString());
            }
        }else ShowConnectionDialogLoading("OnUpdateExpiryDateResult result = null ");
    }

    private void UpdateAppExpiryDate()  // get users/profile
    {
        commun.Log("####################### UpdateAppExpiryDate ####################### ");
        Thread thread = new Thread(new Runnable()
        {
            @Override
            public void run()
            {
                try
                {
                    conn.GetData(PAYMENT_CLASS_ID, "4", "users/profile", "GET", null, true, MyActivity);
                }
                catch (Exception e)
                {
                    commun.Log(e.toString());
                    ShowConnectionDialogLoading("UpdateAppExpiryDate : " + e.toString());
                    e.printStackTrace();
                }
            }
        });
        thread.start();
    }
    public void OnUpdateAppExpiryDateResult(String result)
    {
        commun.Log("####################### UpdateAppExpiryDateRes ####################### ");
        if (result == null)
        {
            commun.Log(" OnUpdateAppExpiryDateResult is NULL");
        }
        else
        {
            try
            {
                JSONObject j = new JSONObject(result);
                String code = j.getString("code");

                if (code.equals("success"))
                {
                    commun.Log("### Check_Recurring_Subscription success ###");
                    JSONObject userdata  = j.getJSONObject("data");
                    //this will update the object class - edit-profile
                    commun.SaveUserProfile(userdata);

                    Landing_Page.RefreshExpiryDate();

                    stopService();
                }
                else
                {
                    commun.Log(" OnUpdateAppExpiryDateResult: " + result);
                }
            }
            catch (Exception e)
            {
                commun.Log(e.getMessage());
                ShowConnectionDialogLoading("OnUpdateAppExpiryDateResult : " + e.toString());
            }
        }
    }
    //endregion

    //region Start Application
    private void stopService()
    {
        this.stopSelf();
    }
    //endregion

    public void ShowConnectionDialogLoading (String error)
    {
        commun.Log("CHECK RECURRING ERROR : " + error);
    }
}
