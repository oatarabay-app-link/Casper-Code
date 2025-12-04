package com.caspervpn.vpn.screens;

import android.app.Activity;
import android.content.ActivityNotFoundException;
import android.content.Intent;
import android.content.SharedPreferences;
import android.net.Uri;
import android.os.Bundle;
import android.preference.PreferenceManager;
import androidx.fragment.app.Fragment;
import androidx.appcompat.widget.SwitchCompat;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.CompoundButton;
import android.widget.LinearLayout;

import com.caspervpn.vpn.R;
import com.caspervpn.vpn.Subscriptions.Screens.Subscriptions;
import com.caspervpn.vpn.common.Commun;
import com.caspervpn.vpn.helper.MyApplication;
import com.caspervpn.vpn.services.MyVpnService;

import de.blinkt.openvpn.core.VpnStatus;
import io.intercom.android.sdk.Intercom;

import static com.caspervpn.vpn.common.Configuration.MainMenuScreenName;
import static com.caspervpn.vpn.common.Configuration.MyVpnServiceInstance;
import static com.caspervpn.vpn.common.Configuration.userprofile;


public class Main_Menu extends Fragment implements View.OnClickListener, SwitchCompat.OnCheckedChangeListener
{
    Activity MyActivity;
    Commun commun;

    LinearLayout About, Subscribe, GetFreePremium, Language, ContactUs, Logout, RateUs, Guide, ShareUS, Affiliate;
    SwitchCompat TrackingProtectionSwitch, FeelSecureSwitch, AutoConnectSwitch, ConnectOnWifiSwitch;
    SharedPreferences prefs;


    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState)
    {
        View view = inflater.inflate(R.layout.main_menu, container, false);

        MyActivity = getActivity();
        commun = new Commun(MyActivity);
        prefs = PreferenceManager.getDefaultSharedPreferences(MyActivity);

        Init(view);

        return view;
    }

    private void Init(View v)
    {
        About = (LinearLayout) v.findViewById(R.id.About);
        Subscribe = (LinearLayout) v.findViewById(R.id.Subscribe);
        //GetFreePremium = (LinearLayout) v.findViewById(R.id.GetFreePremium);
        Language = (LinearLayout) v.findViewById(R.id.Language);
        ContactUs = (LinearLayout) v.findViewById(R.id.ContactUs);
        Logout = (LinearLayout) v.findViewById(R.id.logout);
        RateUs = (LinearLayout) v.findViewById(R.id.RateUs);
        Guide = (LinearLayout) v.findViewById(R.id.Guide);
        ShareUS = (LinearLayout) v.findViewById(R.id.ShareUS);
        Affiliate = (LinearLayout) v.findViewById(R.id.Affiliate);

        TrackingProtectionSwitch = (SwitchCompat) v.findViewById(R.id.TrackingProtectionSwitch);
        FeelSecureSwitch = (SwitchCompat) v.findViewById(R.id.FeelSecureSwitch);
        AutoConnectSwitch = (SwitchCompat) v.findViewById(R.id.AutoConnectSwitch);
        ConnectOnWifiSwitch = (SwitchCompat) v.findViewById(R.id.ConnectonWifiSwitch);

        FeelSecureSwitch.setChecked(prefs.getBoolean("FeelSecure", true));
        TrackingProtectionSwitch.setChecked(prefs.getBoolean("TrackingProtection", true));
        AutoConnectSwitch.setChecked(prefs.getBoolean("AutoConnect", true));
        ConnectOnWifiSwitch.setChecked(prefs.getBoolean("ConnectOnWifi", true));

        TrackingProtectionSwitch.setOnCheckedChangeListener(this);
        FeelSecureSwitch.setOnCheckedChangeListener(this);
        AutoConnectSwitch.setOnCheckedChangeListener(this);
        ConnectOnWifiSwitch.setOnCheckedChangeListener(this);

        About.setOnClickListener(this);
        Subscribe.setOnClickListener(this);
//        GetFreePremium.setOnClickListener(this);
        Language.setOnClickListener(this);
        ContactUs.setOnClickListener(this);
        RateUs.setOnClickListener(this);
        Logout.setOnClickListener(this);
        Guide.setOnClickListener(this);
        ShareUS.setOnClickListener(this);
        Affiliate.setOnClickListener(this);

        //Toufic 3/1/2018 -- google analytics --
        MyApplication.getInstance().trackScreenView(MainMenuScreenName);
        //Toufic 3/1/2018
    }

    @Override
    public void onClick(View v)
    {
        if (v == About)
        {
            ((Landing_Page)MyActivity).About_Clicked();
        }
        else if (v == Subscribe)
        {
//            Disbling Old Below Code
//            Intent myIntent = new Intent(MyActivity, Subscribe.class);
//            MyActivity.startActivity(myIntent);

            Intent myIntent = new Intent(MyActivity, Subscriptions.class);
            MyActivity.startActivity(myIntent);


        }
//        else if (v == GetFreePremium)
//        {
//            Intent myIntent = new Intent(MyActivity, Subscribe.class);
//            MyActivity.startActivity(myIntent);
//        }
        else if (v == Language)
        {
            ((Landing_Page)MyActivity).Language_Clicked();
        }
        else if (v == ContactUs)
        {
            ((Landing_Page)MyActivity).Contactus_Clicked();
        }
        else if (v == Logout)
        {
            prefs.edit().putBoolean("Islogin", false).commit();
            commun.SaveClassToPreference(null, "user");
            commun.SaveClassToPreference(null, "ServerList");
            commun.SaveClassToPreference(null, "UserProfile");

            if (MyVpnServiceInstance != null && VpnStatus.isVPNActive())
            {
                MyVpnServiceInstance.Clear();
            }

            if (commun.IsServiceRunning(MyVpnService.class))  MyActivity.stopService(new Intent(MyActivity, MyVpnService.class));

            Intercom.client().reset();
            commun.RegisterForIntercomGuest();

            Intent myIntent = new Intent(MyActivity, Login.class);
            MyActivity.startActivity(myIntent);
            MyActivity.finish();
        }
        else if (v == RateUs)
        {
            Uri uri = Uri.parse("market://details?id=" + MyActivity.getPackageName());
            Intent goToMarket = new Intent(Intent.ACTION_VIEW, uri);
            goToMarket.addFlags(Intent.FLAG_ACTIVITY_NO_HISTORY | Intent.FLAG_ACTIVITY_NEW_DOCUMENT | Intent.FLAG_ACTIVITY_MULTIPLE_TASK);
            try
            {
                startActivity(goToMarket);
            }
            catch (ActivityNotFoundException e)
            {
                startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse("http://play.google.com/store/apps/details?id=" + MyActivity.getPackageName())));
            }
        }
        else if(v == Guide)
        {
            startActivity(new Intent(MyActivity, GettingStarted.class));
        }
        else if(v == ShareUS)
        {
            startActivity(new Intent(MyActivity, Social_Media.class));
        }
        else if(v == Affiliate)
        {
            startActivity(new Intent(MyActivity, Share_link.class));
        }
    }

    @Override
    public void onCheckedChanged(CompoundButton buttonView, boolean isChecked)
    {
        if (buttonView == FeelSecureSwitch)
        {
            prefs.edit().putBoolean("FeelSecure", isChecked).commit();
        }
        else if (buttonView == TrackingProtectionSwitch)
        {
            prefs.edit().putBoolean("TrackingProtection", isChecked).commit();
        }
        else if (buttonView == AutoConnectSwitch)
        {
            prefs.edit().putBoolean("AutoConnect", isChecked).commit();
        }
        else if (buttonView == ConnectOnWifiSwitch)
        {

            prefs.edit().putBoolean("ConnectOnWifi", isChecked).commit();
        }
    }
}
