package com.caspervpn.vpn.screens;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.res.Resources;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Color;
import android.net.Uri;
import androidx.multidex.MultiDex;
import androidx.appcompat.app.AppCompatActivity;
import android.os.Bundle;
import android.text.Html;
import android.view.View;
import android.view.ViewTreeObserver;
import android.widget.FrameLayout;
import android.widget.ImageButton;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.caspervpn.vpn.R;
import com.caspervpn.vpn.classes.AffiliateClass;
import com.caspervpn.vpn.common.Commun;
import com.caspervpn.vpn.common.DataConnection;
import com.caspervpn.vpn.helper.MyApplication;
import com.caspervpn.vpn.helper.MyButton;
import com.caspervpn.vpn.helper.MyTextView;
import com.facebook.CallbackManager;
import com.facebook.FacebookCallback;
import com.facebook.FacebookException;
import com.facebook.share.Sharer;
import com.facebook.share.model.ShareContent;
import com.facebook.share.model.ShareLinkContent;
import com.facebook.share.widget.ShareDialog;
import com.twitter.sdk.android.Twitter;
import com.twitter.sdk.android.core.TwitterAuthConfig;
import com.twitter.sdk.android.tweetcomposer.TweetComposer;

import org.json.JSONException;
import org.json.JSONObject;

import java.lang.reflect.Field;
import java.net.URL;

import io.fabric.sdk.android.Fabric;

import static com.caspervpn.vpn.common.Configuration.AFFILIATEID;
import static com.caspervpn.vpn.common.Configuration.FbShareAction;
import static com.caspervpn.vpn.common.Configuration.ShareAffiliate;
import static com.caspervpn.vpn.common.Configuration.ShareAffiliateCategory;
import static com.caspervpn.vpn.common.Configuration.TWITTER_KEY1;
import static com.caspervpn.vpn.common.Configuration.TWITTER_KEY2;
import static com.caspervpn.vpn.common.Configuration.TWITTER_KEY3;
import static com.caspervpn.vpn.common.Configuration.TWITTER_SECRET1;
import static com.caspervpn.vpn.common.Configuration.TWITTER_SECRET2;
import static com.caspervpn.vpn.common.Configuration.TWITTER_SECRET3;
import static com.caspervpn.vpn.common.Configuration.TWITTER_SECRET4;
import static com.caspervpn.vpn.common.Configuration.TWITTER_SECRET5;
import static com.caspervpn.vpn.common.Configuration.TwitterShareAction;
import static com.caspervpn.vpn.common.Configuration.userprofile;

public class Share_link extends AppCompatActivity implements View.OnClickListener
{

    ImageButton Back_Btn;
    LinearLayout Share_Twitter, Share_FaceBook, Text, Affiliate;
    Commun commun;
    Activity MyActivity;
    private MyTextView Loading_Text, AffiliateDesc, affiliatehiddentxt, affiliatetxt;
    MyButton affiliatebtn, affiliateRefreshbtn;
    RelativeLayout Loading;
    LinearLayout affiliateimg;
    String type;
    private DataConnection conn;

    CallbackManager callbackManager;

    public static final int TWITTER_REQUEST_CODE = 100;

    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_share_link);
        MyActivity = this;
        commun = new Commun(this);

        Init();

        //Toufic 3/1/2018 -- google analytics --
        MyApplication.getInstance().trackScreenView(ShareAffiliate);
        //Toufic 3/1/2018
    }
    @Override
    protected void onResume()
    {
        super.onResume();
    }

    private void Init()
    {
        commun.Log("share link started");

        conn = new DataConnection(this);
        TextView ApplicationTitle = (TextView) findViewById(R.id.ApplicationTitle);
        ApplicationTitle.setText(getString(R.string.Affiliate).toUpperCase());

        Back_Btn = (ImageButton) findViewById(R.id.back);
        Share_Twitter = (LinearLayout) findViewById(R.id.Share_Twitter);
        Share_FaceBook = (LinearLayout) findViewById(R.id.Share_FaceBook);
        Text =  (LinearLayout) findViewById(R.id.Text);
        Affiliate = (LinearLayout) findViewById(R.id.Affiliate);
        AffiliateDesc = (MyTextView) findViewById(R.id.AffiliateDesc);
        affiliateimg = (LinearLayout) findViewById(R.id.affiliateimg);
        Text.setVisibility(View.GONE);
        Share_FaceBook.setVisibility(View.GONE);
        Share_Twitter.setVisibility(View.GONE);
        Share_FaceBook.setClickable(false);
        Share_Twitter.setClickable(false);


        Loading = (RelativeLayout) findViewById(R.id.loading);
        Loading_Text = (MyTextView) findViewById(R.id.Loading_Text);
        Loading_Text.setText(getString(R.string.Loading));

        Share_Twitter.setOnClickListener(this);
        Share_FaceBook.setOnClickListener(this);
        Back_Btn.setOnClickListener(this);

        affiliatetxt = (MyTextView) findViewById(R.id.affiliatetxt);
        affiliatetxt.setText("");
        affiliatehiddentxt = (MyTextView) findViewById(R.id.affiliatehiddentxt);


        affiliatebtn = (MyButton) findViewById(R.id.affiliatebtn);
        affiliatebtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

                if (affiliatehiddentxt.getText().equals("")) {
                    // show accept privacy policy button then when user clicks ok - create his account
                    AlertDialog.Builder builder = new AlertDialog.Builder(MyActivity);
                    AlertDialog alertDialog = null;

                    View dialogView = getLayoutInflater().inflate(R.layout.terms_and_conditions_notification, null);
                    TextView NotificationText = (TextView) dialogView.findViewById(R.id.TermsAndConditions);
                    NotificationText.setText(Html.fromHtml(MyActivity.getResources().getString(R.string.TermsNotification)));
                    NotificationText.setOnClickListener(new View.OnClickListener() {
                        @Override
                        public void onClick(View v) {
                            // TODO - Add affiliate section in Terms and conditions page
                            MyActivity.startActivity(new Intent(MyActivity, Terms_And_Conditions.class));
                        }
                    });

                    builder.setIcon(R.mipmap.alert_green);
                    builder.setTitle(MyActivity.getResources().getString(R.string.TermsAndConditions));
                    builder.setPositiveButton((MyActivity.getResources().getString(R.string.ACCEPT)), new DialogInterface.OnClickListener() {
                        public void onClick(DialogInterface dialog, int whichButton) {

                            String username = "appAff." + userprofile.getLogin();
                            CreateAffiliateProfile(username);
                        }
                    });
                    builder.setNegativeButton(MyActivity.getResources().getString(R.string.CANCEL), null);
                    builder.setView(dialogView);
                    alertDialog = builder.create();
                    alertDialog.show();
                }else{
                    showShareLink(affiliatehiddentxt.getText().toString());
                }
            }
        });

        affiliateRefreshbtn =(MyButton) findViewById(R.id.affiliateRefreshbtn);
        affiliateRefreshbtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                RefreshAffiliate();
            }
        });

        RefreshAffiliate();

    }

    @Override
    public void onClick(View v)
    {
        if (v == Share_Twitter)
        {
            if(commun.isPackageInstalled("com.twitter.android", this.getPackageManager()))
                Share_Twitter();
            else ShowInstallAppMsg();
        }
        else if (v == Share_FaceBook)
        {
            Share_Facebook();
        }
        if (v == Back_Btn)
        {

            this.finish();
        }
    }

    private void ShowInstallAppMsg()
    {
        AlertDialog.Builder builder = new AlertDialog.Builder(MyActivity);
        AlertDialog alertDialog = null;

        builder.setIcon(R.mipmap.alert_green);
        builder.setTitle((MyActivity.getResources().getString(R.string.NOTE)));
        builder.setMessage((MyActivity.getResources().getString(R.string.ShouldDownloadApp)));
        builder.setPositiveButton((MyActivity.getResources().getString(R.string.OK)), null);
        alertDialog = builder.create();
        alertDialog.show();
    }


    private void Share_Twitter()
    {
        try
        {
            MultiDex.install(this);
            TwitterAuthConfig authConfig = new TwitterAuthConfig(
                    commun.Increment(commun.Reverse(commun.Decrement(TWITTER_KEY1))) +
                            commun.Reverse(TWITTER_KEY2) +
                            commun.Decrement(commun.Reverse(commun.Increment(TWITTER_KEY3))),
                    commun.Increment(commun.Reverse(commun.Decrement(TWITTER_SECRET1))) +
                            commun.Decrement(commun.Reverse(TWITTER_SECRET2)) +
                            commun.Reverse(TWITTER_SECRET3) +
                            commun.Decrement(commun.Reverse(TWITTER_SECRET4)) +
                            commun.Increment(commun.Reverse(commun.Decrement(TWITTER_SECRET5))));

            Fabric.with(this, new Twitter(authConfig));

            TweetComposer.Builder tweet = new TweetComposer.Builder(this);
            tweet.text(getString(R.string.TweetAffiliatetxt) + " ");
            tweet.url(new URL(affiliatehiddentxt.getText().toString()));
            tweet.image(commun.getUriToDrawable(this, R.drawable.social_media_shared_image_square));

            Intent intent =  tweet.createIntent();
            startActivityForResult(intent, TWITTER_REQUEST_CODE);
        }
        catch (Exception e)
        {
            commun.Log(e.toString());
            commun.DisplayToast(getString(R.string.Error), true);
        }
    }

    private void Share_Facebook()
    {
        ShareDialog shareDialog = new ShareDialog(this);

        callbackManager = CallbackManager.Factory.create();
        shareDialog.registerCallback(callbackManager, new
                FacebookCallback<Sharer.Result>() {
                    @Override
                    public void onSuccess(Sharer.Result result)
                    {
                        commun.HideKeyBoard();
                        commun.Log(result.getPostId());
                        if(result != null) {

                            //Toufic 3/1/2018 -- google analytics --
                            if(userprofile != null) MyApplication.getInstance().trackEvent(ShareAffiliateCategory, FbShareAction, userprofile.getId());
                            //Toufic 3/1/2018

                            commun.DisplayToast(getString(R.string.AffiliateShared), true);
                            type = "Share_Us_Facebook";
                        }
                    }

                    @Override
                    public void onCancel()
                    {
                        commun.HideKeyBoard();
                        commun.Log("Facebook_Cancel");
                    }

                    @Override
                    public void onError(FacebookException error)
                    {
                        commun.HideKeyBoard();
                        commun.Log("Facebook_Error");
                        ShowInstallAppMsg();
                    }
                }
        );

        if (ShareDialog.canShow(ShareLinkContent.class))
        {
            ShareContent linkContent = new ShareLinkContent.Builder()
                    .setContentUrl(Uri.parse(affiliatehiddentxt.getText().toString()))
                    .build();

            shareDialog.show(linkContent, ShareDialog.Mode.AUTOMATIC);
        }
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data)
    {
        commun.HideKeyBoard();

        if (requestCode == TWITTER_REQUEST_CODE)
        {
            if (resultCode == RESULT_OK)
            {
                //Toufic 3/1/2018 -- google analytics --
                if(userprofile != null) MyApplication.getInstance().trackEvent(ShareAffiliateCategory, TwitterShareAction, userprofile.getId());
                //Toufic 3/1/2018

                commun.DisplayToast(getString(R.string.AffiliateShared), true);
                type = "Share_Us_Twitter";
            }
        }
        if (callbackManager != null)
        {
            callbackManager.onActivityResult(requestCode, resultCode, data);
        }
    }

    public static boolean active = false;

    @Override
    public void onStart()
    {
        super.onStart();
        active = true;
    }

    @Override
    public void onDestroy()
    {
        super.onDestroy();
        active = false;
    }


    private void RefreshAffiliate(){
        try {
            setWaitScreen(true);
            affiliatebtn.setVisibility(View.GONE);
            affiliateimg.setVisibility(View.GONE);
            affiliateRefreshbtn.setVisibility(View.GONE);
            affiliatetxt.setText("");

            // call Affiliate user check API
            Thread thread = new Thread(new Runnable() {
                @Override
                public void run() {
                    try {

                        String username = "appAff." + userprofile.getId();
                        String email = userprofile.getLogin();

                        JSONObject obj = new JSONObject();
                        obj.put("username", username);
                        obj.put("email", email);

                        conn.GetData(AFFILIATEID, "1", "usercheck.php", "POST", obj.toString(), false, MyActivity);
                    } catch (Exception e) {
                        e.printStackTrace();
                        commun.Log(e.toString());
                        runOnUiThread(new Runnable() {
                            @Override
                            public void run() {
                                affiliatetxt.setText(MyActivity.getResources().getString(R.string.AffiliateError));
                                affiliateRefreshbtn.setVisibility(View.VISIBLE);
                            }
                        });
                    }
                }
            });
            thread.start();
        } catch (Exception e) {
            e.printStackTrace();
            affiliatetxt.setText(MyActivity.getResources().getString(R.string.AffiliateError));
            affiliateRefreshbtn.setVisibility(View.VISIBLE);
            commun.Log(e.toString());
        }

    }

    public void OnRefreshAffiliate(String result){
        try {

            setWaitScreen(false);
            commun.Log("result: " + result);

            if (result != null) {
                JSONObject j = new JSONObject(result);
                String status = j.getString("status");
                if (status.equals("Username already exists.") || status.equals("Email already exists.")) {
                    // if yes - show share button and AffiliateGenerated link
                    String link = j.getString("link");
                    showShareLink(link);
                } else if (status.equals("User not Exist.")) {

                    // if no - show create affiliate button and AffiliateText
                    affiliatetxt.setText(R.string.AffiliateText);
                    affiliatebtn.setVisibility(View.VISIBLE);
                    affiliateimg.setVisibility(View.VISIBLE);

                } else {
                    // error message AffiliateError
                    // show refresh button - that re-call RefreshAffiliate()
                    affiliatetxt.setText(status);
                    affiliateRefreshbtn.setVisibility(View.VISIBLE);
                }
            }else {
                showRefresh();
            }
        } catch (JSONException e) {
            showRefresh();
            e.printStackTrace();
        }
    }

    private void CreateAffiliateProfile(String username){
        try {
            affiliateimg.setVisibility(View.GONE);
            affiliatebtn.setVisibility(View.GONE);
            affiliateRefreshbtn.setVisibility(View.GONE);
            // generate a password for user
            // call Affiliate API with credentials
            AffiliateClass aff = new AffiliateClass();

            Thread thread = new Thread(new Runnable() {
                @Override
                public void run() {
                    try {

                        String username = "appAff." + userprofile.getId();
                        String email = userprofile.getLogin();

                        JSONObject obj = new JSONObject();
                        obj.put("username", username);
                        obj.put("email", email);

                        conn.GetData(AFFILIATEID, "2", "signup.php", "POST", obj.toString(), false, MyActivity);
                    } catch (Exception e) {
                        e.printStackTrace();
                        commun.Log(e.toString());
                        runOnUiThread(new Runnable() {
                            @Override
                            public void run() {
                                showRefresh();
                            }
                        });
                    }
                }
            });
            thread.start();

        }catch(Exception ex){
            ex.printStackTrace();
            showRefresh();
            commun.Log(ex.toString());
        }
    }

    public void OnCreateAffiliateProfile(String result){
        try {
            JSONObject j = new JSONObject(result);
            String status = j.getString("status");
            if (status.equals("User Created.")) {
                // show popup message that have credentials + share buttons
                String link = j.getString("link");
                String affiliateurl = j.getString("affiliateurl");
                String password = j.getString("password");
                String username = "appAff." + userprofile.getId();
                String email = userprofile.getLogin();
                affiliatetxt.setText(R.string.AffiliateText);
                affiliatetxt.setText(MyActivity.getResources().getString(R.string.AffiliateDescr) + "\n" + new URL(link));
                showPopupUserCredentials(link, username, email, password, affiliateurl);
            } else {
                affiliatetxt.setText(status);
                affiliateRefreshbtn.setVisibility(View.VISIBLE);
            }
        }catch (Exception ex){
            showRefresh();
            ex.printStackTrace();
            commun.Log(ex.toString());
        }
    }

    private void showShareLink(String link){
        commun.Log("Link: " + link);
        Text.setVisibility(View.VISIBLE);
        Affiliate.setVisibility(View.GONE);
        affiliatehiddentxt.setText(link);
        AffiliateDesc.setText(MyActivity.getResources().getString(R.string.AffiliateDescr) + "\n" + link);

        Text.setBackgroundResource(R.drawable.affiliatebg);
        Share_FaceBook.setVisibility(View.VISIBLE);
        Share_Twitter.setVisibility(View.VISIBLE);
        Share_FaceBook.setClickable(true);
        Share_Twitter.setClickable(true);
    }

    private void showRefresh(){
        affiliatetxt.setText(MyActivity.getResources().getString(R.string.AffiliateError));
        affiliateRefreshbtn.setVisibility(View.VISIBLE);
        affiliatebtn.setVisibility(View.GONE);
        affiliateimg.setVisibility(View.GONE);
    }

    private void showPopupUserCredentials(final String link, String username, String email, String password, String affiliateurl){
        try {
            String message =
                    MyActivity.getResources().getString(R.string.AffiliateAccount3) + "\n\n"
                            + MyActivity.getResources().getString(R.string.AffiliateAccount2) + ": " + new URL(affiliateurl) + "\n"
                            + MyActivity.getResources().getString(R.string.Username) + ": " + username + "\n"
                            + MyActivity.getResources().getString(R.string.Email) + ": " + email + "\n"
                            + MyActivity.getResources().getString(R.string.Password) + ": " + password + "\n"
                            + MyActivity.getResources().getString(R.string.AffiliateGenerated) + ": " + new URL(link) + "\n\n"
                            + MyActivity.getResources().getString(R.string.AffiliateAccount4);
            AlertDialog.Builder builder = new AlertDialog.Builder(MyActivity);
            AlertDialog alertDialog = null;

            TextView showText = new TextView(this);
            showText.setText(message);
            showText.setTextIsSelectable(true);
            showText.setTextColor(Color.BLACK);
            showText.setPadding(40,10,10,10);
            showText.setTextSize(15);
            builder.setView(showText);

            builder.setIcon(R.mipmap.alert_green);
            builder.setTitle((MyActivity.getResources().getString(R.string.AffiliateAccount1)));
            builder.setPositiveButton((MyActivity.getResources().getString(R.string.OK)), new DialogInterface.OnClickListener()
            {
                public void onClick(DialogInterface dialog, int whichButton)
                {
                    showShareLink(link);
                }
            });
            builder.setNegativeButton((MyActivity.getResources().getString(R.string.CANCEL)), null);
            alertDialog = builder.create();
            alertDialog.show();
        }catch (Exception ex){
            commun.Log(ex.getMessage());}
    }


    void setWaitScreen(boolean set)
    {
        Loading.setVisibility(set ? View.VISIBLE : View.GONE);
        Loading_Text.setVisibility(set ? View.VISIBLE : View.GONE);
    }


    private static Bitmap decodeSampledBitmapFromResource(Resources res, int resId, int reqWidth, int reqHeight) {
        // First decode with inJustDecodeBounds = true to check dimensions
        final BitmapFactory.Options options = new BitmapFactory.Options();
        options.inJustDecodeBounds = true;
        BitmapFactory.decodeResource(res, resId, options);

        // Calculate inSampleSize
        options.inSampleSize = calculateInSampleSize(options, reqWidth, reqHeight);

        // Decode bitmap with inSampleSize set
        options.inJustDecodeBounds = false;
        return BitmapFactory.decodeResource(res, resId, options);
    }

    private static int calculateInSampleSize(
            BitmapFactory.Options options, int reqWidth, int reqHeight) {

        // Raw height and width of image
        final int height = options.outHeight;
        final int width = options.outWidth;
        int inSampleSize = 1;

        if (height > reqHeight || width > reqWidth) {

            final int halfHeight = height / 2;
            final int halfWidth = width / 2;

            // Calculate the largest inSampleSize value that is a power of 2 and keeps both
            // height and width larger than the requested height and width.
            while ((halfHeight / inSampleSize) > reqHeight
                    && (halfWidth / inSampleSize) > reqWidth) {
                inSampleSize *= 2;
            }
        }

        return inSampleSize;
    }

}
