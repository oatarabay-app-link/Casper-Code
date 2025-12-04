package com.caspervpn.vpn.screens;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.content.ActivityNotFoundException;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.net.Uri;
import android.net.VpnService;
import android.os.Build;
import android.os.Bundle;
import android.preference.PreferenceManager;
import com.google.android.material.navigation.NavigationView;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentTransaction;
import androidx.core.view.GravityCompat;
import androidx.drawerlayout.widget.DrawerLayout;
import androidx.appcompat.app.ActionBarDrawerToggle;
import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import androidx.appcompat.widget.Toolbar;

import android.view.View;
import android.widget.Button;
import android.widget.ImageButton;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;

import com.caspervpn.vpn.R;
import com.caspervpn.vpn.Subscriptions.Screens.Subscriptions;
import com.caspervpn.vpn.classes.Server;
import com.caspervpn.vpn.classes.Server_Array;
import com.caspervpn.vpn.classes.User;
import com.caspervpn.vpn.classes.UserProfile;
import com.caspervpn.vpn.common.Commun;
import com.caspervpn.vpn.common.DataConnection;
import com.caspervpn.vpn.common.SubscriptionClass;
import com.caspervpn.vpn.helper.ApplicationRater;
import com.caspervpn.vpn.helper.MyApplication;
import com.caspervpn.vpn.helper.MyButton;
import com.caspervpn.vpn.helper.MyTextView;
import com.caspervpn.vpn.services.MyVpnService;
import com.google.gson.Gson;
import com.pollfish.interfaces.PollfishClosedListener;
import com.pollfish.interfaces.PollfishOpenedListener;
import com.pollfish.interfaces.PollfishSurveyCompletedListener;
import com.pollfish.interfaces.PollfishSurveyNotAvailableListener;
import com.pollfish.interfaces.PollfishSurveyReceivedListener;
import com.pollfish.interfaces.PollfishUserNotEligibleListener;
import com.pollfish.main.PollFish;

import org.json.JSONObject;

import java.util.Calendar;
import java.util.Random; //Added By M7
import de.blinkt.openvpn.core.ConnectionStatus;
import de.blinkt.openvpn.core.LogItem;
import de.blinkt.openvpn.core.OpenVPNManagement;
import de.blinkt.openvpn.core.VpnStatus;
import io.intercom.android.sdk.Intercom;

import static com.caspervpn.vpn.common.Configuration.ACCESS_LOCATION_REQUEST;
import static com.caspervpn.vpn.common.Configuration.ConnectedCategory;
import static com.caspervpn.vpn.common.Configuration.DefaultMapView;
import static com.caspervpn.vpn.common.Configuration.LOGIN_CLASS_ID;
import static com.caspervpn.vpn.common.Configuration.LandingPageInstance;
import static com.caspervpn.vpn.common.Configuration.MyVpnServiceInstance;
import static com.caspervpn.vpn.common.Configuration.POLLFISH;
import static com.caspervpn.vpn.common.Configuration.SERVICE_CLASS_ID;
import static com.caspervpn.vpn.common.Configuration.SelectedServer;
import static com.caspervpn.vpn.common.Configuration.VPN_PERMISSION_REQUEST;
import static com.caspervpn.vpn.common.Configuration.pollfishCustomMode;
import static com.caspervpn.vpn.common.Configuration.pollfishRelease;
import static com.caspervpn.vpn.common.Configuration.servers;
import static com.caspervpn.vpn.common.Configuration.user;
import static com.caspervpn.vpn.common.Configuration.userprofile;
import static de.blinkt.openvpn.core.OpenVPNService.humanReadableByteCount;

public class Landing_Page extends AppCompatActivity implements View.OnClickListener, VpnStatus.ByteCountListener, VpnStatus.LogListener
        ,PollfishSurveyCompletedListener,
        PollfishClosedListener, PollfishSurveyReceivedListener,
        PollfishSurveyNotAvailableListener, PollfishUserNotEligibleListener, PollfishOpenedListener
{
    //region Fields
    private static Commun commun;
    private Activity MyActivity;
    private Boolean DrawerFirstTimeBug = true, SwitchViewEnabled = true, ConnectAfterPermissionRequestNotNull = null;

    private LinearLayout Menu_Container;
    private ImageButton Back_Btn, Edit_profile;
    private DrawerLayout drawer;
    private Toolbar toolbar;
    private ActionBarDrawerToggle toggle;
    private ImageButton Switch_View, Server_List;
    private RecyclerView RecyclerView;
    private NavigationView NavigationView;
    private LinearLayoutManager LinearLayoutManager;
    private MyTextView ApplicationVersion, Username;
    private static MyTextView RemainingDays;
    private AlertDialog SubscriptionDialog;
    private DataConnection conn;
    private MyButton TakeASurvey;

    private Map_View Map_View_Fragment = new Map_View();
    private Earth_View Earth_View_Fragment = new Earth_View();
    private Main_Menu Main_Menu_Fragment = new Main_Menu();
    private About_Menu About_Menu_Fragment = new About_Menu();
    private Contact_Menu Contact_Menu_Fragment = new Contact_Menu();
    private Language_Menu Language_Menu_Fragment = new Language_Menu();

    private String EARTH_VIEW = "EARTH_VIEW";
    private String MAP_VIEW = "MAP_VIEW";
    private String MAIN_MENU = "MAIN_MENU";
    private String ABOUT_MENU = "ABOUT_MENU";
    private String CONTACT_MENU = "CONTACT_MENU";
    private String LANGUAGE_MENU = "LANGUAGE_MENU";


    // Pollfish
    RelativeLayout Loading;
    private MyTextView Loading_Text;
    private Dialog SurveyAvailable;
    private Button okbtn, dialogButtonCancel, Updatebtn, dialogButtonCancelNV;
    private Dialog NewVersionDialog;
    // Pollfish

    //endregion

    //region OnCreate
    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        MyActivity = this;
        commun = new Commun(this);
        LandingPageInstance = this;
        commun.Log("Landing Page onCreate");

        user = new Gson().fromJson(commun.LoadClassFromPreference("user"), User.class);
        servers = new Gson().fromJson(commun.LoadClassFromPreference("ServerList"), Server_Array.class);
        userprofile = new Gson().fromJson(commun.LoadClassFromPreference("UserProfile"), UserProfile.class);

        conn = new DataConnection(this);
        setContentView(R.layout.landing_page);

        Init();

        //Intercom Push Messages
        Intercom.client().handlePushMessage();

        //Ask for Rating the App
        ApplicationRater.app_launched(this);

        try {
            //Ask for VPN permission
            Intent intent = VpnService.prepare(this);
            if (intent != null) startActivityForResult(intent, VPN_PERMISSION_REQUEST);
        }catch (Exception ex){
            ex.printStackTrace();
            commun.Log(ex.toString());
        }

        CallPollfish(); // Pollfish
        InitNewVersionCheck();
    }
    //endregion

    //region OnPause and OnResume
    @Override
    protected void onResume()
    {
        commun.Log("Landing Page onResume");

        super.onResume();

        CallNewVersionCheck();

        if (!commun.IsServiceRunning(MyVpnService.class))
        {
            MyActivity.startService(new Intent(MyActivity, MyVpnService.class));
        }

        VpnStatus.addByteCountListener(this);
        VpnStatus.addLogListener(this);

        SelectedServer = new Gson().fromJson( commun.LoadClassFromPreference("SelectedServer"), Server.class);

        if (SelectedServer == null) commun.Log("Landing Page onResume - Selected Server NULL");

        if (userprofile != null) // toufic
        {
            RemainingDays.setText(commun.GetExpiryDateString());
            commun.Log("### GetExpiryDateString Refreshed - OnResume ###");
        }
        else
            showReloginMessage();
    }

    @Override
    public void onPause()
    {
        commun.Log("Landing Page onPause");

        super.onPause();

        VpnStatus.removeByteCountListener(this);
        VpnStatus.removeLogListener(this);

        commun.SaveClassToPreference(SelectedServer, "SelectedServer");
        if (SelectedServer == null) commun.Log("Landing Page onPause - Selected Server NULL");
    }
    //endregion

    //region Menu
    @Override
    public void onBackPressed()
    {
        if (drawer.isDrawerOpen(GravityCompat.START))
        {
            Adjust_Menu();
        }
        else super.onBackPressed();
    }


    private void Adjust_Menu()
    {
        Fragment myFragment = getSupportFragmentManager().findFragmentByTag(MAIN_MENU);
        if (myFragment != null && myFragment.isVisible())
        {
            drawer.closeDrawer(GravityCompat.START);
        }
        else
        {
            FragmentTransaction ft = getSupportFragmentManager().beginTransaction();
            ft.replace(R.id.menu_fragment, Main_Menu_Fragment, MAIN_MENU).commit();
        }

        LinearLayoutManager.scrollToPositionWithOffset(0, 0);
    }
    //endregion

    //region Menu action
    public void About_Clicked()
    {
        FragmentTransaction ft = getSupportFragmentManager().beginTransaction();
        ft.replace(R.id.menu_fragment, About_Menu_Fragment, ABOUT_MENU).commit();
    }

    public void Contactus_Clicked()
    {
        FragmentTransaction ft = getSupportFragmentManager().beginTransaction();
        ft.replace(R.id.menu_fragment, Contact_Menu_Fragment, CONTACT_MENU).commit();
    }

    public void Language_Clicked()
    {
        FragmentTransaction ft = getSupportFragmentManager().beginTransaction();
        ft.replace(R.id.menu_fragment, Language_Menu_Fragment, LANGUAGE_MENU).commit();
    }
    //endregion

    //region Init
    @Override
    protected void onSaveInstanceState(Bundle outState)
    {
        //Do not call for super(). Fragment Transaction Bug on API Level > 11.
    }

    private void Init() {
        //region Application Version
        ApplicationVersion = (MyTextView) findViewById(R.id.ApplicationVersion);
        try {
            ApplicationVersion.setText("v. " + MyActivity.getPackageManager().getPackageInfo(getPackageName(), 0).versionName);
        } catch (Exception e) {
        }
        //endregion

        //region Subscription Expired
        AlertDialog.Builder SubscriptionDialogBuilder = new AlertDialog.Builder(MyActivity);
        SubscriptionDialogBuilder.setCancelable(false);
        SubscriptionDialogBuilder.setIcon(R.mipmap.alert_green);
        SubscriptionDialogBuilder.setTitle(MyActivity.getResources().getString(R.string.SubscriptionExpired));
        SubscriptionDialogBuilder.setPositiveButton((MyActivity.getResources().getString(R.string.ACCEPT)), new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int whichButton) {
                Intent myIntent = new Intent(MyActivity, Subscriptions.class);
                MyActivity.startActivity(myIntent);
            }
        });
        SubscriptionDialogBuilder.setNegativeButton(MyActivity.getResources().getString(R.string.FreebeeCenter), new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int whichButton) {
                Intent myIntent = new Intent(MyActivity, Subscriptions.class);
                MyActivity.startActivity(myIntent);
            }
        });
        SubscriptionDialogBuilder.setMessage(MyActivity.getResources().getString(R.string.SubscriptionExpiredText));
        SubscriptionDialog = SubscriptionDialogBuilder.create();
        //endregion

        //region Landing View
        if (DefaultMapView)
        {
            getSupportFragmentManager().beginTransaction().replace(R.id.fragment, Map_View_Fragment, MAP_VIEW).commit();
        }
        else
        {
            getSupportFragmentManager().beginTransaction().replace(R.id.fragment, Earth_View_Fragment, EARTH_VIEW).commit();
        }
        //endregion

        //region Drawer
        toolbar = (Toolbar) findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
        getSupportActionBar().setDisplayShowTitleEnabled(false);

        drawer = (DrawerLayout) findViewById(R.id.drawer_layout);
        drawer.setDrawerListener(new DrawerLayout.DrawerListener()
        {
            @Override public void onDrawerSlide(View view, float v) {}
            @Override public void onDrawerOpened(View view) {}
            @Override public void onDrawerClosed(View view) {Adjust_Menu();}
            @Override public void onDrawerStateChanged(int i)
            {
                if (DrawerFirstTimeBug)
                {
                    getSupportFragmentManager().beginTransaction().replace(R.id.menu_fragment, Main_Menu_Fragment, MAIN_MENU).commit();
                    DrawerFirstTimeBug = false;
                }
            }
        });

        toggle = new ActionBarDrawerToggle(this, drawer, toolbar, R.string.navigation_drawer_open, R.string.navigation_drawer_close);
        toggle.syncState();
        //endregion

        //region Menu
        NavigationView = (NavigationView) findViewById(R.id.nav_view);

        RecyclerView = (RecyclerView) NavigationView.getChildAt(0);
        LinearLayoutManager = (LinearLayoutManager) RecyclerView.getLayoutManager();

        Menu_Container = (LinearLayout) NavigationView.getHeaderView(0);
        Back_Btn = (ImageButton) Menu_Container.findViewById(R.id.back);
        Edit_profile = (ImageButton) Menu_Container.findViewById(R.id.edit_user);
        Username = (MyTextView) Menu_Container.findViewById(R.id.Username);
        RemainingDays = (MyTextView) Menu_Container.findViewById(R.id.RemainingDays);

        TakeASurvey = (MyButton) findViewById(R.id.TakeASurvey);
        TakeASurvey.setVisibility(View.INVISIBLE);
        TakeASurvey.setOnClickListener(this);

        if(userprofile == null)
            showReloginMessage();
        else{
            Username.setText(userprofile.getLogin());
            RemainingDays.setText(commun.GetExpiryDateString());
        }

        //region Status Bar height bug
        if (Build.VERSION.SDK_INT > Build.VERSION_CODES.KITKAT)
            Menu_Container.setPadding(0,commun.getStatusBarHeight(),0,0);
        //endregion
        //endregion

        Switch_View = (ImageButton) findViewById(R.id.switch_view);
        Server_List = (ImageButton) findViewById(R.id.server_list);

        Switch_View.setOnClickListener(this);
        Server_List.setOnClickListener(this);
        Back_Btn.setOnClickListener(this);
        Edit_profile.setOnClickListener(this);
    }
    //endregion

    //region Switch View

    private void Switch_View()
    {
        if (!SwitchViewEnabled) return;

        SwitchViewEnabled = false;
        Switch_View.postDelayed(new Runnable()
        {
            @Override
            public void run()
            {
                SwitchViewEnabled = true;
            }
        }, 300);

        Fragment myFragment = getSupportFragmentManager().findFragmentByTag(EARTH_VIEW);
        if (myFragment != null && myFragment.isVisible())
        {
            FragmentTransaction ft = getSupportFragmentManager().beginTransaction();
            ft.setCustomAnimations(R.anim.enter_from_right, R.anim.exit_to_left);
            ft.replace(R.id.fragment, Map_View_Fragment, MAP_VIEW).commit();

            Switch_View.setImageResource(R.drawable.earth_button);

            commun.Log("Map View Visible");
        }
        else
        {
            FragmentTransaction ft = getSupportFragmentManager().beginTransaction();
            ft.setCustomAnimations(R.anim.enter_from_left, R.anim.exit_to_right);
            ft.replace(R.id.fragment, Earth_View_Fragment, EARTH_VIEW).commit();

            Switch_View.setImageResource(R.drawable.map_button);

            commun.Log("Earth View Visible");
        }
    }

    private boolean IsEarthViewVisible()
    {
        Fragment myFragment = getSupportFragmentManager().findFragmentByTag(EARTH_VIEW);
        return (myFragment != null && myFragment.getUserVisibleHint());
    }

    private boolean IsMapViewVisible()
    {
        Fragment myFragment = getSupportFragmentManager().findFragmentByTag(MAP_VIEW);
        return (myFragment != null && myFragment.getUserVisibleHint());
    }
    //endregion

    //region OnClick
    @Override
    public void onClick(View v)
    {
        if (v == Back_Btn)
        {
            Adjust_Menu();
        }
        else if (v == Switch_View)
        {
            Switch_View();
        }
        else if (v == Edit_profile)
        {
            this.startActivity(new Intent(this, Edit_Profile.class));
        }
        else if (v == Server_List)
        {
            this.startActivity(new Intent(this, Server_List.class));
        }
        else if(v == TakeASurvey)
        {
            SurveyAvailable.show();
        }
    }
    //endregion

    //region Connect VPN
    public void Connect(boolean OptimizedServer)
    {
        if(userprofile == null){
            showReloginMessage();
            return;
        }

        //Ask for VPN permission
        ConnectAfterPermissionRequestNotNull = null;
        Intent intent = VpnService.prepare(this);
        if (intent != null)
        {
            ConnectAfterPermissionRequestNotNull = OptimizedServer;
            startActivityForResult(intent, VPN_PERMISSION_REQUEST);
            return;
        }

        if (!commun.IsActive() && !VpnStatus.isVPNActive())
        {
            SubscriptionDialog.show();
            return;
        }

        if(MyVpnServiceInstance != null)    // toufic, add this check because we had problem here 7/9/2018 (MyVpnServiceInstance = null)
        {
            if (MyVpnServiceInstance.ConnectEnabled)
            {
                MyVpnServiceInstance.ConnectEnabled = false;

                //if (OptimizedServer) SelectedServer = GetBestServer();
                if (SelectedServer == null) {
                    SelectedServer = GetBestServer();
                }

                if (SelectedServer == null) {
                    MyVpnServiceInstance.ConnectEnabled = true;
                    Set_NoSelectedServers();
                } else {
                    if (VpnStatus.isVPNActive()) {
                        commun.Log("Stop VPN Connection");
                        MyVpnServiceInstance.ConnectEnabled = true;
                        MyVpnServiceInstance.Stop_VPN();
                    } else {
                        if (commun.isNetworkConnected()) {
                            commun.Log("Start VPN Connection");
                            MyVpnServiceInstance.Start_VPN();
                        } else {
                            commun.Log("No Available Network to Start VPN Connection");
                            MyVpnServiceInstance.ConnectEnabled = true;
                            Set_ConnectionFailed();
                        }
                    }
                }
            }
            else {
                showReloginMessage();
                commun.Log("Connect Button Disabled");
            }
        }else
            showReloginMessage();
    }

    private Server GetBestServer()
    {
        double Health = 0;
        Server VPNServer = null;
        int seed = new Random().nextInt(servers.getServers().size());
        int c =1;
        for (Server server : servers.getServers())
        {
            if (server.getProtocolTypes().contains("OPEN_VPN"))
            {
                /* toufic sleiman 7-5-2017 error was server.getSystemInfo().getHelathPercent() > Health => shows no servers*/

                if (server.getSystemInfo().getHelathPercent() >= Health)
                {
                    Health = server.getSystemInfo().getHelathPercent();
                    VPNServer = server;
                }
                // Commented Above Added By Waqar Just to Randomize server.
//                if (c==seed){
//                    VPNServer = server;
//                }
//                c=c+1;

            }
        }
        return VPNServer;
    }
    //endregion

    //region Connection State
    public void Set_ConnectionStart()
    {
        commun.Log("Display: Connection Start");
        MyActivity.runOnUiThread(new Runnable()
        {
            @Override
            public void run()
            {
                if (IsEarthViewVisible()) Earth_View_Fragment.Start_Animation();
                if (IsMapViewVisible()) Map_View_Fragment.Start_Animation();
            }
        });
    }

    public void Set_Connecting()
    {
        commun.Log("Display: Connecting");
        MyActivity.runOnUiThread(new Runnable()
        {
            @Override
            public void run()
            {
                if (IsEarthViewVisible())  Earth_View_Fragment.Set_Connecting();
                if (IsMapViewVisible())  Map_View_Fragment.Set_Connecting();
            }
        });
    }

    public void Set_Connected()
    {
        commun.updateIntercomAttributeBoolean("Connected", true);
        commun.updateIntercomAttributeString("Last Connected", Calendar.getInstance().getTime().toString());

        commun.Log("Display: Connected");
        MyActivity.runOnUiThread(new Runnable()
        {
            @Override
            public void run()
            {
                if (IsEarthViewVisible()) Earth_View_Fragment.Set_Connected();
                if (IsMapViewVisible())  Map_View_Fragment.Set_Connected();
            }
        });
    }

    public void Set_NotConnected()
    {
        commun.Log("Display: Not Connected");
        MyActivity.runOnUiThread(new Runnable()
        {
            @Override
            public void run()
            {
                if (IsEarthViewVisible()) Earth_View_Fragment.Set_NotConnected();
                if (IsMapViewVisible()) Map_View_Fragment.Set_NotConnected();
            }
        });
    }

    public void Set_ConnectionFailed()
    {
        commun.Log("Display: Connection Failed");
        MyActivity.runOnUiThread(new Runnable()
        {
            @Override
            public void run()
            {
                if (IsEarthViewVisible()) Earth_View_Fragment.Set_ConnectionFailed();
                if (IsMapViewVisible()) Map_View_Fragment.Set_ConnectionFailed();
            }
        });
    }

    public void Set_NoSelectedServers()
    {
        commun.Log("Display: No VPN Server Available");
        if (IsEarthViewVisible()) Earth_View_Fragment.Set_NoSelectedServers();
        if (IsMapViewVisible()) Map_View_Fragment.Set_NoSelectedServers();
    }
    //endregion

    //region ByteCount
    @Override
    public void updateByteCount(long in, long out, long diffIn, long diffOut)
    {
        if (!commun.IsServiceRunning(MyVpnService.class))
        {
            MyActivity.startService(new Intent(MyActivity, MyVpnService.class));
        }

        //%2$s/s %1$s - ↑%4$s/s %3$s
        //final String down = String.format("%2$s/s %1$s", humanReadableByteCount(in, false), humanReadableByteCount(diffIn / OpenVPNManagement.mBytecountInterval, true));
        //final String up = String.format("%2$s/s %1$s", humanReadableByteCount(out, false), humanReadableByteCount(diffOut / OpenVPNManagement.mBytecountInterval, true));

        //commun.Log("Bytes up " + up+ " Bytes Down " + down) ;
        long total = ((in+out)/1024)/1024;
        final long bwlimit =  (userprofile.getBwRemainigLimit() >0) ?  userprofile.getBwRemainigLimit() - total : (500)-total;
        commun.Log(Long.toString(userprofile.getBwRemainigLimit()));
        commun.Log(bwlimit + " MB Left" );


        if (MyVpnServiceInstance != null && MyVpnServiceInstance.CurrentConnectionStatus == ConnectionStatus.LEVEL_CONNECTED)
        {
            if (MyActivity != null)
            {
                MyActivity.runOnUiThread(new Runnable()
                {
                    @Override
                    public void run()
                    {
                        //commun.DisplayToast("UP:" +  up);
                        //commun.DisplayToast("Down:" +  down);
                        if (IsEarthViewVisible()) Earth_View_Fragment.UpdateBytes(bwlimit + " MB Left" );
                    }
                });
            }
        }
    }
    //endregion

    //region OnPermissionResult
    @Override
    public void onRequestPermissionsResult(int requestCode, String[] permissions, int[] grantResults)
    {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);
        if (requestCode == ACCESS_LOCATION_REQUEST && IsMapViewVisible()) Map_View_Fragment.Show_Map_Hint();
    }
    //endregion

    //region OnActivityResult
    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data)
    {
        if (requestCode == VPN_PERMISSION_REQUEST)
        {
            if (resultCode == Activity.RESULT_OK)
            {
                if (ConnectAfterPermissionRequestNotNull != null)
                {
                    Connect(ConnectAfterPermissionRequestNotNull);
                }
            }
        }
    }

    //region OnStart and OnDestroy
    public static boolean active = false;

    @Override
    public void onStart()
    {
        super.onStart();
        active = true;
    }

    @Override
    public void onDestroy() {
        super.onDestroy();
        active = false;
        try {
            MyVpnServiceInstance.SaveToPreference();
        }catch (Exception e){}
    }

    @Override
    public void newLog(LogItem logItem)
    {
        //commun.LogOpenVPN(logItem.toString());
    }
    //endregion

    public static void RefreshExpiryDate(){
        RemainingDays.setText(commun.GetExpiryDateString());
    }

    //Pollfish region
    private void CallPollfish()
    {
        try {
            SurveyAvailable = new Dialog(MyActivity);
            SurveyAvailable.setCanceledOnTouchOutside(true);
            SurveyAvailable.setCancelable(true);
            SurveyAvailable.setContentView(R.layout.survey_popup);
            SurveyAvailable.getWindow().setBackgroundDrawableResource(android.R.color.transparent);
            okbtn = (Button) SurveyAvailable.findViewById(R.id.dialogButtonOK);
            dialogButtonCancel = (Button) SurveyAvailable.findViewById(R.id.dialogButtonCancel);
            okbtn.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    SurveyAvailable.dismiss();
                    PollFish.show();
                }
            });
            dialogButtonCancel.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    SurveyAvailable.dismiss();
                }
            });

            Loading = (RelativeLayout) findViewById(R.id.loading);
            Loading_Text = (MyTextView) findViewById(R.id.Loading_Text);
            Loading_Text.setText(getString(R.string.Loading));

            PollFish.ParamsBuilder paramsBuilder = new PollFish.ParamsBuilder(POLLFISH)
//                    .indicatorPosition(Position.BOTTOM_LEFT)
                    .releaseMode(pollfishRelease)
                    .customMode(pollfishCustomMode)
                    .build();

            PollFish.initWith(this, paramsBuilder);
            PollFish.hide();

        }catch (Exception ex){
            ex.printStackTrace();
            commun.Log("POLLFISH>> " + ex.toString());
        }
    }

    @Override
    public void onPollfishOpened() {
    }

    @Override
    public void onPollfishSurveyNotAvailable() {
        TakeASurvey.setVisibility(View.INVISIBLE);
    }

    @Override
    public void onPollfishClosed() {
        TakeASurvey.setVisibility(View.INVISIBLE);
        PollFish.hide();
    }

    @Override
    public void onPollfishSurveyReceived(boolean b, int i) {
        ShowMessages("onPollfishSurveyReceived");
    }

    @Override
    public void onPollfishSurveyCompleted(boolean b, int i) {
        ShowMessages("onPollfishSurveyCompleted");

    }

    @Override
    public void onUserNotEligible() {
        TakeASurvey.setVisibility(View.INVISIBLE);
        PollFish.hide();
    }

    private void ShowMessages(String flag){
        try {
            if (userprofile == null)
                showReloginMessage();
            else {
                AlertDialog.Builder builder = new AlertDialog.Builder(MyActivity);
                AlertDialog alertDialog = null;

                if (flag.equals("onPollfishSurveyReceived")) {
                    TakeASurvey.setVisibility(View.VISIBLE);
                } else if (flag.equals("onPollfishSurveyCompleted")) {
                    TakeASurvey.setVisibility(View.INVISIBLE);
                    SubscriptionClass subscriptionClass = new SubscriptionClass(Loading_Text, commun, MyActivity, Loading, "Fill_Survey", MyActivity.getPackageManager().getPackageInfo(getPackageName(), 0).versionName);
                    subscriptionClass.getPayment("84399a16-ee63-42aa-93e5-a51d36b88960");
                    RemainingDays.setText(commun.GetExpiryDateString());
                }
            }

        }catch (Exception ex){
            commun.Log(ex.toString());
        }
    }
    //endregion

    //region CheckNewVersion
    private void InitNewVersionCheck(){
        NewVersionDialog = new Dialog(MyActivity);
        NewVersionDialog.setCanceledOnTouchOutside(false);
        NewVersionDialog.setCancelable(false);
        NewVersionDialog.setContentView(R.layout.new_version_update_popup);
        NewVersionDialog.getWindow().setBackgroundDrawableResource(android.R.color.transparent);

        Updatebtn = (Button) NewVersionDialog.findViewById(R.id.Updatebtn);
        dialogButtonCancelNV = (Button) NewVersionDialog.findViewById(R.id.dialogButtonCancel);
        Updatebtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                RedirectToGooglePlay();
            }
        });
        dialogButtonCancelNV.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Logout();
            }
        });
    }

    private void RedirectToGooglePlay(){
        try
        {
            Uri uri = Uri.parse("market://details?id=" + MyActivity.getPackageName());
            Intent goToMarket = new Intent(Intent.ACTION_VIEW, uri);
            goToMarket.addFlags(Intent.FLAG_ACTIVITY_NO_HISTORY | Intent.FLAG_ACTIVITY_NEW_DOCUMENT | Intent.FLAG_ACTIVITY_MULTIPLE_TASK);

            startActivity(goToMarket);
        }
        catch (ActivityNotFoundException e)
        {
            commun.Log(e.getMessage());
            startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse("http://play.google.com/store/apps/details?id=" + MyActivity.getPackageName())));
        }
    }

    private void Logout(){
        SharedPreferences prefs;
        prefs = PreferenceManager.getDefaultSharedPreferences(MyActivity);
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

    private void CallNewVersionCheck(){
        new Thread()
        {
            @Override
            public void run()
            {
                try
                {
                    conn.GetData(LOGIN_CLASS_ID, "4", "subscriptions/appversion", "GET", null, false, MyActivity);
                }
                catch (Exception e)
                {
                }
            }
        }.start();
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
                    Logout();
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

    public void OnResultIsNewVersionExist(String result)
    {
        if (result != null)
        {
            try
            {
                JSONObject j = new JSONObject(result);
                String code = j.getString("code");

                if (code.equals("success"))
                {
                    JSONObject data = j.getJSONObject("data");
                    String androidversion = data.getString("androidVersion");

                    if (Double.parseDouble(MyActivity.getPackageManager().getPackageInfo(getPackageName(), 0).versionName) < Double.parseDouble(androidversion))
                    {
                        NewVersionDialog.show();
                    }
                }
            }
            catch (Exception e)
            {
            }
        }
    }
    //endregion
}
