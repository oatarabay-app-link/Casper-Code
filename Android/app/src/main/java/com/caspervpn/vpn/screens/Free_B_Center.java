package com.caspervpn.vpn.screens;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Color;
import androidx.appcompat.app.AppCompatActivity;
import android.os.Bundle;
import android.text.Html;
import android.util.Log;
import android.view.KeyEvent;
import android.view.View;
import android.widget.ImageButton;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;

//import com.appodeal.ads.Appodeal;
//import com.appodeal.ads.RewardedVideoCallbacks;
import com.caspervpn.vpn.R;
import com.caspervpn.vpn.common.Commun;
import com.caspervpn.vpn.common.DataConnection;
import com.caspervpn.vpn.common.SubscriptionClass;
import com.caspervpn.vpn.helper.MyApplication;
import com.caspervpn.vpn.helper.MyButton;
import com.caspervpn.vpn.helper.MyTextView;
import com.pollfish.interfaces.PollfishClosedListener;
import com.pollfish.interfaces.PollfishOpenedListener;
import com.pollfish.interfaces.PollfishSurveyCompletedListener;
import com.pollfish.interfaces.PollfishSurveyNotAvailableListener;
import com.pollfish.interfaces.PollfishSurveyReceivedListener;
import com.pollfish.interfaces.PollfishUserNotEligibleListener;
import com.pollfish.main.PollFish;

import static com.caspervpn.vpn.common.Configuration.APPODEAL;
import static com.caspervpn.vpn.common.Configuration.FreeBeeCenterScreenName;
import static com.caspervpn.vpn.common.Configuration.FreePremiumCategory;
import static com.caspervpn.vpn.common.Configuration.POLLFISH;
import static com.caspervpn.vpn.common.Configuration.RewardVideoMAction;
import static com.caspervpn.vpn.common.Configuration.SurveyAction;
import static com.caspervpn.vpn.common.Configuration.pollfishCustomMode;
import static com.caspervpn.vpn.common.Configuration.pollfishRelease;
import static com.caspervpn.vpn.common.Configuration.userprofile;

public class Free_B_Center extends AppCompatActivity
//        implements
//
//        PollfishSurveyCompletedListener,
//        PollfishClosedListener, PollfishSurveyReceivedListener,
//        PollfishSurveyNotAvailableListener, PollfishUserNotEligibleListener, PollfishOpenedListener

{

//    private LinearLayout Fill_Servey, Watch_Video;
//    private MyTextView Loading_Text;
//    RelativeLayout Loading;
//    Commun commun;
//    Activity MyActivity;
//    private DataConnection conn;
//    ImageButton Back_Btn;
//    String type;
//    MyTextView txtSurveryLoading;
//
//    @Override
//    protected void onCreate(Bundle savedInstanceState) {
//        super.onCreate(savedInstanceState);
//        setContentView(R.layout.activity_free_b_center);
//
////        Initial Appodeal
//        Appodeal.setBannerViewId(R.id.appodealBannerView);
//        Appodeal.initialize(this, APPODEAL, Appodeal.REWARDED_VIDEO | Appodeal.BANNER | Appodeal.NATIVE | Appodeal.MREC);
//
//        Init();
//    }
//
//    private void Init(){
//        MyActivity = this;
//        Loading = (RelativeLayout) findViewById(R.id.loading);
//        Loading_Text = (MyTextView) findViewById(R.id.Loading_Text);
//        Loading_Text.setText(getString(R.string.Loading));
//        commun = new Commun(MyActivity);
//        conn = new DataConnection(this);
//
//        //Toufic 3/1/2018 -- google analytics --
//        MyApplication.getInstance().trackScreenView(FreeBeeCenterScreenName);
//        //Toufic 3/1/2018
//
//        TextView ApplicationTitle = (TextView) findViewById(R.id.ApplicationTitle);
//        ApplicationTitle.setText(getString(R.string.GETFREEPREMIUM));
//
//        Back_Btn = (ImageButton) findViewById(R.id.back);
//        Back_Btn.setOnClickListener(new View.OnClickListener() {
//            @Override
//            public void onClick(View v) {
//                Appodeal.hide(MyActivity, Appodeal.BANNER);
//                Appodeal.destroy(Appodeal.BANNER);
//                MyActivity.finish();
//            }
//        });
//
//        Fill_Servey = (LinearLayout) findViewById(R.id.Fill_Servey);
//        Fill_Servey.setOnClickListener(new View.OnClickListener() {
//            @Override
//            public void onClick(View v) {
//                PollFish.show();
//            }
//        });
//        txtSurveryLoading = (MyTextView) findViewById(R.id.txtSurveryLoading);
//        Fill_Servey.setClickable(false);
//        txtSurveryLoading.setText(R.string.LoadingSurvey);
//
//        Watch_Video = (LinearLayout) findViewById(R.id.Watch_Video);
//        Watch_Video.setOnClickListener(new View.OnClickListener() {
//            @Override
//            public void onClick(View v) {
//                if(!Appodeal.isLoaded(Appodeal.REWARDED_VIDEO)) ShowWaitDialog();
//                else Appodeal.show(MyActivity, Appodeal.REWARDED_VIDEO);
//            }
//        });
//
////        Checking if rewarded video is loaded
////        Appodeal.isLoaded(Appodeal.REWARDED_VIDEO);
//        Appodeal.setRewardedVideoCallbacks(new RewardedVideoCallbacks() {
//            @Override
//            public void onRewardedVideoLoaded() {
//                Log.d("Appodeal", "onRewardedVideoLoaded");
//            }
//            @Override
//            public void onRewardedVideoFailedToLoad() {
//                commun.DisplayToast("Rewarded Video is not available", false);
//                Log.d("Appodeal", "onRewardedVideoFailedToLoad");
//            }
//            @Override
//            public void onRewardedVideoShown() {
//                Log.d("Appodeal", "onRewardedVideoShown");
//            }
//            @Override
//            public void onRewardedVideoFinished(int amount, String name) {
//                //Toufic 3/1/2018 -- google analytics --
//                if(userprofile != null) MyApplication.getInstance().trackEvent(FreePremiumCategory, RewardVideoMAction, userprofile.getId());
//                //Toufic 3/1/2018
//
//                commun.DisplayToast(getResources().getString(R.string.WaitfinishLoading), false);
//                Log.d("Appodeal", "onRewardedVideoFinished");
//                type = "Watch_Videos";
//                GetPayment();
//            }
//            @Override
//            public void onRewardedVideoClosed(boolean finished) {
//                Log.d("Appodeal", "onRewardedVideoClosed");
//            }
//        });
//
//        // Banner
//        Appodeal.show(this, Appodeal.BANNER_BOTTOM);
//        Appodeal.setBannerViewId(R.id.appodealBannerViewBanner);
//
//        if(userprofile == null)
//            showReloginMessage();
//    }
//
//    // region Pollfish
//    @Override
//    public void onPollfishOpened() {
//        Log.d("Pollfish", "onPollfishOpened");
//    }
//
//    @Override
//    public void onPollfishClosed() {
//        Log.d("Pollfish", "onPollfishClosed");
//        Fill_Servey.setClickable(false);
//        txtSurveryLoading.setText(R.string.LoadingSurvey);
//    }
//
//    @Override
//    public void onPollfishSurveyCompleted(boolean playfulSurvey, int surveyPrice) {
//        //Toufic 3/1/2018 -- google analytics --
//        if(userprofile != null) MyApplication.getInstance().trackEvent(FreePremiumCategory, SurveyAction, userprofile.getId());
//        //Toufic 3/1/2018
//
//        type ="Fill_Survey";
//        GetPayment();
//        Fill_Servey.setClickable(false);
//        txtSurveryLoading.setText(R.string.LoadingSurvey);
//    }
//
//    @Override
//    public void onPollfishSurveyNotAvailable() {
//        Fill_Servey.setClickable(false);
//        txtSurveryLoading.setText(R.string.LoadingSurvey);
//        Log.d("Pollfish", "onPollfishSurveyNotAvailable");
//        setWaitScreen(false);
//    }
//
//    @Override
//    public void onPollfishSurveyReceived(boolean playfulSurvey, int surveyPrice) {
//        /*
//        onPollfishSurveyReceived show the button
//        then in completed and user not eligible you should handle the states
//         */
//        Log.d("Pollfish", "onPollfishSurveyReceived(" + playfulSurvey + " , " + surveyPrice + ")");
//        Fill_Servey.setClickable(true);
//        txtSurveryLoading.setText(R.string.FillOutSurvey);
//        setWaitScreen(false);
//    }
//
//    @Override
//    public void onUserNotEligible() {
//        Fill_Servey.setClickable(false);
//        txtSurveryLoading.setText(R.string.LoadingSurvey);
//        Log.d("Pollfish", "onUserNotEligible");
//        // show message that he can take a reward video instead
//    }
//    // end region Pollfish
//
//    @Override
//    public void onResume() {
//
////        initiate Pollfish in the onResume
////        in custom mode and then call hide just after that
//        super.onResume();
//
//        Log.d("Pollfish", "onResume() ");
//
//        PollFish.ParamsBuilder paramsBuilder = new PollFish.ParamsBuilder(POLLFISH)
//                .indicatorPadding(5)
//                .releaseMode(pollfishRelease)
//                .customMode(pollfishCustomMode)
//                .build();
//
//        PollFish.initWith(this, paramsBuilder);
//        PollFish.hide();
//
//
//        // Appodeal
//        Appodeal.onResume(this, Appodeal.BANNER);
//    }
//
//    //    //region Update Expiry date
//    private void GetPayment()
//    {
//        try {
//            SubscriptionClass subscriptionClass = new SubscriptionClass(Loading_Text, commun, MyActivity, Loading, type, MyActivity.getPackageManager().getPackageInfo(getPackageName(), 0).versionName);
//            subscriptionClass.getPayment("84399a16-ee63-42aa-93e5-a51d36b88960");
//        }catch (Exception ex){
//            commun.Log(ex.toString());
//        }
//    }
//
//    void setWaitScreen(boolean set)
//    {
//        Loading.setVisibility(set ? View.VISIBLE : View.GONE);
//        Loading_Text.setVisibility(set ? View.VISIBLE : View.GONE);
//    }
//    //endregion
//
//    public static boolean active = false;
//
//    @Override
//    public void onStart()
//    {
//        super.onStart();
//        active = true;
//    }
//
//    @Override
//    public void onDestroy()
//    {
//        super.onDestroy();
//        Appodeal.hide(this, Appodeal.BANNER);
//        Appodeal.destroy(Appodeal.BANNER);
//        active = false;
//    }
//
//
//    @Override
//    public void onBackPressed() {
//        Appodeal.hide(this, Appodeal.BANNER);
//        Appodeal.destroy(Appodeal.BANNER);
//        MyActivity.finish();
//    }
//
//    @Override
//    public boolean onKeyDown(int keyCode, KeyEvent event) {
//        if (keyCode == KeyEvent.KEYCODE_BACK) {
//            Appodeal.hide(this, Appodeal.BANNER);
//            Appodeal.destroy(Appodeal.BANNER);
//            MyActivity.finish();
//            return true;
//        }
//
//        return super.onKeyDown(keyCode, event);
//    }
//
//
//    private void ShowWaitDialog()
//    {
//        try {
//            setWaitScreen(false);
//
//            AlertDialog.Builder builder = new AlertDialog.Builder(MyActivity);
//            AlertDialog alertDialog = null;
//
//            builder.setIcon(R.mipmap.alert_green);
//            builder.setTitle((MyActivity.getResources().getString(R.string.NOTE)));
//            builder.setMessage(MyActivity.getResources().getString(R.string.Waitingrewardvideo));
//            builder.setPositiveButton((MyActivity.getResources().getString(R.string.OK)), null);
//            alertDialog = builder.create();
//            alertDialog.show();
//        }catch (Exception ex){
//            ex.printStackTrace();
//            commun.Log(ex.toString());
//        }
//    }
//
//    private void showReloginMessage()
//    {
//        try {
//            AlertDialog.Builder SubscriptionDialogBuilder = new AlertDialog.Builder(MyActivity);
//            SubscriptionDialogBuilder.setCancelable(false);
//            SubscriptionDialogBuilder.setIcon(R.mipmap.alert_green);
//            SubscriptionDialogBuilder.setTitle(MyActivity.getResources().getString(R.string.TimeoutTitle));
//            SubscriptionDialogBuilder.setPositiveButton((MyActivity.getResources().getString(R.string.Login)), new DialogInterface.OnClickListener() {
//                public void onClick(DialogInterface dialog, int whichButton) {
//                    logout();
//                }
//            });
//            SubscriptionDialogBuilder.setMessage(MyActivity.getResources().getString(R.string.TimeoutMessage));
//            AlertDialog ReloginDialog = SubscriptionDialogBuilder.create();
//            ReloginDialog.show();
//        }catch (Exception ex){
//            ex.printStackTrace();
//            commun.Log(ex.toString());
//        }
//    }
//
//    private void logout()
//    {
//        try {
//            Intent myIntent = new Intent(this, Login.class);
//            this.startActivityForResult(myIntent, 2);
//        }catch (Exception ex){
//            ex.printStackTrace();
//            commun.Log(ex.toString());
//        }
//    }

}
