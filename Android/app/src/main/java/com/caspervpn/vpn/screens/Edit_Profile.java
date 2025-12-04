package com.caspervpn.vpn.screens;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import androidx.fragment.app.FragmentActivity;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.ImageButton;
import android.widget.ImageView;
import android.widget.RelativeLayout;

import com.caspervpn.vpn.R;
import com.caspervpn.vpn.common.Commun;
import com.caspervpn.vpn.helper.MyApplication;
import com.caspervpn.vpn.helper.MyButton;
import com.caspervpn.vpn.helper.MyEditText;
import com.caspervpn.vpn.helper.MyTextView;

import java.util.Date;

import io.intercom.android.sdk.Intercom;

import static com.caspervpn.vpn.R.id.back;
import static com.caspervpn.vpn.common.Configuration.Edit_Profile_Page;
import static com.caspervpn.vpn.common.Configuration.SubscriptionsCategory;
import static com.caspervpn.vpn.common.Configuration.UnSubscribedAction;
import static com.caspervpn.vpn.common.Configuration.user;
import static com.caspervpn.vpn.common.Configuration.userprofile;

public class Edit_Profile extends FragmentActivity implements OnClickListener
{
    MyTextView Loading_Text;
    MyEditText Email, SubscriptionType, SubscriptionStatus;
    ImageButton Back_Btn;
    ImageView Edit_Profile, Edit_Subscription;
    RelativeLayout Loading;
    MyButton Unsubscribe_Upgrade;

    Commun commun;
    Activity MyActivity;

    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.edit_profile);
        commun = new Commun (this);
        MyActivity = this;

        Init();
    }

    private void Init()
    {
        Email = (MyEditText) findViewById(R.id.User_Name);

        SubscriptionType = (MyEditText) findViewById(R.id.SubscriptionType);
        SubscriptionStatus = (MyEditText) findViewById(R.id.SubscriptionStatus);

        Back_Btn = (ImageButton) findViewById(back);
        Edit_Profile = (ImageView) findViewById(R.id.edit_password);
        Edit_Subscription = (ImageView) findViewById(R.id.edit_subscription);
        Unsubscribe_Upgrade = (MyButton) findViewById(R.id.Unsubscribe_Upgrade);

        Loading = (RelativeLayout) findViewById(R.id.loading);
        Loading_Text = (MyTextView) findViewById(R.id.Loading_Text);

        if(user != null) Email.setText(user.getEmail());
        //toufic sleiman 5-10-17
        if(userprofile != null) {
            if(userprofile.getSubscription().getSubscriptionId().equals("12c3fc2a-4915-43c3-a992-388b38aa02e3"))
                SubscriptionType.setText(MyActivity.getResources().getString(R.string.Lifetime));
            else {
                commun.Log("Expiry Date: " +  userprofile.getSubscription().getSubscriptionEndDate());
                Date date = new Date(userprofile.getSubscription().getSubscriptionEndDate());
                SubscriptionType.setText(commun.translateDate(date));
            }
        }else
            showReloginMessage();

        //toufic sleiman 5-10-17

        if (commun.IsActive())
        {
            SubscriptionStatus.setText(getString(R.string.Active));
            Unsubscribe_Upgrade.setText(getString(R.string.UNSUBSCRIBE));
        }
        else
        {
            SubscriptionStatus.setText(getString(R.string.Expired));
            Unsubscribe_Upgrade.setText(getString(R.string.SUBSCRIBE));
        }

        Back_Btn.setOnClickListener(this);
        Edit_Profile.setOnClickListener(this);
        Edit_Subscription.setOnClickListener(this);
        Unsubscribe_Upgrade.setOnClickListener(this);

        //Toufic 3/1/2018 -- google analytics --
        MyApplication.getInstance().trackScreenView(Edit_Profile_Page);
        //Toufic 3/1/2018
    }


    @Override
    public void onClick(View v)
    {
        if (v == Back_Btn)
        {
            finish();
        }
        else if (v == Edit_Profile)
        {
            this.startActivity(new Intent(this, Change_Password.class));
        }
        else if (v == Edit_Subscription)
        {
            this.startActivity(new Intent(this, Subscribe.class));
        }
        else if (v == Unsubscribe_Upgrade)
        {
            if (commun.IsActive()){
                ShowUnsubscribeDialog();
            }else{
                this.startActivity(new Intent(this, Subscribe.class));
            }
        }
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

    private void Unsubsribe()
    {
        //Toufic 3/1/2018 -- google analytics --
        if(userprofile != null) MyApplication.getInstance().trackEvent(SubscriptionsCategory, UnSubscribedAction, userprofile.getId());
        //Toufic 3/1/2018

        startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse("https://play.google.com/store/account/subscriptions")));
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
