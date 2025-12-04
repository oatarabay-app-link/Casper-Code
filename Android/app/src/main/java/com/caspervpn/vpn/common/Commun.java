package com.caspervpn.vpn.common;

import android.app.Activity;
import android.app.ActivityManager;
import android.app.AlertDialog;
import android.content.ContentResolver;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.SharedPreferences.Editor;
import android.content.pm.PackageManager;
import android.content.res.Resources;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.net.Uri;
import android.preference.PreferenceManager;
//import androidx.annotation.AnyRes;
//import androidx.annotation.NonNull;
//import android.support.v4.os.ConfigurationCompat;
import androidx.core.os.ConfigurationCompat;
import androidx.annotation.AnyRes;
import androidx.annotation.NonNull;

import android.util.DisplayMetrics;
import android.util.Log;
import android.util.TypedValue;
import android.view.MotionEvent;
import android.view.View;
import android.view.ViewGroup;
import android.view.inputmethod.InputMethodManager;
import android.widget.ImageButton;
import android.widget.TextView;
import android.widget.Toast;

import com.caspervpn.vpn.R;
import com.caspervpn.vpn.classes.IKEV2;
import com.caspervpn.vpn.classes.L2TP;
import com.caspervpn.vpn.classes.OPEN_VPN;
import com.caspervpn.vpn.classes.Payment;
import com.caspervpn.vpn.classes.SSTP;
import com.caspervpn.vpn.classes.Server;
import com.caspervpn.vpn.classes.ServerComparator;
import com.caspervpn.vpn.classes.ServerInfo;
import com.caspervpn.vpn.classes.ServerParameter;
import com.caspervpn.vpn.classes.Server_Array;
import com.caspervpn.vpn.classes.Subscription;
import com.caspervpn.vpn.classes.SubscriptionData;
import com.caspervpn.vpn.classes.SusbcriptionDataList;
import com.caspervpn.vpn.classes.User;
import com.caspervpn.vpn.classes.UserProfile;
import com.caspervpn.vpn.helper.AsteriskPasswordTransformationMethod;
import com.caspervpn.vpn.helper.MyPasswordText;
import com.caspervpn.vpn.helper.MyTextView;
import com.caspervpn.vpn.screens.GettingStarted;
import com.caspervpn.vpn.screens.Landing_Page;
import com.caspervpn.vpn.services.MyVpnService;
import com.crashlytics.android.Crashlytics;
import com.google.gson.Gson;

//import org.apache.http.NameValuePair;
//import org.apache.http.client.utils.URLEncodedUtils;
import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.client.utils.URLEncodedUtils;

import org.json.JSONArray;
import org.json.JSONObject;

import java.net.URI;
import java.net.URISyntaxException;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Collections;
import java.util.Date;
import java.util.HashMap;
import java.util.List;
import java.util.Locale;
import java.util.Map;
import java.util.TimeZone;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import de.blinkt.openvpn.core.VpnStatus;
import io.intercom.android.sdk.Intercom;
import io.intercom.android.sdk.UserAttributes;
import io.intercom.android.sdk.identity.Registration;

import static com.caspervpn.vpn.common.Configuration.IsDebugMode;
import static com.caspervpn.vpn.common.Configuration.OPEN_SETTINGS;
import static com.caspervpn.vpn.common.Configuration.SelectedServer;
import static com.caspervpn.vpn.common.Configuration.payment;
import static com.caspervpn.vpn.common.Configuration.servers;
import static com.caspervpn.vpn.common.Configuration.user;
import static com.caspervpn.vpn.common.Configuration.userprofile;

public class Commun
{
    Activity MyActivity;
    Context MyContext;

    public Commun(Activity activity)
    {
        MyActivity = activity;
        MyContext = MyActivity.getBaseContext();
    }

    public Commun(Context context)
    {
        MyContext = context;
    }

    public void HideKeyBoard()
    {
        // Check if no view has focus:
        View view = MyActivity.getCurrentFocus();
        if (view != null)
        {
            InputMethodManager imm = (InputMethodManager) MyActivity.getSystemService(Context.INPUT_METHOD_SERVICE);
            imm.hideSoftInputFromWindow(view.getWindowToken(), 0);
        }
    }

    // Added
    public String getLocalCountry(){
        Locale locale = ConfigurationCompat.getLocales(Resources.getSystem().getConfiguration()).get(0);
        Log("country language: " + locale.getDisplayName());
        return locale.getLanguage(); // locale.getDisplayName()
    }

    protected Boolean IsActivityRunning(Class activityClass)
    {
        ActivityManager activityManager = (ActivityManager) MyContext.getSystemService(Context.ACTIVITY_SERVICE);
        List<ActivityManager.RunningTaskInfo> tasks = activityManager.getRunningTasks(Integer.MAX_VALUE);

        for (ActivityManager.RunningTaskInfo task : tasks)
        {
            if (activityClass.getCanonicalName().equalsIgnoreCase(task.baseActivity.getClassName()))
                return true;
        }

        return false;
    }


    public boolean VerityEmail (String email)
    {
        Pattern p = Pattern.compile(".+@.+\\.[a-z]+");
        Matcher m = p.matcher(email);
        return m.matches();
    }

    public boolean VerityPassword (String password)
    {
        return password.length() >=0;
    }

    public void DisplayToast(String msg, boolean IsLong)
    {
        Toast.makeText(MyActivity, msg, IsLong ? Toast.LENGTH_LONG :  Toast.LENGTH_SHORT).show();
    }

    public void DisplayToast(String msg)
    {
        Toast.makeText(MyActivity, msg, Toast.LENGTH_SHORT).show();
    }

    public void DisplayToast(double msg)
    {
        Toast.makeText(MyActivity, String.valueOf(msg), Toast.LENGTH_SHORT).show();
    }

    public void DisplayToast(boolean msg)
    {
        Toast.makeText(MyActivity, String.valueOf(msg), Toast.LENGTH_SHORT).show();
    }

    public void GetScreenSize()
    {
        if ((MyContext.getResources().getConfiguration().screenLayout & android.content.res.Configuration.SCREENLAYOUT_SIZE_MASK) == android.content.res.Configuration.SCREENLAYOUT_SIZE_LARGE)
            this.Log("Large screen");
        else if ((MyContext.getResources().getConfiguration().screenLayout & android.content.res.Configuration.SCREENLAYOUT_SIZE_MASK) == android.content.res.Configuration.SCREENLAYOUT_SIZE_NORMAL)
            this.Log("Normal sized screen");
        else if ((MyContext.getResources().getConfiguration().screenLayout & android.content.res.Configuration.SCREENLAYOUT_SIZE_MASK) == android.content.res.Configuration.SCREENLAYOUT_SIZE_SMALL)
            this.Log("Small sized screen");
        else
            this.Log("Screen size is neither large, normal or small");
    }

    public boolean isNetworkConnected()
    {
        ConnectivityManager connectivityManager = (ConnectivityManager) MyActivity.getSystemService(Context.CONNECTIVITY_SERVICE);

        if (connectivityManager.getActiveNetworkInfo() != null)
        {
            NetworkInfo activeNetworkInfo = connectivityManager.getActiveNetworkInfo();
            return activeNetworkInfo != null && activeNetworkInfo.isConnected();
        }
        else return false;
    }

    public void ShowConnectionDialog ()
    {
        MyActivity.runOnUiThread(new Runnable()
        {
             public void run() {
                 AlertDialog.Builder builder = new AlertDialog.Builder(MyActivity);
                 AlertDialog alertDialog = null;

                 builder.setIcon(R.mipmap.alert_green);
                 builder.setTitle((MyActivity.getResources().getString(R.string.NoInternetConnection)));
                 builder.setMessage((MyActivity.getResources().getString(R.string.PleaseCheckYourInternetConnectivity)));
                 builder.setPositiveButton((MyActivity.getResources().getString(R.string.OK)), new DialogInterface.OnClickListener() {
                     public void onClick(DialogInterface dialog, int whichButton) {
                         Intent intent = new Intent(android.provider.Settings.ACTION_WIRELESS_SETTINGS);
                         MyActivity.startActivityForResult(intent, OPEN_SETTINGS);

                         View Loading = MyActivity.findViewById(R.id.loading);
                         if (Loading != null) Loading.setVisibility(View.GONE);

                         View Display_Message_Frame = MyActivity.findViewById(R.id.Display_Message_Frame);
                         if (Display_Message_Frame != null) Display_Message_Frame.setVisibility(View.VISIBLE);

                         TextView Error = (MyTextView) MyActivity.findViewById(R.id.Display_Message);
                         if (Error != null) Error.setText((MyActivity.getResources().getString(R.string.Error)));
                     }
                 });
                 builder.setNegativeButton((MyActivity.getResources().getString(R.string.Cancel)), new DialogInterface.OnClickListener() {
                     public void onClick(DialogInterface dialog, int whichButton) {
                         View Loading = MyActivity.findViewById(R.id.loading);
                         if (Loading != null) Loading.setVisibility(View.GONE);

                         View Display_Message_Frame = MyActivity.findViewById(R.id.Display_Message_Frame);
                         if (Display_Message_Frame != null) Display_Message_Frame.setVisibility(View.VISIBLE);

                         TextView Error = (MyTextView) MyActivity.findViewById(R.id.Display_Message);
                         if (Error != null) Error.setText((MyActivity.getResources().getString(R.string.Error)));
                     }
                 });
                 alertDialog = builder.create();

                 alertDialog.show();
             }
         });
    }


    public float dipToPixels(float dipValue)
    {
        return TypedValue.applyDimension(TypedValue.COMPLEX_UNIT_DIP,  dipValue, MyContext.getResources().getDisplayMetrics());
    }

    public void SaveClassToPreference(Object Object, String ObjectName)
    {
        SharedPreferences mPrefs = PreferenceManager.getDefaultSharedPreferences(MyContext);
        Editor prefsEditor = mPrefs.edit();
        Gson gson = new Gson();
        String json = gson.toJson(Object);
        prefsEditor.putString(ObjectName, json);
        prefsEditor.commit();
    }


    public String LoadClassFromPreference(String ObjectName)
    {
        SharedPreferences mPrefs = PreferenceManager.getDefaultSharedPreferences(MyContext);
        String json = mPrefs.getString(ObjectName, "");
        return json;
    }

    public static final Uri getUriToDrawable(@NonNull Context context, @AnyRes int drawableId)
    {
        Uri imageUri = Uri.parse(ContentResolver.SCHEME_ANDROID_RESOURCE + "://" + context.getResources().getResourcePackageName(drawableId) + '/' + context.getResources().getResourceTypeName(drawableId) + '/' + context.getResources().getResourceEntryName(drawableId) );
        return imageUri;
    }

    public int getStatusBarHeight()
    {
        int result = 0;
        int resourceId = MyActivity.getResources().getIdentifier("status_bar_height", "dimen", "android");
        if (resourceId > 0) result = MyActivity.getResources().getDimensionPixelSize(resourceId);

        return result;
    }

    //region Log
    public void Log(double message)
    {
        Log("" + message);
    }

    public void Log(boolean message)
    {
        Log("" + message);
    }

    public void Log(String message)
    {
        Log.e("CasperVPN", "" + message);
        if (!IsDebugMode) Crashlytics.log(0, "CasperVPN", message);
    }

    public void LogOpenVPN(String message)
    {
        Log.e("OpenVPN", "" + message);
        if (!IsDebugMode) Crashlytics.log(0, "OpenVPN", message);
    }
    //endregion

    private void RegisterForCrashlytics()
    {
        Crashlytics.setUserIdentifier(user.getUserid());
        Crashlytics.setUserEmail(user.getEmail());
    }

    public void RegisterForIntercom()
    {
        Intercom.client().reset();
        Intercom.client().registerIdentifiedUser(Registration.create().withUserId(user.getUserid()).withEmail(user.getEmail()));
        //Toufic 7/4/2017 -- Intercom --
        //Intercom.client().setSecureMode(user.getIntHash(), user.getUserid());
        Intercom.client().setUserHash(user.getIntHash());
        //Toufic 7/4/2017 -- Intercom --
    }


    public void RegisterForIntercomGuest()
    {
        Intercom.client().reset();
        Intercom.client().registerUnidentifiedUser();
    }

    public void StartCasperVPNApplication()
    {
        Log("VPN Application Started");

        SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(MyActivity);
//        if (!prefs.getBoolean("Islogin", false))
//        {
//            DataConnection conn = new DataConnection();
//            conn.GetData(0, "0", "http://caspervpn.com/receiveemail.php?email=" + user.getEmail(), "GET", null, false, MyContext);
//        }
        prefs.edit().putBoolean("Islogin", true).commit();

        Load_Data();

        RegisterForIntercom();

        if (!Configuration.IsDebugMode) RegisterForCrashlytics();

        if (!IsServiceRunning(MyVpnService.class))
        {
            MyActivity.startService(new Intent(MyActivity, MyVpnService.class));
        }

        MyActivity.startActivity(new Intent(MyActivity, Landing_Page.class));

        if (prefs.getBoolean("FirstTimeLoaded", true))
        {
            Log("First Time Loaded");

            prefs.edit().putBoolean("FirstTimeLoaded", false).commit();

            prefs.edit().putBoolean("FeelSecure", true).commit();
            prefs.edit().putBoolean("TrackingProtection", true).commit();
            prefs.edit().putBoolean("AutoConnect", false).commit();             //  auto connect = connect on boot = false per default
            prefs.edit().putBoolean("ConnectOnWifi", false).commit();           //  connect on wifi = false per default
        }

        if (prefs.getBoolean("ShowEarthHint", true) || Configuration.AlwaysShowInstructions)
        {
            MyActivity.startActivity(new Intent(MyActivity, GettingStarted.class));
            prefs.edit().putBoolean("ShowEarthHint", false).commit();

            try { // 3/23
                updateIntercomAttributeString("Signed Up App Version",  MyActivity.getPackageManager().getPackageInfo(MyActivity.getPackageName(), 0).versionName);
            } catch (PackageManager.NameNotFoundException e) {
                Log(e.getMessage());
            }
        }

        MyActivity.finish();
    }

    public void Load_Data()
    {
        user = new Gson().fromJson(LoadClassFromPreference("user"), User.class);
        servers = new Gson().fromJson(LoadClassFromPreference("ServerList"), Server_Array.class);
        userprofile = new Gson().fromJson(LoadClassFromPreference("UserProfile"), UserProfile.class);
    }

    public boolean IsServiceRunning(Class<?> serviceClass)
    {
        ActivityManager manager = (ActivityManager) MyActivity.getSystemService(Context.ACTIVITY_SERVICE);
        for (ActivityManager.RunningServiceInfo service : manager.getRunningServices(Integer.MAX_VALUE))
        {
            if (serviceClass.getName().equals(service.service.getClassName()))
            {
                return true;
            }
        }
        return false;
    }

    public String Reverse(String string)
    {
        return new StringBuilder(string).reverse().toString();
    }

    public String Increment(String string)
    {
        StringBuffer b = new StringBuffer();
        char[] chars = string.toCharArray();

        for (char c : chars)
        {
            b.append((char) (c + 1));
        }

        return b.toString();
    }

    public String Decrement(String string)
    {
        StringBuffer b = new StringBuffer();
        char[] chars = string.toCharArray();

        for (char c : chars)
        {
            b.append((char) (c - 1));
        }

        return b.toString();
    }

    public void ChangeLanguage(String language)
    {
        SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(MyActivity);
        Locale myLocale = new Locale(language);

        Resources res = MyActivity.getResources();
        DisplayMetrics dm = res.getDisplayMetrics();
        android.content.res.Configuration conf = res.getConfiguration();
        conf.locale = myLocale;
        res.updateConfiguration(conf, dm);

        android.content.res.Configuration configuration = MyActivity.getResources().getConfiguration();
        configuration.setLayoutDirection(new Locale(language));
        MyActivity.getResources().updateConfiguration(configuration,  MyActivity.getResources().getDisplayMetrics());

        prefs.edit().putString("language", language).commit();
    }

    public ArrayList<Server> SaveServer(JSONArray serverList) throws Exception
    {
        ArrayList<Server> ServerList = new ArrayList<>();


        for (int i = 0; i < serverList.length(); i++)
        {
            try
            {
                JSONObject ServerItem = serverList.getJSONObject(i);

                String ServerId = ServerItem.getString("serverId");
                String ServerName = ServerItem.getString("serverName");
                String ServerIP = ServerItem.getString("serverIp");
                double ServerLongitude = ServerItem.getDouble("serverLongitude");
                double ServerLatitude = ServerItem.getDouble("serverLatitude");
                long CreateDate = ServerItem.getLong("createDate");
                String Country = ServerItem.getString("country");
                String ConnectionData = ServerItem.getString("connectionData");
                Boolean Disabled = ServerItem.getBoolean("disabled");


                JSONArray protocolTypes = ServerItem.getJSONArray("protocolTypes");

                ArrayList<String> ProtocolTypes = new ArrayList<String>();
                for (int j = 0; j < protocolTypes.length(); j++) {
                    ProtocolTypes.add(protocolTypes.get(j).toString());
                }

                JSONObject ServerParameters = ServerItem.getJSONObject("parameters");

                JSONObject IKEV2_Parameters = (JSONObject) ServerParameters.get("IKEV2");
                String IKEV2_remoteId = IKEV2_Parameters.getString("remoteId");

                JSONObject L2TP_Parameters = (JSONObject) ServerParameters.get("L2TP");
                String L2TP_secret = L2TP_Parameters.getString("secret");


                JSONObject OPEN_VPN_Parameters = (JSONObject) ServerParameters.get("OPEN_VPN");
                String OPEN_VPN_cert = OPEN_VPN_Parameters.getString("cert");
                String OPEN_VPN_conf = OPEN_VPN_Parameters.getString("conf");


                JSONObject SSTP_Parameters = (JSONObject) ServerParameters.get("SSTP");
                String SSTP_cert = SSTP_Parameters.getString("cert");

                IKEV2 IKEV2_param = new IKEV2(IKEV2_remoteId);
                L2TP L2TP_param = new L2TP(L2TP_secret);
                OPEN_VPN OPEN_VPN_param = new OPEN_VPN(OPEN_VPN_conf, OPEN_VPN_cert);
                SSTP SSTP_param = new SSTP(SSTP_cert);


                ServerParameter ServerParam = new ServerParameter(IKEV2_param, L2TP_param, OPEN_VPN_param, SSTP_param);

                JSONObject SystemInfo = ServerItem.getJSONObject("systemInfo");
                String Info_serverId = SystemInfo.getString("serverId");
                double helathPercent = SystemInfo.getDouble("healthPercent");


                String UpTime = "0 day, 0:00:00.00";
                double CPU_Load = 0, RAM_ALL = 0, RAM_USED = 0, HDD_All = 0, HDD_Used = 0, net_All = 0, net_Used = 0;
                try
                {
                    if (SystemInfo.getJSONObject("serverParams")!=null){
                    JSONObject SystemInfoDetails = SystemInfo.getJSONObject("serverParams");

                    UpTime = SystemInfoDetails.getString("uptime");
                    CPU_Load = SystemInfoDetails.getDouble("cpuLoad");
                    RAM_ALL = SystemInfoDetails.getDouble("ramAll");
                    RAM_USED = SystemInfoDetails.getDouble("ramUsed");
                    HDD_All = SystemInfoDetails.getDouble("hddAll");
                    HDD_Used = SystemInfoDetails.getDouble("hddUsed");
                    net_All = SystemInfoDetails.getDouble("netAll");
                    net_Used = SystemInfoDetails.getDouble("netUsed");
                   }else
                       {

                           UpTime = "100";
                           CPU_Load = 10;
                           RAM_ALL = 5000;
                           RAM_USED = 2;
                           HDD_All =0;
                           HDD_Used = 0;
                           net_All = 100000;
                           net_Used = 20;
                    }
                }
                catch (Exception e) {Log(e.getMessage());}
                ServerInfo info = new ServerInfo(Info_serverId, UpTime, helathPercent, CPU_Load, RAM_ALL, RAM_USED, HDD_All, HDD_Used, net_All, net_Used);

                Server server = new Server(ServerName, ServerIP, ServerId, Country, ServerParam, info, ConnectionData, ServerLongitude, ServerLatitude, Disabled, CreateDate, ProtocolTypes);

                if (ProtocolTypes.contains("OPEN_VPN")){  /* toufic sleiman 5-8-2017   display just Open vpn servers */
                    ServerList.add(server);
                }
            }
            catch (Exception e) {Log(e.getMessage());}
        }

        Collections.sort(ServerList, new ServerComparator());

        servers = new Server_Array(ServerList);
        SaveClassToPreference(servers, "ServerList");

        return ServerList;
    }

    public UserProfile SaveUserProfile(JSONObject userdata) throws Exception
    {
        String UserID = userdata.getString("id");
        String Login = userdata.getString("login");
        String Phone = userdata.getString("phone");
        Boolean Blocked = userdata.getBoolean("blocked");
        long CreateTime = userdata.getLong("createTime");
        String FirstName = userdata.getString("firstName");
        String LastName = userdata.getString("lastName");
        String Description = userdata.getString("description");
        String UserRoleType = userdata.getString("userRoleType");
        String Role = userdata.getString("role");



        JSONObject SubscriptionDataObject = userdata.getJSONObject("subscriptionData");
        String SubscriptionId = SubscriptionDataObject.getString("subscriptionId");
        String SubscriptionName = SubscriptionDataObject.getString("subscriptionName");

        double MonthlyPrice = SubscriptionDataObject.getDouble("monthlyPrice");
        double periodPrice = SubscriptionDataObject.getDouble("periodPrice");
        int periodLength = SubscriptionDataObject.getInt("periodLength");

        int TrafficSize = SubscriptionDataObject.getInt("trafficSize");
        int RateLimit = SubscriptionDataObject.getInt("rateLimit");
        int MaxConnections = SubscriptionDataObject.getInt("maxConnections");

        String NumServers = SubscriptionDataObject.getString("numServers");
        String NumCountries = SubscriptionDataObject.getString("numCountries");

        Boolean AvailableForAndroid = SubscriptionDataObject.getBoolean("availableForAndroid");
        Boolean AvailableForIos = SubscriptionDataObject.getBoolean("availableForIos");
        long SubscriptionCreateTime = SubscriptionDataObject.getLong("createTime");


        JSONArray protocolTypes = SubscriptionDataObject.getJSONArray("protocols");
        ArrayList<String> ProtocolTypes = new ArrayList<String>();
        for (int j = 0; j < protocolTypes.length(); j++)
        {
            ProtocolTypes.add(protocolTypes.get(j).toString());
        }



        JSONObject SubscriptionObject = userdata.getJSONObject("subscription");
        String subscriptionId = SubscriptionObject.getString("subscriptionId");
        long subscriptionStartDate = SubscriptionObject.getLong("subscriptionStartDate");
        long subscriptionEndDate = SubscriptionObject.getLong("subscriptionEndDate");
        String vpnPassword = SubscriptionObject.getString("vpnPassword");


        Subscription subscription = new Subscription(subscriptionId, vpnPassword, subscriptionStartDate, subscriptionEndDate);

        SubscriptionData subscriptiondata = new SubscriptionData(SubscriptionId, SubscriptionName, MonthlyPrice, periodPrice, periodLength, TrafficSize, RateLimit, MaxConnections, NumServers, NumCountries, AvailableForAndroid, AvailableForIos, SubscriptionCreateTime, ProtocolTypes);
        userprofile = new UserProfile(UserID, Login, Phone, FirstName, LastName, Description, UserRoleType , Role, subscriptiondata, subscription, Blocked, CreateTime,0,0);
        SaveClassToPreference(userprofile, "UserProfile");

        Log("#### userprofile saved now ####");
        return userprofile;
    }

    public String GetExpiryDateString()
    {
        if (userprofile == null)
            return MyActivity.getResources().getString(R.string.TimeoutTitle);

        if(userprofile.getSubscription().getSubscriptionId().equals("12c3fc2a-4915-43c3-a992-388b38aa02e3")){
            return MyActivity.getResources().getString(R.string.Lifetime);
        }
        // toufic sleiman
        Date date = new Date(userprofile.getSubscription().getSubscriptionEndDate());
        Date currentTime = Calendar.getInstance().getTime();

        if (date.before(currentTime)) return MyActivity.getString(R.string.ExpiredIn);
        return MyActivity.getString(R.string.Validtill)+ " " + translateDate(date);
        // toufic sleiman

    }

    public int GetRemainingDays()
    {
        try {
            Date date = new Date(userprofile.getSubscription().getSubscriptionEndDate());
            Date currentTime = Calendar.getInstance().getTime();

            if (date.before(currentTime)) return 0;

            long diff = date.getTime() - currentTime.getTime();
            long remainingDays = diff / (24 * 60 * 60 * 1000);
            return (int) remainingDays;

        } catch (Exception e) {
            Log(e.getMessage());
            e.printStackTrace();
            return 0;
        }
    }

    // toufic sleiman
    public String translateDate(Date date){
        SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(MyActivity);
        String Language = prefs.getString("language", "");
        Locale locale = new Locale(Language);
        SimpleDateFormat sdf = new SimpleDateFormat("d-MM-yy, hh:mm",locale);
        String format = sdf.format(date);

        return format;
    }
    // toufic sleiman

    public int getPixelsFromDp(int dp)
    {
        final float scale = MyActivity.getResources().getDisplayMetrics().density;
        return (int)(dp * scale + 0.5f);
    }

    public double getRandom(int Min, int Max)
    {
        return Min + (int)(Math.random() * ((Max - Min) + 1));
    }

    public Map<String,String> readParamsIntoMap(String url) throws URISyntaxException
    {
        Map<String, String> params = new HashMap<>();
        List<NameValuePair> result = URLEncodedUtils.parse(new URI(url), "UTF-8");
        for (NameValuePair nvp : result) params.put(nvp.getName(), nvp.getValue());
        return params;
    }

    //region DistanceCalculator
    public static String CalculateDistance(double lat1, double lon1, double lat2, double lon2)
    {
        double theta = lon1 - lon2;
        double dist = Math.sin(deg2rad(lat1)) * Math.sin(deg2rad(lat2)) + Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * Math.cos(deg2rad(theta));
        dist = Math.acos(dist);
        dist = rad2deg(dist);
        dist = dist * 60 * 1.1515 * 1.609344; //Kilometers

        return (String.valueOf(Math.round(dist)));
    }

    private static double deg2rad(double deg)
    {
        return (deg * Math.PI / 180.0);
    }

    private static double rad2deg(double rad)
    {
        return (rad * 180 / Math.PI);
    }
    //endregion

    public boolean isRTL()
    {
        android.content.res.Configuration configuration = MyContext.getResources().getConfiguration();
        int directionality = configuration.getLayoutDirection();

        return directionality == Character.DIRECTIONALITY_RIGHT_TO_LEFT ||
                directionality == Character.DIRECTIONALITY_RIGHT_TO_LEFT_ARABIC;
    }

    public String GetConnectedServerName()
    {
        return !SelectedServer.getCountry().equals("null") ? SelectedServer.getCountry() : SelectedServer.getServerName();
    }

    public boolean IsActive()
    {
        if (Configuration.AlwaysInactiveSubscription)
        {
            return false;
        }
        else
        {
            if (userprofile == null)
                return false;

            if(userprofile.getSubscription().getSubscriptionId().equals("12c3fc2a-4915-43c3-a992-388b38aa02e3")){
                return true;
            }
            Calendar currentdate = Calendar.getInstance();
            DateFormat formatter = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
            TimeZone obj = TimeZone.getTimeZone("IST");
            formatter.setTimeZone(obj);
            currentdate.setTimeZone(obj);
            System.out.println("Local:: " +currentdate.getTime());
            System.out.println("Ireland:: "+ formatter.format(currentdate.getTime()));

            Date date = new Date(userprofile.getSubscription().getSubscriptionEndDate());
            Boolean IsActive = date.after(currentdate.getTime());
            Log("Is Subscription Active: " + IsActive);
            return IsActive;
        }
    }

    public View.OnTouchListener ShowPassword(final MyPasswordText Password, final ImageButton ShowPassword)
    {
        return new View.OnTouchListener()
        {
            @Override
            public boolean onTouch(View v, MotionEvent event)
            {
                switch ( event.getAction() )
                {
                    case MotionEvent.ACTION_DOWN:
                        Password.setTransformationMethod(null);
                        Password.setSelection(Password.getText().length());
                        ShowPassword.setImageDrawable(MyContext.getResources().getDrawable(R.mipmap.show_password));
                        break;
                    case MotionEvent.ACTION_UP:
                        Password.setTransformationMethod(new AsteriskPasswordTransformationMethod());
                        Password.setSelection(Password.getText().length());
                        ShowPassword.setImageDrawable(MyContext.getResources().getDrawable(R.mipmap.show_password_disabled));
                        break;
                }
                return true;
            }
        };
    }

    public void StartFlashing(final View v, final boolean DefaultInVisible)
    {
        v.postDelayed(new Runnable(){
            @Override
            public void run()
            {
                v.setVisibility(View.INVISIBLE);
                v.postDelayed(new Runnable(){
                    @Override
                    public void run()
                    {
                        v.setVisibility(View.VISIBLE);
                        v.postDelayed(new Runnable(){
                            @Override
                            public void run()
                            {
                                v.setVisibility(View.INVISIBLE);
                                v.postDelayed(new Runnable(){
                                    @Override
                                    public void run()
                                    {
                                        v.setVisibility(View.VISIBLE);
                                        v.postDelayed(new Runnable(){
                                            @Override
                                            public void run()
                                            {
                                                v.setVisibility(View.INVISIBLE);
                                                v.postDelayed(new Runnable(){
                                                    @Override
                                                    public void run()
                                                    {
                                                        v.setVisibility(View.VISIBLE);
                                                        if (DefaultInVisible) if (!VpnStatus.isVPNActive()) v.setVisibility(View.INVISIBLE);
                                                    }
                                                }, 100);
                                            }
                                        }, 100);
                                    }
                                }, 100);
                            }
                        }, 100);
                    }
                }, 100);
            }
        }, 100);
    }

    public void ZoomViews(ViewGroup view, int font_scale)
    {
        for (int i = 0; i < view.getChildCount(); i++)
        {
            View v = view.getChildAt(i);

            if (v instanceof TextView)
            {
                float TextSize = ((TextView) v).getTextSize();
                if (font_scale < 0 && TextSize < 35) continue;
                if (font_scale > 0 && TextSize > 60) continue;

                ((TextView) v).setTextSize(TypedValue.COMPLEX_UNIT_PX, TextSize + font_scale);
            }
            else if (v instanceof ViewGroup)
            {
                this.ZoomViews((ViewGroup) v, font_scale);
            }
        }
    }


    /* Toufic sleiman subscription start */

    public Payment SavePaymentURL(String subscriptionId, JSONObject userdata) throws Exception
    {
        String code = userdata.getString("code");
        String checkoutUrl = userdata.getString("checkoutUrl");

        payment = new Payment(subscriptionId, checkoutUrl, code);

        SaveClassToPreference(payment, "PaymentURL");

        return payment;
    }

    /* Toufic sleiman subscription start */



    /* Toufic sleiman subscription start */

    public Payment SaveSubscriptions(JSONArray userdata) throws Exception
    {
        ArrayList<SubscriptionData> listsubscriptiondata = new ArrayList<SubscriptionData>();


        for (int i = 0; i < userdata.length(); i++) {
            try {
                JSONObject SubscriptionItem = userdata.getJSONObject(i);

                String SubscriptionId = SubscriptionItem.getString("subscriptionId");
                String SubscriptionName = SubscriptionItem.getString("subscriptionName");
                double MonthlyPrice = SubscriptionItem.getDouble("monthlyPrice");
                double periodPrice = SubscriptionItem.getDouble("periodPrice");
                int periodLength = SubscriptionItem.getInt("periodLength");
                long TrafficSize = SubscriptionItem.getLong("trafficSize");
                double RateLimit = SubscriptionItem.getDouble("rateLimit");
                int MaxConnections = SubscriptionItem.getInt("maxConnections");
                String NumServers = SubscriptionItem.getString("numServers");
                String NumCountries = SubscriptionItem.getString("numCountries");
                boolean AvailableForAndroid = SubscriptionItem.getBoolean("availableForAndroid");
                boolean AvailableForIos = SubscriptionItem.getBoolean("availableForIos");
                long SubscriptionCreateTime = SubscriptionItem.getLong("createTime");

                JSONArray Protocols = SubscriptionItem.getJSONArray("protocols");

                ArrayList<String> ProtocolTypes = new ArrayList<String>();
                for (int j = 0; j < Protocols.length(); j++) {
                    ProtocolTypes.add(Protocols.get(j).toString());
                }

                SubscriptionData subscriptiondata = new SubscriptionData(SubscriptionId, SubscriptionName, MonthlyPrice, periodPrice, periodLength, TrafficSize, RateLimit, MaxConnections, NumServers, NumCountries, AvailableForAndroid, AvailableForIos, SubscriptionCreateTime, ProtocolTypes);

                listsubscriptiondata.add(subscriptiondata);
            } catch (Exception e) {
                Log(e.getMessage());
                this.Log("aaa");
            }
        }

        SusbcriptionDataList susbcriptionDataList = new SusbcriptionDataList(listsubscriptiondata);

        SaveClassToPreference(susbcriptionDataList, "SubscriptionDataList");

        return payment;
    }

    /* Toufic sleiman subscription start */


    // Attributes region

    public void updateIntercomAttributeBoolean(String key, boolean value){
        UserAttributes userAttributes = new UserAttributes.Builder()
                .withName(user.getUsername())
                .withEmail(user.getEmail())
                .withCustomAttribute(key, value)
                .build();
        Intercom.client().updateUser(userAttributes);
    }

    public void updateIntercomAttributeString(String key, String value){
        UserAttributes userAttributes = new UserAttributes.Builder()
                .withName(user.getUsername())
                .withEmail(user.getEmail())
                .withCustomAttribute(key, value)
                .build();
        Intercom.client().updateUser(userAttributes);
    }
    //end region


    public boolean isPackageInstalled(String packagename, PackageManager packageManager) {
        try {
            packageManager.getPackageInfo(packagename, 0);
            return true;
        } catch (PackageManager.NameNotFoundException e) {
            Log(e.getMessage());
            return false;
        }
    }

}
