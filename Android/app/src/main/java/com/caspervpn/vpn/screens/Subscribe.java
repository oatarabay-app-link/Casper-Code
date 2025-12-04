package com.caspervpn.vpn.screens;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Typeface;
import android.net.Uri;
import android.os.Bundle;
import android.view.View;
import android.widget.ImageButton;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.caspervpn.vpn.R;
import com.caspervpn.vpn.classes.Payment;
import com.caspervpn.vpn.classes.SubscriptionData;
import com.caspervpn.vpn.classes.SusbcriptionDataList;
import com.caspervpn.vpn.common.Commun;
import com.caspervpn.vpn.common.DataConnection;
import com.caspervpn.vpn.helper.MyApplication;
import com.caspervpn.vpn.helper.MyButton;
import com.caspervpn.vpn.helper.MyTextView;
import com.caspervpn.vpn.util.IabBroadcastReceiver.IabBroadcastListener;
import com.caspervpn.vpn.util.IabHelper;
import com.caspervpn.vpn.util.IabHelper.IabAsyncInProgressException;
import com.caspervpn.vpn.util.IabResult;
import com.caspervpn.vpn.util.Inventory;
import com.caspervpn.vpn.util.Purchase;
import com.google.gson.Gson;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;

import io.intercom.android.sdk.Intercom;

import static com.caspervpn.vpn.common.Configuration.LICENSE;
import static com.caspervpn.vpn.common.Configuration.PAYMENT_CLASS_ID;
import static com.caspervpn.vpn.common.Configuration.SKU_LIFETIME;
import static com.caspervpn.vpn.common.Configuration.SKU_ONE_MONTH;
import static com.caspervpn.vpn.common.Configuration.SKU_ONE_YEAR;
import static com.caspervpn.vpn.common.Configuration.SKU_SIX_MONTHS;
import static com.caspervpn.vpn.common.Configuration.SUBSCRIPTION_CLASS_ID;
import static com.caspervpn.vpn.common.Configuration.SubscribeScreenName;
import static com.caspervpn.vpn.common.Configuration.Subscribed1MAction;
import static com.caspervpn.vpn.common.Configuration.Subscribed1YAction;
import static com.caspervpn.vpn.common.Configuration.Subscribed6MAction;
import static com.caspervpn.vpn.common.Configuration.SubscribedLifetimeAction;
import static com.caspervpn.vpn.common.Configuration.SubscriptionsCategory;
import static com.caspervpn.vpn.common.Configuration.UnSubscribedAction;
import static com.caspervpn.vpn.common.Configuration.payment;
import static com.caspervpn.vpn.common.Configuration.userprofile;

public class Subscribe extends Activity implements View.OnClickListener, IabBroadcastListener
{
    Activity MyActivity;
    Commun commun;

    MyButton OneMonthBtn, SixMonthsBtn, TwelveMonthsBtn, LifeimteSubscribeBtn;
    LinearLayout OneMonth, SixMonths, TwelveMonths, LiftimeLL;
    ImageButton Back_Btn;
    TextView ApplicationTitle;
    RelativeLayout Loading;
    MyTextView OneMonthPrice, SixMonthPrice, OneYearPrice, LifetimePrice, ua1, ua2, ua3, ua4;
    MyTextView OneMtxt, SixMtxt, twMtxt, OneMDevicestxt, lifetimeDevicestxt, Lifetimetxt, Lifetimetxtstrike1;
    MyTextView SixMDevicestxt, twMDevicestxt, Loading_Text, unsubscribe_updowngrade;
    MyTextView howsubscriptionworks;

    Long value;

    int SelectedSubscriptionType;

    static final int RC_REQUEST = 10101;

    boolean IsOneMonthSubscribed = false;
    boolean IsSixMonthsSubscribed = false;
    boolean IsOneYearSubscribed = false;
    boolean IsLifetimeSubscribed = false;

    boolean IsAutoRenewEnabled = false;
    boolean IsSubscribed = false;

    String onemp, sixmp, oneyp, lifetimep;

    // The helper object
    IabHelper mHelper;


    private DataConnection conn;

    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.subscribe);
        MyActivity = this;
        commun = new Commun(MyActivity);
        conn = new DataConnection(this);

        Init();
        GetSubscriptions();
        SetUpBilling();
    }

    private void Init()
    {
        ApplicationTitle = (TextView) findViewById(R.id.ApplicationTitle);
        ApplicationTitle.setText(getString(R.string.SUBSCRIBE));

        Loading = (RelativeLayout) findViewById(R.id.loading);
        Loading_Text = (MyTextView) findViewById(R.id.Loading_Text);

        OneMonthBtn = (MyButton) findViewById(R.id.OneMonthSubscribeBtn);
        SixMonthsBtn = (MyButton) findViewById(R.id.SixMonthsSubscribeBtn);
        TwelveMonthsBtn = (MyButton) findViewById(R.id.OneYearSubscribeBtn);
        LifeimteSubscribeBtn = (MyButton) findViewById(R.id.LifeimteSubscribeBtn);

        OneMonth = (LinearLayout) findViewById(R.id.OneMonth);
        SixMonths = (LinearLayout) findViewById(R.id.SixMonths);
        TwelveMonths = (LinearLayout) findViewById(R.id.TwelveMonths);
        LiftimeLL = (LinearLayout) findViewById(R.id.LiftimeLL);
        Back_Btn = (ImageButton) findViewById(R.id.back);

        OneMonthPrice =(MyTextView)findViewById(R.id.OneMonthPrice);
        SixMonthPrice =(MyTextView) findViewById(R.id.SixMonthPrice);
        OneYearPrice = (MyTextView) findViewById(R.id.OneYearPrice);
        LifetimePrice = (MyTextView) findViewById(R.id.LifetimePrice);
        howsubscriptionworks = (MyTextView) findViewById(R.id.howsubscriptionworks);
        unsubscribe_updowngrade = (MyTextView) findViewById(R.id.unsubscribe_updowngrade);
        OneMtxt = (MyTextView) findViewById(R.id.OneMtxt);
        SixMtxt = (MyTextView) findViewById(R.id.SixMtxt);
        twMtxt = (MyTextView) findViewById(R.id.twMtxt);
        Lifetimetxt = (MyTextView) findViewById(R.id.Lifetimetxt);
        OneMDevicestxt = (MyTextView) findViewById(R.id.OneMDevicestxt);
        lifetimeDevicestxt = (MyTextView) findViewById(R.id.lifetimeDevicestxt);
        SixMDevicestxt = (MyTextView) findViewById(R.id.SixMDevicestxt);
        twMDevicestxt = (MyTextView) findViewById(R.id.twMDevicestxt);
        Lifetimetxtstrike1 = (MyTextView) findViewById(R.id.Lifetimetxtstrike1);

        ua1 =(MyTextView)findViewById(R.id.ua1);
        ua2 =(MyTextView) findViewById(R.id.ua2);
        ua3 = (MyTextView) findViewById(R.id.ua3);
        ua4 = (MyTextView) findViewById(R.id.ua4);

        OneMonthPrice.setVisibility(View.INVISIBLE);
        SixMonthPrice.setVisibility(View.INVISIBLE);
        OneYearPrice.setVisibility(View.INVISIBLE);
        LifetimePrice.setVisibility(View.INVISIBLE);
        OneMtxt.setTypeface(null, Typeface.BOLD);
        SixMtxt.setTypeface(null, Typeface.BOLD);
        twMtxt.setTypeface(null, Typeface.BOLD);
        Lifetimetxt.setTypeface(null, Typeface.BOLD);
        OneMDevicestxt.setTypeface(null, Typeface.NORMAL);
        SixMDevicestxt.setTypeface(null, Typeface.NORMAL);
        twMDevicestxt.setTypeface(null, Typeface.NORMAL);
        lifetimeDevicestxt.setTypeface(null, Typeface.NORMAL);
        Lifetimetxtstrike1.setTypeface(null, Typeface.NORMAL);

        ua1.setTypeface(null, Typeface.NORMAL);
        ua2.setTypeface(null, Typeface.NORMAL);
        ua3.setTypeface(null, Typeface.NORMAL);
        ua4.setTypeface(null, Typeface.NORMAL);

        OneMonth.setOnClickListener(this);
        SixMonths.setOnClickListener(this);
        TwelveMonths.setOnClickListener(this);
        LiftimeLL.setOnClickListener(this);
        Back_Btn.setOnClickListener(this);
        howsubscriptionworks.setOnClickListener(this);
        unsubscribe_updowngrade.setOnClickListener(this);

        //Toufic 3/1/2018 -- google analytics --
        MyApplication.getInstance().trackScreenView(SubscribeScreenName);
        //Toufic 3/1/2018

        if(userprofile == null)
            showReloginMessage();
    }

    //region get_subscripiton info
    public void GetSubscriptions()
    {
        setWaitScreen(true);
        Loading_Text.setText(R.string.Loading);

        Thread thread = new Thread(new Runnable()
        {
            @Override
            public void run()
            {
                try
                {
                    conn.GetData(SUBSCRIPTION_CLASS_ID, "1", "subscriptions", "GET", null, false, MyActivity);
                }
                catch (Exception e) {
                    commun.Log(e.toString());
                    runOnUiThread(new Runnable()
                    {
                        @Override
                        public void run()
                        {
                            ShowRefreshDialog();
                        }
                    });
                    e.printStackTrace();
                }
            }
        });
        thread.start();
    }

    public void OnSubscriptionsResult(String result)
    {
        if (result != null)
        {
            try
            {
                JSONObject j = new JSONObject(result);
                JSONArray userdata  = j.getJSONArray("data");
                commun.SaveSubscriptions(userdata);

                Update_Subscription();
            }
            catch (Exception e) {
                commun.Log(e.getMessage());
                ShowRefreshDialog();
            }
        }
        else ShowRefreshDialog();
    }
    //endregion

    private void Update_Subscription()
    {
        SusbcriptionDataList listsubscriptiondata = new Gson().fromJson(commun.LoadClassFromPreference("SubscriptionDataList"), SusbcriptionDataList.class);

        if(listsubscriptiondata !=null)
        {
            ArrayList<SubscriptionData> SubscriptionDataList = listsubscriptiondata.getSubscriptionDataList();
            for (SubscriptionData subscriptionData : SubscriptionDataList) {
                if (subscriptionData.getSubscriptionName().equals("1 Month")) {
                    OneMonthPrice.setText(Double.toString(subscriptionData.getperiodPrice()) + "$ / " + getString(R.string.Month));
                    OneMtxt.setText(getString(R.string.OneMonth) + " : " + Double.toString(subscriptionData.getMonthlyPrice()) + "$ / " + getString(R.string.Month));
                    OneMDevicestxt.setText(Integer.toString(subscriptionData.getMaxConnections()) + " " + getString(R.string.Devices));
                    onemp = Double.toString(subscriptionData.getperiodPrice());
                }
                if (subscriptionData.getSubscriptionName().equals("6 Months")) {
                    SixMonthPrice.setText(Double.toString(subscriptionData.getperiodPrice()) + "$ / " + getString(R.string.SixMonth));
                    SixMtxt.setText(getString(R.string.SixMonth) + " : " + Double.toString(subscriptionData.getMonthlyPrice()) + "$ / " + getString(R.string.Month));
                    SixMDevicestxt.setText(Integer.toString(subscriptionData.getMaxConnections()) + " " + getString(R.string.Devices));
                    sixmp = Double.toString(subscriptionData.getperiodPrice());
                }
                if (subscriptionData.getSubscriptionName().equals("1 Year")) {
                    OneYearPrice.setText(Double.toString(subscriptionData.getperiodPrice()) + "$ / " + getString(R.string.OneYear));
                    twMtxt.setText(getString(R.string.OneYear) + " : " + Double.toString(subscriptionData.getMonthlyPrice()) + "$ / " + getString(R.string.Month));
                    twMDevicestxt.setText(Integer.toString(subscriptionData.getMaxConnections()) + " " + getString(R.string.Devices));
                    oneyp = Double.toString(subscriptionData.getperiodPrice());
                }if(subscriptionData.getSubscriptionName().equals("Lifetime")){
                    lifetimeDevicestxt.setText(Integer.toString(subscriptionData.getMaxConnections()) + " " + getString(R.string.Devices));
                    LifetimePrice.setText(Double.toString(subscriptionData.getperiodPrice()) + "$");
                    Lifetimetxt.setText(getString(R.string.Lifetime) + " : " + Double.toString(subscriptionData.getMonthlyPrice()) + "$");
                    lifetimep = Double.toString(subscriptionData.getperiodPrice());

                }
            }
            OneMonthPrice.setVisibility(View.VISIBLE);
            SixMonthPrice.setVisibility(View.VISIBLE);
            OneYearPrice.setVisibility(View.VISIBLE);
            LifetimePrice.setVisibility(View.VISIBLE);
        }
    }

    @Override
    public void onClick(View v)
    {


        if (v == OneMonth)
        {
            SelectedSubscriptionType = 1;
            Subscribe_Clicked();
        }
        else if (v == SixMonths)
        {
            SelectedSubscriptionType = 2;
            Subscribe_Clicked();
        }
        else if (v == TwelveMonths)
        {
            SelectedSubscriptionType = 3;
            Subscribe_Clicked();
        }
        else if (v == Back_Btn)
        {
            this.finish();
        }
        else if (v == howsubscriptionworks)
        {
            this.startActivity(new Intent(this, How_Subscription_Works.class));
        }
        else if (v == unsubscribe_updowngrade)
        {
            ShowUnsubscribeDialog();
        }
        else if(v == LiftimeLL){
            SelectedSubscriptionType = 4;
            Subscribe_Clicked();
        }
    }

    private void Subscribe_Clicked()
    {
        //TODO feature temporary removed, waiting for backend integration with Google Store
//
//        Intent myIntent = new Intent(MyActivity, PaymentMethods.class);
//
//        MyActivity.startActivity(myIntent);
        // @todo temporary disbled below by Waqar for Payment Methods
        if ((SelectedSubscriptionType == 1 && OneMonthBtn.getText().toString().equals(getString(R.string.Unsubscribe))) ||
                (SelectedSubscriptionType == 2 && SixMonthsBtn.getText().toString().equals(getString(R.string.Unsubscribe))) ||
                (SelectedSubscriptionType == 3 && TwelveMonthsBtn.getText().toString().equals(getString(R.string.Unsubscribe))))
        {
            ShowUnsubscribeDialog();
        }
        else
        if ((SelectedSubscriptionType == 1 && OneMonthBtn.getText().toString().equals(getString(R.string.Downgrade))) ||
                (SelectedSubscriptionType == 2 && SixMonthsBtn.getText().toString().equals(getString(R.string.Downgrade))))
        {
            ShowDowngradeDialog();
        }
        else
        if ((SelectedSubscriptionType == 2 && SixMonthsBtn.getText().toString().equals(getString(R.string.Upgrade))) ||
                (SelectedSubscriptionType == 3 && TwelveMonthsBtn.getText().toString().equals(getString(R.string.Upgrade))))
        {
            ShowUpgradeDialog();
        }
        else
        {
            GetPayment(SelectedSubscriptionType);
        }
    }

    private void ShowDowngradeDialog()
    {
        AlertDialog.Builder builder = new AlertDialog.Builder(MyActivity);
        AlertDialog alertDialog = null;

        builder.setIcon(R.mipmap.alert);
        builder.setTitle((MyActivity.getResources().getString(R.string.DOWNGRADE)));
        builder.setMessage((MyActivity.getResources().getString(R.string.DowngradeInfo)));
        builder.setNegativeButton((MyActivity.getResources().getString(R.string.OK)), null);
        alertDialog = builder.create();
        alertDialog.show();
    }

    private void ShowUpgradeDialog()
    {
        AlertDialog.Builder builder = new AlertDialog.Builder(MyActivity);
        AlertDialog alertDialog = null;

        builder.setIcon(R.mipmap.alert);
        builder.setTitle((MyActivity.getResources().getString(R.string.UPGRADE)));
        builder.setMessage((MyActivity.getResources().getString(R.string.UpgradeInfo)));
        builder.setNegativeButton((MyActivity.getResources().getString(R.string.OK)), null);
        alertDialog = builder.create();
        alertDialog.show();
    }

    private void ShowUnsubscribeDialog()
    {
        AlertDialog.Builder builder = new AlertDialog.Builder(MyActivity);
        AlertDialog alertDialog = null;

        builder.setIcon(R.mipmap.alert_green);
        builder.setTitle((MyActivity.getResources().getString(R.string.UnsubscribeConfirmation)));
        builder.setMessage((MyActivity.getResources().getString(R.string.UnsubscribeText)));
        builder.setPositiveButton((MyActivity.getResources().getString(R.string.OK)), new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int whichButton)
            {
                Unsubsribe();
            }
        });
        builder.setNegativeButton((MyActivity.getResources().getString(R.string.Cancel)), null);
        alertDialog = builder.create();
        alertDialog.show();
    }

    private void ShowWrongInfoDialog()
    {
        AlertDialog.Builder builder = new AlertDialog.Builder(MyActivity);
        AlertDialog alertDialog = null;

        builder.setIcon(R.mipmap.alert_green);
        builder.setTitle((MyActivity.getResources().getString(R.string.NOTE)));
        builder.setMessage((MyActivity.getResources().getString(R.string.WrongAccount)));
        builder.setPositiveButton((MyActivity.getResources().getString(R.string.OK)), null);
        alertDialog = builder.create();
        alertDialog.show();
    }

    private void ShowRefreshDialog()
    {
        try {
            setWaitScreen(false);
            AlertDialog.Builder builder = new AlertDialog.Builder(MyActivity);
            AlertDialog alertDialog = null;

            builder.setIcon(R.mipmap.alert_green);
            builder.setTitle((MyActivity.getResources().getString(R.string.NOTE)));
            builder.setMessage((MyActivity.getResources().getString(R.string.ConnectionErrorClickToRefresh)));
            builder.setPositiveButton((MyActivity.getResources().getString(R.string.REFRESH)), new DialogInterface.OnClickListener() {
                public void onClick(DialogInterface dialog, int whichButton) {
                    GetSubscriptions();
                }
            });
            builder.setNegativeButton((MyActivity.getResources().getString(R.string.CANCEL)), null);
            alertDialog = builder.create();
            alertDialog.show();
        }catch (Exception ex)
        {
            commun.Log(ex.toString());
        }
    }

    private void Unsubsribe()
    {
        //Toufic 3/1/2018 -- google analytics --
        if(userprofile != null) MyApplication.getInstance().trackEvent(SubscriptionsCategory, UnSubscribedAction, userprofile.getId());
        //Toufic 3/1/2018

        startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse("https://play.google.com/store/account/subscriptions")));
    }

    private void SetUpBilling()
    {
        mHelper = new IabHelper(this, LICENSE);

        mHelper.enableDebugLogging(true);

        mHelper.startSetup(new IabHelper.OnIabSetupFinishedListener()
        {
            public void onIabSetupFinished(IabResult result)
            {
                commun.Log("onIabSetupFinished open ");

                try{
                    commun.Log("result : " + result);
                }catch (Exception ex){
                    ex.printStackTrace();
                    commun.Log("result exception : " + ex.toString());
                }

                // Have we been disposed of in the meantime? If so, quit.
                if (mHelper == null) return;

                if (!result.isSuccess())
                {
                    complain("Problem setting up in-app billing: " + result);
                    return;
                }

                // IAB is fully set up. Now, let's get an inventory of stuff we own.
                try
                {
                    mHelper.queryInventoryAsync(mGotInventoryListener);
                }
                catch (IabAsyncInProgressException e)
                {
                    complain("Error querying inventory. Another async operation in progress.");
                }
            }
        });
    }


    public void SubscribeCasperVPN()
    {
        commun.Log("####### SubscribeCasperVPN - SelectedSubscriptionType = " + SelectedSubscriptionType);
        if (!mHelper.subscriptionsSupported())
        {
            complain("Subscriptions not supported on your device yet. Sorry!");
            return;
        }

        if (SelectedSubscriptionType == 1)
        {
            OneMonthClicked();
        }
        else if (SelectedSubscriptionType == 2)
        {
            SixMonthsClicked();
        }
        else if (SelectedSubscriptionType == 3)
        {
            OneYearClicked();
        }
        else if (SelectedSubscriptionType == 4)
        {
            LifetimeClicked();
        }
    }

    // Listener that's called when we finish querying the items and subscriptions we own
    IabHelper.QueryInventoryFinishedListener mGotInventoryListener = new IabHelper.QueryInventoryFinishedListener()
    {
        public void onQueryInventoryFinished(IabResult result, Inventory inventory)
        {
            commun.Log("onQueryInventoryFinished open ");

            try{
                commun.Log("inventory SKU_ONE_MONTH : " + inventory.getPurchase(SKU_ONE_MONTH));
                commun.Log("inventory SKU_SIX_MONTHS : " + inventory.getPurchase(SKU_SIX_MONTHS));
                commun.Log("inventory SKU_ONE_YEAR : " + inventory.getPurchase(SKU_ONE_YEAR));
                commun.Log("inventory SKU_ONE_YEAR : " + inventory.getPurchase(SKU_LIFETIME));
            }catch (Exception ex){
                ex.printStackTrace();
                commun.Log("inventory exception : " + ex.toString());
            }
            // Have we been disposed of in the meantime? If so, quit.
            if (mHelper == null) return;

            if (result.isFailure())
            {
                complain("Failed to query inventory: " + result);
                return;
            }

            // First find out which subscription is auto renewing
            Purchase oneMonth = inventory.getPurchase(SKU_ONE_MONTH);
            Purchase sixMonths = inventory.getPurchase(SKU_SIX_MONTHS);
            Purchase oneYear = inventory.getPurchase(SKU_ONE_YEAR);
            Purchase lifetime = inventory.getPurchase(SKU_LIFETIME);

            IsOneMonthSubscribed = oneMonth != null;
            IsSixMonthsSubscribed = sixMonths != null;
            IsOneYearSubscribed = oneYear != null;
            IsLifetimeSubscribed = lifetime != null;

            IsSubscribed = ((oneMonth != null && verifyDeveloperPayload(SKU_ONE_MONTH, oneMonth))
                    || (sixMonths != null && verifyDeveloperPayload(SKU_SIX_MONTHS, sixMonths))
                    || (oneYear != null && verifyDeveloperPayload(SKU_ONE_YEAR, oneYear))
                    || (lifetime != null && verifyDeveloperPayload(SKU_LIFETIME, lifetime)));

            IsAutoRenewEnabled = ((oneMonth != null && oneMonth.isAutoRenewing())
                    || (sixMonths != null && sixMonths.isAutoRenewing())
                    || (oneYear != null && oneYear.isAutoRenewing()));

            UpdateUI();
            setWaitScreen(false);
        }
    };

    @Override
    public void receivedBroadcast()
    {
        try
        {
            mHelper.queryInventoryAsync(mGotInventoryListener);
        }
        catch (IabAsyncInProgressException e)
        {
            complain("Error querying inventory. Another async operation in progress.");
        }
    }

    //region payload
    boolean verifyDeveloperPayload(String itemUniqueId, Purchase purchase)
    {
        try {

            String responsePayload = purchase.getDeveloperPayload();
            String computedPayload = getUserEmailFromAndroidAccounts() + itemUniqueId;

            return responsePayload != null && responsePayload.equals(computedPayload);
        }catch (Exception ex){
            ex.printStackTrace();
            commun.Log(ex.toString());
            return false;
        }
    }

    private String getUserEmailFromAndroidAccounts()
    {
        return userprofile.getLogin();
    }
    //endregion

    // Callback for when a purchase is finished
    IabHelper.OnIabPurchaseFinishedListener mPurchaseFinishedListener = new IabHelper.OnIabPurchaseFinishedListener()
    {
        public void onIabPurchaseFinished(IabResult result, Purchase purchase)
        {
            // if we were disposed of in the meantime, quit.
            commun.Log("mPurchaseFinishedListener opened ");
            try{
                commun.Log("purchase " + purchase.getSku().toString());
            }catch (Exception ex){
                commun.Log("mPurchaseFinishedListener exception ");

            }

            if (mHelper == null) return;

            if (result.isFailure())
            {
                commun.Log("Error purchasing: " + result);
                setWaitScreen(false);
                return;
            }

            if (!verifyDeveloperPayload(purchase.getSku(), purchase))
            {
                commun.Log("Error purchasing. Authenticity verification failed.");
                setWaitScreen(false);
                return;
            }

            commun.updateIntercomAttributeBoolean("Subscribed", true);
            commun.updateIntercomAttributeBoolean("Canceled Subscription", false);

            IsOneMonthSubscribed = purchase.getSku().equals(SKU_ONE_MONTH);
            IsSixMonthsSubscribed = purchase.getSku().equals(SKU_SIX_MONTHS);
            IsOneYearSubscribed = purchase.getSku().equals(SKU_ONE_YEAR);
            IsLifetimeSubscribed = purchase.getSku().equals(SKU_LIFETIME);

            if (IsOneMonthSubscribed || IsSixMonthsSubscribed || IsOneYearSubscribed)
            {
                IsSubscribed = true;
                IsAutoRenewEnabled = purchase.isAutoRenewing();

                if(IsOneMonthSubscribed)
                {
                    //Toufic 3/1/2018 -- google analytics --
                    if(userprofile != null) MyApplication.getInstance().trackEventValue(SubscriptionsCategory, Subscribed1MAction, userprofile.getId(), value);
                    //Toufic 3/1/2018

                    Update_Expiry_Date(1, purchase);
                }
                else if(IsSixMonthsSubscribed)
                {
                    //Toufic 3/1/2018 -- google analytics --
                    if(userprofile != null) MyApplication.getInstance().trackEventValue(SubscriptionsCategory, Subscribed6MAction, userprofile.getId(), value);
                    //Toufic 3/1/2018

                    Update_Expiry_Date(6,purchase);
                }
                else if(IsOneYearSubscribed)
                {
                    //Toufic 3/1/2018 -- google analytics --
                    if(userprofile != null) MyApplication.getInstance().trackEventValue(SubscriptionsCategory, Subscribed1YAction, userprofile.getId(), value);
                    //Toufic 3/1/2018

                    Update_Expiry_Date(12, purchase);
                }
            }
            else if(IsLifetimeSubscribed){
                IsSubscribed = true;
                //Toufic 3/1/2018 -- google analytics --
                if(userprofile != null) MyApplication.getInstance().trackEventValue(SubscriptionsCategory, SubscribedLifetimeAction, userprofile.getId(), value);
                //Toufic 3/1/2018

                Update_Expiry_Date(4, purchase);

            }
            else commun.Log("Error purchasing. Should contact us!");

        }
    };

    //region Subscribe
    public void OneMonthClicked ()
    {
        String payload = getUserEmailFromAndroidAccounts() + SKU_ONE_MONTH;

        List<String> oldSkus = new ArrayList<>();
        if (IsSixMonthsSubscribed)
            oldSkus .add(SKU_SIX_MONTHS);
        else if (IsOneYearSubscribed)
            oldSkus .add(SKU_ONE_YEAR);
        if (oldSkus.isEmpty()) oldSkus = null;

        try
        {
            value = Math.round(Double.parseDouble(onemp));
            mHelper.launchPurchaseFlow(this, SKU_ONE_MONTH, IabHelper.ITEM_TYPE_SUBS, oldSkus, RC_REQUEST, mPurchaseFinishedListener, payload);
        }
        catch (IabAsyncInProgressException e)
        {
            complain("Error launching purchase flow. Another async operation in progress");
            setWaitScreen(false);
        }
    }

    public void SixMonthsClicked ()
    {
        String payload = getUserEmailFromAndroidAccounts() + SKU_SIX_MONTHS;

        List<String> oldSkus = new ArrayList<>();
        if (IsOneMonthSubscribed)
            oldSkus .add(SKU_ONE_MONTH);
        else if (IsOneYearSubscribed)
            oldSkus .add(SKU_ONE_YEAR);
        if (oldSkus.isEmpty()) oldSkus = null;

        try
        {
            value = Math.round(Double.parseDouble(sixmp));
            mHelper.launchPurchaseFlow(this, SKU_SIX_MONTHS, IabHelper.ITEM_TYPE_SUBS, oldSkus, RC_REQUEST, mPurchaseFinishedListener, payload);
        }
        catch (IabAsyncInProgressException e)
        {
            complain("Error launching purchase flow. Another async operation in progress");
            setWaitScreen(false);
        }
    }

    public void OneYearClicked ()
    {
        String payload = getUserEmailFromAndroidAccounts() + SKU_ONE_YEAR;

        List<String> oldSkus = new ArrayList<>();
        if (IsOneMonthSubscribed)
            oldSkus .add(SKU_ONE_MONTH);
        else if (IsSixMonthsSubscribed)
            oldSkus .add(SKU_SIX_MONTHS);
        if (oldSkus.isEmpty()) oldSkus = null;

        try
        {
            value = Math.round(Double.parseDouble(oneyp));
            mHelper.launchPurchaseFlow(this, SKU_ONE_YEAR, IabHelper.ITEM_TYPE_SUBS, oldSkus, RC_REQUEST, mPurchaseFinishedListener, payload);
        }
        catch (IabAsyncInProgressException e)
        {
            complain("Error launching purchase flow. Another async operation in progress");
            setWaitScreen(false);
        }
    }

    public  void  LifetimeClicked(){
        String payload = getUserEmailFromAndroidAccounts() + SKU_LIFETIME;

        List<String> oldSkus = new ArrayList<>();
        if (IsOneMonthSubscribed)
            oldSkus .add(SKU_ONE_MONTH);
        else if (IsSixMonthsSubscribed)
            oldSkus .add(SKU_SIX_MONTHS);
        if (oldSkus.isEmpty()) oldSkus = null;

        try
        {
            value = Math.round(Double.parseDouble(lifetimep));
            mHelper.launchPurchaseFlow(this, SKU_LIFETIME, IabHelper.ITEM_TYPE_INAPP, oldSkus, RC_REQUEST, mPurchaseFinishedListener, payload);
        }
        catch (IabAsyncInProgressException e)
        {
            complain("Error launching purchase flow. Another async operation in progress");
            setWaitScreen(false);
        }
    }
    //endregion

    //region UI
    public void UpdateUI()
    {
        OneMonth.setClickable(true);
        SixMonths.setClickable(true);
        TwelveMonths.setClickable(true);
        LiftimeLL.setClickable(true);

        if(userprofile != null)
            if(userprofile.getSubscription().getSubscriptionId().equals("12c3fc2a-4915-43c3-a992-388b38aa02e3")) {
                OneMonth.setClickable(false);
                SixMonths.setClickable(false);
                TwelveMonths.setClickable(false);
                LiftimeLL.setClickable(false);
                OneMonthBtn.setVisibility(View.INVISIBLE);
                SixMonthsBtn.setVisibility(View.INVISIBLE);
                TwelveMonthsBtn.setVisibility(View.INVISIBLE);
                LifeimteSubscribeBtn.setVisibility(View.INVISIBLE);
                return;
            }

        OneMonthBtn.setText(getString(R.string.Package));
        SixMonthsBtn.setText(getString(R.string.Package));
        TwelveMonthsBtn.setText(getString(R.string.Package));
        // Logic
        // - if user is active and his subscription still auto renewable - buttons will change
        // - else if (user not active / his subscription canceled) - buttons still package (he can subscribe)
        // one case is: if user subscription canceled and he subscribe before his subscription get inactive - his recurring will begin after 1 month that he payed, he will gain his expiry date after he cancel his subscription
        // Logic

        commun.Log("IsAutoRenewEnabled:: " + IsAutoRenewEnabled);
        if(commun.IsActive() && IsAutoRenewEnabled)
        {
            if (IsOneMonthSubscribed)
            {
                OneMonthBtn.setText(getString(R.string.Unsubscribe));
                SixMonthsBtn.setText(getString(R.string.Upgrade));
                TwelveMonthsBtn.setText(getString(R.string.Upgrade));
            }
            else if (IsSixMonthsSubscribed)
            {
                OneMonthBtn.setText(getString(R.string.Downgrade));
                SixMonthsBtn.setText(getString(R.string.Unsubscribe));
                TwelveMonthsBtn.setText(getString(R.string.Upgrade));
            }
            else if (IsOneYearSubscribed)
            {
                OneMonthBtn.setText(getString(R.string.Downgrade));
                SixMonthsBtn.setText(getString(R.string.Downgrade));
                TwelveMonthsBtn.setText(getString(R.string.Unsubscribe));
            }
            if(!IsSubscribed) ShowWrongInfoDialog();  // if wrong account just show popup message for user - each time he enter subscribe page
        }
    }

    void setWaitScreen(boolean set)
    {
        Loading.setVisibility(set ? View.VISIBLE : View.GONE);
        Loading_Text.setVisibility(set ? View.VISIBLE : View.GONE);
    }

    void complain(String message)
    {
        commun.Log(message);

        AlertDialog.Builder bld = new AlertDialog.Builder(this);
        bld.setMessage(message);
        bld.setNeutralButton(getString(R.string.OK), null);
        bld.create().show();
    }
    //endregion

    //region On Activity result
    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data)
    {
        // if we were disposed of in the meantime, quit.
        if (mHelper == null) return;

        // Pass on the activity result to the helper for handling
        if (!mHelper.handleActivityResult(requestCode, resultCode, data))
        {
            // not handled, so handle it ourselves (here's where you'd perform any handling of activity results not related to in-app billing...
            super.onActivityResult(requestCode, resultCode, data);
        }
        else
        {
            //onActivityResult handled by IABUtil.
        }
    }
    //endregion


    public static boolean active = false;

    @Override
    public void onStart()
    {
        super.onStart();
        active = true;
    }

    //region ondestory
    @Override
    public void onDestroy()
    {
        super.onDestroy();
        active = false;

        try {
            //It's important to dispose of the helper here!
            if (mHelper != null)
            {
                mHelper.disposeWhenFinished();
                mHelper = null;
            }
        }catch (Exception ex)
        {
            ex.printStackTrace();
            commun.Log(ex.toString());
        }
    }
    //endregion


    //region Update Expiry date
    private void GetPayment(final int month)
    {
        setWaitScreen(true);
        Loading_Text.setText(R.string.Loading);
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
                        case 4: subscriptionId = "12c3fc2a-4915-43c3-a992-388b38aa02e3";
                            break;
                    }

                    JSONObject obj = new JSONObject();
                    obj.put("subscriptionId", subscriptionId);

                    conn.GetData(PAYMENT_CLASS_ID, "1", "payment/twocheckout/code/generate", "POST", obj.toString(), true, MyActivity);
                }
                catch (Exception e)
                {
                    commun.Log(e.toString());
                    runOnUiThread(new Runnable()
                    {
                        @Override
                        public void run()
                        {
                            ShowErrorDialog2();
                        }
                    });
                    e.printStackTrace();
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
                payment = payment1;

                SubscribeCasperVPN();
            }
            catch (Exception e) {
                commun.Log(e.getMessage());
                ShowErrorDialog2();
            }
        }
    }

    private void Update_Expiry_Date(final int month, final Purchase purchase)
    {
        Thread thread = new Thread(new Runnable()
        {
            @Override
            public void run()
            {
                try
                {
                    String email, merchant_product_id, custom_check_code, pay_method, order_number, currency_code;

                    String code_generate_url = payment.getCheckouturl();
                    Map<String, String> Params = commun.readParamsIntoMap(code_generate_url);

                    email  = userprofile.getLogin();
                    merchant_product_id = Params.get("subscriptionId");
                    custom_check_code = Params.get("code");
                    pay_method = "android";
                    order_number = purchase.getOrderId();;
                    currency_code = "USD";
                    String product_id = "1";
                    String product_description = "1_Month";
                    String payment_sum = onemp;
                    String isrecurring = "false";
                    String app_version = MyActivity.getPackageManager().getPackageInfo(getPackageName(), 0).versionName;

                    switch (month) {
                        case 1:
                            product_id = "1";
                            product_description = "1_Month";
                            payment_sum = onemp;
                            break;
                        case 6:
                            product_id = "2";
                            product_description = "6_Months";
                            payment_sum = sixmp;
                            break;
                        case 12:
                            product_id = "3";
                            product_description = "1_Year";
                            payment_sum = oneyp;
                            break;
                        case 4:
                            product_id = "4";
                            product_description = "Lifetime";
                            payment_sum = lifetimep;
                            break;
                    }

                    String url = "payment/twocheckout/checkoutApps?email=" + email + "&merchant_product_id=" + merchant_product_id + "&custom_check_code=" + custom_check_code + "&pay_method=" + pay_method + "&product_id=" + product_id + "&product_description=" + product_description + "&order_number=" + order_number + "&currency_code=" + currency_code  + "&total=" + payment_sum + "&isrecurring=" + isrecurring + "&payment_sum=" + payment_sum + "&app_version=" + app_version + "&google_subscription_token=" + purchase.getToken();

                    conn.GetData(SUBSCRIPTION_CLASS_ID, "2", url , "GET", null, false, MyActivity);
                }
                catch (Exception e) {
                    commun.Log(e.toString());
                    runOnUiThread(new Runnable()
                    {
                        @Override
                        public void run()
                        {
                            ShowErrorDialog();
                        }
                    });
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
                    UpdateAppExpiryDate();
                    commun.Log("####################### Payment Success ####################### ");
                }
                else  // payment error
                {
                    commun.Log("####################### Payment Failed ####################### ");
                    ShowErrorDialog();
                }

            }
            catch (Exception e) {
                commun.Log(e.getMessage());
                ShowErrorDialog();
            }
        }else
            ShowErrorDialog();
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
                        runOnUiThread(new Runnable()
                        {
                            @Override
                            public void run()
                            {
                                ShowErrorDialog();
                            }
                        });
                        return;
                    }

                    conn.GetData(SUBSCRIPTION_CLASS_ID, "3", "users/profile", "GET", null, true, MyActivity);
                }
                catch (Exception e)
                {
                    commun.Log(e.toString());
                    runOnUiThread(new Runnable()
                    {
                        @Override
                        public void run()
                        {
                            ShowErrorDialog();
                        }
                    });
                    e.printStackTrace();
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
                    UpdateUI();
                    setWaitScreen(false);
                    ShowSuccessSubscription();
                }
                else
                {
                    ShowErrorDialog();
                    commun.Log("On User Profile Result: " + result);
                }
            }
            catch (Exception e)
            {
                commun.Log(e.getMessage());
                ShowErrorDialog();
            }
        }
    }

    private void ShowSuccessSubscription(){
        try {
            setWaitScreen(false);

            AlertDialog.Builder builder = new AlertDialog.Builder(MyActivity);
            AlertDialog alertDialog = null;

            builder.setIcon(R.mipmap.alert_green);
            builder.setTitle((MyActivity.getResources().getString(R.string.allset)));
            builder.setMessage((MyActivity.getResources().getString(R.string.SubscriptionActivated)));
            builder.setPositiveButton((MyActivity.getResources().getString(R.string.OK)), null);
            alertDialog = builder.create();
            alertDialog.show();
        }catch (Exception ex){
            commun.Log(ex.getMessage());}
    }

    private void ShowErrorDialog()
    {
        try {
            setWaitScreen(false);

            AlertDialog.Builder builder = new AlertDialog.Builder(MyActivity);
            AlertDialog alertDialog = null;

            builder.setIcon(R.mipmap.alert_green);
            builder.setTitle((MyActivity.getResources().getString(R.string.NOTE)));
            builder.setMessage((MyActivity.getResources().getString(R.string.ErrorContactUs)));
            builder.setPositiveButton((MyActivity.getResources().getString(R.string.OK)), new DialogInterface.OnClickListener()
            {
                public void onClick(DialogInterface dialog, int whichButton)
                {
                    Intercom.client().displayMessenger();
                }
            });
            alertDialog = builder.create();
            alertDialog.show();
        }catch (Exception ex){
            commun.Log(ex.getMessage());}
    }

    private void ShowErrorDialog2()
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
            commun.Log(ex.getMessage());}
    }
    //endregion

    private void showReloginMessage()
    {
        try {
            AlertDialog.Builder SubscriptionDialogBuilder = new AlertDialog.Builder(MyActivity);
            SubscriptionDialogBuilder.setCancelable(false);
            SubscriptionDialogBuilder.setIcon(R.mipmap.alert_green);
            SubscriptionDialogBuilder.setTitle(MyActivity.getResources().getString(R.string.TimeoutTitle));
            SubscriptionDialogBuilder.setPositiveButton((MyActivity.getResources().getString(R.string.Login)), new DialogInterface.OnClickListener() {
                public void onClick(DialogInterface dialog, int whichButton) {
                    logout();
                }
            });
            SubscriptionDialogBuilder.setMessage(MyActivity.getResources().getString(R.string.TimeoutMessage));
            AlertDialog ReloginDialog = SubscriptionDialogBuilder.create();
            ReloginDialog.show();
        }catch (Exception ex){
            ex.printStackTrace();
            commun.Log(ex.toString());
        }
    }

    private void logout()
    {
        try {
            Intent myIntent = new Intent(this, Login.class);
            this.startActivityForResult(myIntent, 2);
        }catch (Exception ex){
            ex.printStackTrace();
            commun.Log(ex.toString());
        }
    }

}
