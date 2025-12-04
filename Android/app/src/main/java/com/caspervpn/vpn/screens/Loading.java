package com.caspervpn.vpn.screens;

import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.preference.PreferenceManager;
import androidx.fragment.app.FragmentActivity;
import android.view.View;

import com.caspervpn.vpn.R;
import com.caspervpn.vpn.classes.Server_Array;
import com.caspervpn.vpn.classes.User;
import com.caspervpn.vpn.classes.UserProfile;
import com.caspervpn.vpn.common.Commun;
import com.caspervpn.vpn.common.Configuration;
import com.caspervpn.vpn.common.DataConnection;
import com.caspervpn.vpn.services.Check_Recurring_Subscription;
import com.google.gson.Gson;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.Locale;

import static com.caspervpn.vpn.common.Configuration.SHEETSU;
import static com.caspervpn.vpn.common.Configuration.servers;
import static com.caspervpn.vpn.common.Configuration.user;
import static com.caspervpn.vpn.common.Configuration.userprofile;


public class Loading extends FragmentActivity
{
    Commun commun;
    SharedPreferences prefs;
    private DataConnection conn;
    private Activity MyActivity;

    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        MyActivity = this;

        conn = new DataConnection(this);
        commun = new Commun(this);
        prefs = PreferenceManager.getDefaultSharedPreferences(this);

        commun.Log("Application Opened");

        //region Set Default Device Language
        String language = prefs.getString("language", null);
        if (language == null)
        {
            language = Locale.getDefault().getLanguage();
            if (!language.equals("ar") && !language.equals("de") &&
                    !language.equals("en") && !language.equals("es") &&
                    !language.equals("fa") && !language.equals("fr") &&
                    !language.equals("hi") && !language.equals("ru") &&
                    !language.equals("tr") && !language.equals("zh")) language = "en";
        }
        commun.ChangeLanguage(language);
        //endregion

//        checkCountry();

        boolean Islogin = prefs.getBoolean("Islogin", false);
        if (Islogin)
        {
            //region Load Stored Classes from Preference
            user = new Gson().fromJson(commun.LoadClassFromPreference("user"), User.class);
            servers = new Gson().fromJson(commun.LoadClassFromPreference("ServerList"), Server_Array.class);
            userprofile = new Gson().fromJson(commun.LoadClassFromPreference("UserProfile"), UserProfile.class);
            //endregion

            startService(new Intent(this, Check_Recurring_Subscription.class));
            commun.StartCasperVPNApplication();
        }
        else
        {
            setContentView(R.layout.loading);

            new Thread()
            {
                @Override
                public void run()
                {
                    try
                    {
                        synchronized (this)
                        {
                            wait(Configuration.LOADING_WAITING_TIME);
                        }
                    }
                    catch (InterruptedException e)
                    {
                        e.printStackTrace();
                    }
                    finally
                    {
                        commun.RegisterForIntercomGuest();
                        //If application is launched for the first time => launch Sign Up class
                        //else  if application already launched => launch Log In class
                        Intent intent = new Intent(getApplication().getBaseContext(), prefs.getBoolean("FirstTimeLoaded", false) ? Signup.class : Login.class);
                        startActivity(intent);
                        finish();
                    }
                }
            }.start();
        }
    }

    private void checkCountry()
    {
        try {
            String fixcountryurl = prefs.getString("fixcountryurl", null);
            commun.SaveClassToPreference(null, "fixcountryurl");
            // Added
            String country = commun.getLocalCountry();
            commun.Log("country id = " + country);
            if (country.equals("en")) // +971
                GetCountryURL();
        }catch (Exception ex){
            ex.printStackTrace();
            commun.Log(ex.toString());
        }
    }



    private void GetCountryURL()
    {
        Thread thread = new Thread(new Runnable()
        {
            @Override
            public void run()
            {
                try
                {
                    if (!commun.isNetworkConnected()) {
                        commun.Log("Bad internet connection ");
                        return;
                    }

                    conn.GetData(SHEETSU, "1", "sheetsu", "GET", null, false, MyActivity);
                }
                catch (Exception e)
                {
                    commun.Log(e.toString());
                    e.printStackTrace();
                }
            }
        });
        thread.start();
    }

    public void OnGetCountryURLResult(String result)
    {
        if (result == null)
        {
            commun.Log("OnGetCountryURLResult is NULL");
        }
        else
        {
            try
            {
                JSONArray objs = new JSONArray(result);

                for (int i = 0; i < objs.length(); i++)
                {
                    JSONObject obj = objs.getJSONObject(i);
                    if(obj.getString("name").equals("uae")){
                        commun.SaveClassToPreference(obj.getString("url"), "fixcountryurl");
                        commun.Log("connection: " + obj.getString("url"));
                        return;
                    }
                }

            }
            catch (Exception e)
            {
                commun.Log(e.toString());
                e.printStackTrace();
            }
        }
    }
}
