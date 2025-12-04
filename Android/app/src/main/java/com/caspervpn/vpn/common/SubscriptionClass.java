package com.caspervpn.vpn.common;

import android.app.Activity;
import android.app.AlertDialog;
import android.view.View;
import android.widget.RelativeLayout;

import com.caspervpn.vpn.R;
import com.caspervpn.vpn.classes.Payment;
import com.caspervpn.vpn.helper.MyTextView;

import org.json.JSONObject;

import java.util.Map;

import static com.caspervpn.vpn.common.Configuration.ONE_HOURS;
import static com.caspervpn.vpn.common.Configuration.SUBSCRIPTION_CLASS;
import static com.caspervpn.vpn.common.Configuration.WATCH_VIDEO_HOURS;
import static com.caspervpn.vpn.common.Configuration.paymentClass;
import static com.caspervpn.vpn.common.Configuration.userprofile;


/**
 * Created by toufics on 4/12/2018.
 */

public class SubscriptionClass {
    private MyTextView Loading_Text;
    private Commun commun;
    private DataConnection conn;
    private Activity MyActivity;
    private RelativeLayout Loading;
    private String type;
    private String appversion;

    public SubscriptionClass(MyTextView loading_Text, Commun commun, Activity MyActivity, RelativeLayout Loading, String type, String app_version){
        Loading_Text = loading_Text;
        this.commun = commun;
        conn = new DataConnection(this);
        this.MyActivity = MyActivity;
        this.Loading = Loading;
        this.type = type;
        appversion = app_version;
    }

    //region Update Expiry date
    public void getPayment(final String subscriptionId)
    {
        setWaitScreen(true);
        Thread thread = new Thread(new Runnable()
        {
            @Override
            public void run()
            {
                try
                {
                    JSONObject obj = new JSONObject();
                    obj.put("subscriptionId", subscriptionId);

                    conn.GetData(SUBSCRIPTION_CLASS, "1", "payment/twocheckout/code/generate", "POST", obj.toString(), true, MyActivity);
                }
                catch (Exception e)
                {
                    commun.Log(e.toString());
                    ShowErrorDialog();
                }
            }
        });

        thread.start();
    }

    public void OnGetPaymentResult(String result)
    {
        if (result != null)
        {
            try
            {
                JSONObject j = new JSONObject(result);
                JSONObject userdata  = j.getJSONObject("data");

                String code = userdata.getString("code");
                String checkoutUrl = userdata.getString("checkoutUrl");

                Payment payment1 = new Payment(userprofile.getSubscription().getSubscriptionId(), checkoutUrl, code);
                paymentClass = payment1;

                Update_Expiry_Date();
            }
            catch (Exception e) {
                commun.Log(e.toString());
                ShowErrorDialog();
            }
        }else {
            ShowErrorDialog();
        }
    }

    private void Update_Expiry_Date()
    {
        Thread thread = new Thread(new Runnable()
        {
            @Override
            public void run()
            {
                try
                {
                    String email, merchant_product_id, custom_check_code, pay_method, order_number, currency_code;
                    Long expiry_date, one_week;

                    // one_day = 86400000 millisecond
                    one_week =  Long.valueOf(WATCH_VIDEO_HOURS * ONE_HOURS);
                    expiry_date = userprofile.getSubscription().getSubscriptionEndDate() + one_week;

                    String code_generate_url = paymentClass.getCheckouturl();
                    Map<String, String> Params = commun.readParamsIntoMap(code_generate_url);

                    email  = userprofile.getLogin();
                    merchant_product_id = Params.get("subscriptionId");
                    custom_check_code = Params.get("code");
                    pay_method = "android";
                    order_number = "55555555";
                    currency_code = "USD";
                    String product_id = "1";
                    String product_description = type;
                    String app_version = appversion;

                    commun.updateIntercomAttributeBoolean("Free Premium", true);

                    String url = "payment/twocheckout/checkoutApps?email=" + email + "&merchant_product_id=" + merchant_product_id + "&custom_check_code=" + custom_check_code + "&pay_method=" + pay_method + "&product_id=" + product_id + "&product_description=" + product_description + "&order_number=" + order_number + "&currency_code=" + currency_code + "&expiry_date=" + expiry_date + "&app_version=" + app_version;

                    conn.GetData(SUBSCRIPTION_CLASS, "3", url , "GET", null, false, MyActivity);

                }
                catch (Exception e) {
                    commun.Log(e.toString());
                    ShowErrorDialog();
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
                    UpdateAppExpiryDate();
                }
                else  // payment error
                {
                    commun.Log("####################### Free Bee center Failed ####################### ");
                    ShowErrorDialog();
                }
            }
            catch (Exception e) {
                commun.Log(e.getMessage());
                ShowErrorDialog();
            }
        }else {
            ShowErrorDialog();
        }
    }

    private void UpdateAppExpiryDate()  // get users/profile
    {
        Thread thread = new Thread(new Runnable()
        {
            @Override
            public void run()
            {
                try
                {
                    if (!commun.isNetworkConnected())
                    {
                        return;
                    }

                    conn.GetData(SUBSCRIPTION_CLASS, "4", "users/profile", "GET", null, true, MyActivity);
                }
                catch (Exception e)
                {
                    ShowErrorDialog();
                    commun.Log(e.toString());
                }
            }
        });
        thread.start();
    }
    public void OnUpdateAppExpiryDateResult(String result)
    {
        if (result == null)
        {
            commun.Log("On User Profile Result is NULL");
            ShowErrorDialog();
        }
        else
        {
            try
            {
                JSONObject j = new JSONObject(result);
                String code = j.getString("code");

                if (code.equals("success"))
                {
                    JSONObject userdata  = j.getJSONObject("data");
                    //this will update the object class - edit-profile
                    commun.SaveUserProfile(userdata);
                    //update buttons after expiry date update
                    ShowSuccessBeeCenter();
                }
                else
                {
                    commun.Log("On User Profile Result: " + result);
                    ShowErrorDialog();
                }
            }
            catch (Exception e)
            {
                commun.Log(e.toString());
                ShowErrorDialog();
            }
        }
    }

    void setWaitScreen(boolean set)
    {
        Loading.setVisibility(set ? View.VISIBLE : View.GONE);
        Loading_Text.setVisibility(set ? View.VISIBLE : View.GONE);
    }


    private void ShowErrorDialog()
    {
        try {
            setWaitScreen(false);

            AlertDialog.Builder builder = new AlertDialog.Builder(MyActivity);
            AlertDialog alertDialog = null;

            builder.setIcon(R.mipmap.alert_green);
            builder.setTitle((MyActivity.getResources().getString(R.string.NOTE)));
            builder.setMessage((MyActivity.getResources().getString(R.string.Error)));
            builder.setPositiveButton((MyActivity.getResources().getString(R.string.OK)), null);
            alertDialog = builder.create();
            alertDialog.show();
        }catch (Exception ex){
            commun.Log(ex.toString());
        }
    }

    private void ShowSuccessBeeCenter(){
        try {
            setWaitScreen(false);

            AlertDialog.Builder builder = new AlertDialog.Builder(MyActivity);
            AlertDialog alertDialog = null;

            builder.setIcon(R.mipmap.alert_green);
            builder.setTitle((MyActivity.getResources().getString(R.string.allset)));
            builder.setMessage((MyActivity.getResources().getString(R.string.FreeBeeCenterActivated)));
            builder.setPositiveButton((MyActivity.getResources().getString(R.string.OK)), null);
            alertDialog = builder.create();
            alertDialog.show();
        }catch (Exception ex){
            commun.Log(ex.toString());
        }
    }

    //endregion
}
