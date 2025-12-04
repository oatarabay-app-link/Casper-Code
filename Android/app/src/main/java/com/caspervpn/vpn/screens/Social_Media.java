package com.caspervpn.vpn.screens;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.net.Uri;
import android.os.Bundle;
import androidx.multidex.MultiDex;
import androidx.appcompat.app.AppCompatActivity;
import android.view.View;
import android.widget.ImageButton;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;
import com.caspervpn.vpn.R;
import com.caspervpn.vpn.common.Commun;
import com.caspervpn.vpn.common.Configuration;
import com.caspervpn.vpn.common.DataConnection;
import com.caspervpn.vpn.helper.MyApplication;
import com.caspervpn.vpn.helper.MyTextView;
import com.facebook.CallbackManager;
import com.facebook.FacebookCallback;
import com.facebook.FacebookException;
import com.facebook.share.Sharer;
import com.facebook.share.model.ShareLinkContent;
import com.facebook.share.model.SharePhoto;
import com.facebook.share.model.SharePhotoContent;
import com.facebook.share.widget.ShareDialog;
import com.google.android.gms.plus.PlusOneButton;
import com.google.android.gms.plus.PlusShare;
import com.twitter.sdk.android.Twitter;
import com.twitter.sdk.android.core.TwitterAuthConfig;
import com.twitter.sdk.android.tweetcomposer.TweetComposer;

import java.net.URL;

import io.fabric.sdk.android.Fabric;

import static com.caspervpn.vpn.common.Configuration.FbShareAction;
import static com.caspervpn.vpn.common.Configuration.SocialMediaCategory;
import static com.caspervpn.vpn.common.Configuration.GooglePlusShareAction;
import static com.caspervpn.vpn.common.Configuration.SocialMediaScreenName;
import static com.caspervpn.vpn.common.Configuration.TWITTER_KEY1;
import static com.caspervpn.vpn.common.Configuration.TWITTER_KEY2;
import static com.caspervpn.vpn.common.Configuration.TWITTER_KEY3;
import static com.caspervpn.vpn.common.Configuration.TWITTER_SECRET1;
import static com.caspervpn.vpn.common.Configuration.TWITTER_SECRET2;
import static com.caspervpn.vpn.common.Configuration.TWITTER_SECRET3;
import static com.caspervpn.vpn.common.Configuration.TWITTER_SECRET4;
import static com.caspervpn.vpn.common.Configuration.TWITTER_SECRET5;
import static com.caspervpn.vpn.common.Configuration.TwitterShareAction;
import static com.caspervpn.vpn.common.Configuration.WebsiteURL;
import static com.caspervpn.vpn.common.Configuration.userprofile;


/**
 * Created by zaherZ on 1/28/2017.
 */

public class Social_Media extends AppCompatActivity implements View.OnClickListener
{
    ImageButton Back_Btn;
    LinearLayout Share_Twitter, Share_FaceBook, Share_GooglePlus;
    PlusOneButton PlusOne_Button;
    Commun commun;
    Activity MyActivity;
    private MyTextView Loading_Text;
    RelativeLayout Loading;
    String type;

    CallbackManager callbackManager;

    public static final int TWITTER_REQUEST_CODE = 100;
    public static final int GOOGLE_REQUEST_CODE = 102;
    public static final int GOOGLE_PLUS_ONE_CODE = 104;

    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.social_media);
        MyActivity = this;
        commun = new Commun(this);

        Init();

        //Toufic 3/1/2018 -- google analytics --
        MyApplication.getInstance().trackScreenView(SocialMediaScreenName);

        //Toufic 3/1/2018
    }
    @Override
    protected void onResume()
    {
        super.onResume();
        PlusOne_Button.initialize("http://play.google.com/store/apps/details?id=" + MyActivity.getPackageName(), GOOGLE_PLUS_ONE_CODE);
    }

    private void Init()
    {
        TextView ApplicationTitle = (TextView) findViewById(R.id.ApplicationTitle);
        ApplicationTitle.setText(getString(R.string.SocialMedia).toUpperCase());

        Back_Btn = (ImageButton) findViewById(R.id.back);
        Share_Twitter = (LinearLayout) findViewById(R.id.Share_Twitter);
        Share_FaceBook = (LinearLayout) findViewById(R.id.Share_FaceBook);
        PlusOne_Button = (PlusOneButton) findViewById(R.id.plus_one_button);

        Loading = (RelativeLayout) findViewById(R.id.loading);
        Loading_Text = (MyTextView) findViewById(R.id.Loading_Text);
        Loading_Text.setText(getString(R.string.Loading));

        Share_Twitter.setOnClickListener(this);
        Share_FaceBook.setOnClickListener(this);
        Back_Btn.setOnClickListener(this);

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
        else if (v == Share_GooglePlus)
        {
            Share_GooglePlus();
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

    private void Share_GooglePlus()
    {
        Intent shareIntent = new PlusShare.Builder(this)
                .setType("text/plain")
                .setText(getString(R.string.TryCasperVPNForFree))
                .setContentUrl(Uri.parse(Configuration.GooglePlus_Url))
                .getIntent();

        startActivityForResult(shareIntent, GOOGLE_REQUEST_CODE);
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
            tweet.text(getString(R.string.TryCasperVPNForFree));
            tweet.url(new URL(WebsiteURL));
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
                                    if(userprofile != null) MyApplication.getInstance().trackEvent(SocialMediaCategory, FbShareAction, userprofile.getId());
                                    //Toufic 3/1/2018

                                    commun.DisplayToast(getString(R.string.ThankYou), true);
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
            Bitmap image = BitmapFactory.decodeResource(getResources(), R.drawable.social_media_shared_image);
            SharePhoto photo = new SharePhoto.Builder().setBitmap(image).build();
            SharePhotoContent.Builder PhotoContent = new SharePhotoContent.Builder();
            PhotoContent.addPhoto(photo);
            shareDialog.show(PhotoContent.build(), ShareDialog.Mode.AUTOMATIC);
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
                if(userprofile != null) MyApplication.getInstance().trackEvent(SocialMediaCategory, TwitterShareAction, userprofile.getId());
                //Toufic 3/1/2018

                commun.DisplayToast(getString(R.string.ThankYou), true);
                type = "Share_Us_Twitter";
            }
        }
        if (requestCode ==  GOOGLE_REQUEST_CODE)
        {
            if (resultCode == RESULT_OK)
            {
                //Toufic 3/1/2018 -- google analytics --
                if(userprofile != null) MyApplication.getInstance().trackEvent(SocialMediaCategory, GooglePlusShareAction, userprofile.getId());
                //Toufic 3/1/2018

                commun.DisplayToast(getString(R.string.ThankYou), true);
                type = "Share_Us_Google";
            }
        }
        if (requestCode == GOOGLE_PLUS_ONE_CODE)
        {
            Share_GooglePlus();
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

}