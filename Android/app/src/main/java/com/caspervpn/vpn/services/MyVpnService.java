package com.caspervpn.vpn.services;

import android.app.PendingIntent;
import android.app.Service;
import android.content.ComponentName;
import android.content.Context;
import android.content.Intent;
import android.content.ServiceConnection;
import android.content.SharedPreferences;
import android.os.Handler;
import android.os.IBinder;
import android.os.RemoteException;
import android.os.SystemClock;
import android.preference.PreferenceManager;
//import androidx.annotation.NonNull;
import androidx.annotation.NonNull;
import android.text.TextUtils;

import com.caspervpn.vpn.common.Commun;
import com.caspervpn.vpn.common.Configuration;
import com.caspervpn.vpn.common.DataConnection;

import org.apache.commons.lang3.StringUtils;
import org.json.JSONArray;
import org.json.JSONObject;

import java.io.IOException;
import java.io.StringReader;
import java.util.Collection;
import java.util.Locale;
import java.util.Timer;
import java.util.TimerTask;
import java.util.Vector;

import de.blinkt.openvpn.activities.DisconnectVPN;
import de.blinkt.openvpn.activities.LaunchVPN;
import de.blinkt.openvpn.activities.VpnProfile;
import de.blinkt.openvpn.core.ConfigParser;
import de.blinkt.openvpn.core.Connection;
import de.blinkt.openvpn.core.ConnectionStatus;
import de.blinkt.openvpn.core.IOpenVPNServiceInternal;
import de.blinkt.openvpn.core.OpenVPNService;
import de.blinkt.openvpn.core.ProfileManager;
import de.blinkt.openvpn.core.VpnStatus;

import static com.caspervpn.vpn.common.Configuration.BACKGROUND_REFRESH_INTERVAL;
import static com.caspervpn.vpn.common.Configuration.CONNECTION_BUG_REFRESH_INTERVAL;
import static com.caspervpn.vpn.common.Configuration.CasperVPNProfileName;
import static com.caspervpn.vpn.common.Configuration.LandingPageInstance;
import static com.caspervpn.vpn.common.Configuration.MyVpnServiceInstance;
import static com.caspervpn.vpn.common.Configuration.SERVICE_CLASS_ID;
import static com.caspervpn.vpn.common.Configuration.SIMULTANIOUS_CONNECTION_BUG_INTERVAL;
import static com.caspervpn.vpn.common.Configuration.SelectedServer;
import static com.caspervpn.vpn.common.Configuration.userprofile;
import static de.blinkt.openvpn.activities.VpnProfile.TYPE_CERTIFICATES;
import static de.blinkt.openvpn.activities.VpnProfile.TYPE_KEYSTORE;
import static de.blinkt.openvpn.activities.VpnProfile.TYPE_STATICKEYS;
import static de.blinkt.openvpn.activities.VpnProfile.TYPE_USERPASS_CERTIFICATES;
import static de.blinkt.openvpn.activities.VpnProfile.TYPE_USERPASS_KEYSTORE;
import static de.blinkt.openvpn.activities.VpnProfile.X509_VERIFY_TLSREMOTE;
import static de.blinkt.openvpn.core.ConnectionStatus.LEVEL_NOTCONNECTED;
import static de.blinkt.openvpn.core.OpenVPNService.DISCONNECT_VPN;
import static de.blinkt.openvpn.core.VpnStatus.mLastLevel;

/**
 * Created by zaherZ on 4/18/2017.
 */

public class MyVpnService extends Service  implements VpnStatus.StateListener {
    Commun commun;
    Handler TimeOutHandler = new Handler(), DisconnectionBugHandler = new Handler(), NewConnectionHandler = new Handler();
    Runnable TimeOutRunnable, DisconnectionBugRunnable, NewConnectionRunnable;
    public Long ConnectionStartTime = 0l;
    SharedPreferences prefs;
    DataConnection conn;
    Timer timer;

    //Service MyVpnService;

    public ConnectionStatus CurrentConnectionStatus = mLastLevel;

    public Boolean ReadConnectionState = true, ConnectEnabled = true;
    private boolean DisconnectionBugEnable = false;
    public boolean ConnectOnDisconnect = false;

    //region Init
    @Override
    public void onCreate() {
        MyVpnServiceInstance = this;
        prefs = PreferenceManager.getDefaultSharedPreferences(this);
        LoadFromPreference();

        super.onCreate();

        conn = new DataConnection(this);

        commun = new Commun(this);
        commun.Log("Service Created");

        VpnStatus.addStateListener(this);

        NewConnectionRunnable = new Runnable() {
            @Override
            public void run() {
                commun.Log("Switching between MapServers");
                ConnectOnDisconnect = false;
                if (LandingPageInstance != null) LandingPageInstance.Connect(false);
            }
        };

        //region Runnable for Connection timeout
        TimeOutRunnable = new Runnable() {
            @Override
            public void run() {
                commun.Log("VPN Timeout By the Application");
                ConnectEnabled = true;
                Stop_VPN();
                if (LandingPageInstance != null) LandingPageInstance.Set_ConnectionFailed();
            }
        };
        //endregion

        //region Runnable for Disconnection Bug
        DisconnectionBugRunnable = new Runnable() {
            @Override
            public void run() {
                commun.Log("Fixing Bug Connectivity");
                if (VpnStatus.isVPNActive() && CurrentConnectionStatus != ConnectionStatus.LEVEL_CONNECTED && CurrentConnectionStatus != LEVEL_NOTCONNECTED) {
                    PauseVPN();
                    ResumeVPN();

                    DisconnectionBugHandler.postDelayed(DisconnectionBugRunnable, CONNECTION_BUG_REFRESH_INTERVAL);
                }
            }
        };
        //endregion

        //region Background Data Refresh
        timer = new Timer();
        timer.scheduleAtFixedRate(new TimerTask() {
            @Override
            public void run() {
                if (prefs.getBoolean("Islogin", false)) GetUserProfile();
            }
        }, 0, BACKGROUND_REFRESH_INTERVAL);
        //endregion
    }

    @Override
    public int onStartCommand(Intent intent, int flags, int startId) {
        return START_NOT_STICKY;
    }

    @Override
    public IBinder onBind(Intent arg0) {
        return null;
    }

    @Override
    public void onDestroy() {
        commun.Log("Service Destroyed");

        timer.cancel();
        TimeOutHandler.removeCallbacks(TimeOutRunnable);
        DisconnectionBugHandler.removeCallbacks(DisconnectionBugRunnable);

        VpnStatus.removeStateListener(this);
        //MyVpnService.startService(new Intent(MyVpnService, MyVpnService.class));

        super.onDestroy();
    }
    //endregion

    //region Preference
    public void LoadFromPreference() {
        ConnectionStartTime = prefs.getLong("ConnectionStartTime", 0);
    }

    public void SaveToPreference() {
        prefs.edit().putLong("ConnectionStartTime", ConnectionStartTime).commit();
    }
    //endregion

    //region VPN Status
    @Override
    public void updateState(String state, String logmessage, int localizedResId, ConnectionStatus level) {
        commun.Log("VPN Active State: " + VpnStatus.isVPNActive());
        commun.Log("***CONNECTION LEVEL***: " + level.name());
        commun.Log("***VPN CONNECTION ***: " + logmessage);

        //region Timer
        if (level == ConnectionStatus.LEVEL_CONNECTED) {
            if (ConnectionStartTime == 0l) ConnectionStartTime = SystemClock.elapsedRealtime();
        } else {
            ConnectionStartTime = 0l;
        }
        //endregion

        //Do not repeat same action if already proceeded
        if (CurrentConnectionStatus == level) return;
        CurrentConnectionStatus = level;


        if (level == ConnectionStatus.LEVEL_START) {
            ConnectEnabled = false;
            ReadConnectionState = true;
            DisconnectionBugEnable = false;

            DisconnectionBugHandler.removeCallbacks(DisconnectionBugRunnable);

            if (LandingPageInstance != null) LandingPageInstance.Set_ConnectionStart();
        } else if (level == ConnectionStatus.LEVEL_CONNECTED) {
            ConnectEnabled = true;
            DisconnectionBugEnable = true;

            TimeOutHandler.removeCallbacks(TimeOutRunnable);
            DisconnectionBugHandler.removeCallbacks(DisconnectionBugRunnable);

            if (LandingPageInstance != null) LandingPageInstance.Set_Connected();

        } else if (level == LEVEL_NOTCONNECTED) {
            ConnectEnabled = true;
            DisconnectionBugEnable = false;

            TimeOutHandler.removeCallbacks(TimeOutRunnable);
            DisconnectionBugHandler.removeCallbacks(DisconnectionBugRunnable);

            commun.Log("ReadConnectionState: " + ReadConnectionState);
            if (!ReadConnectionState) {
                ReadConnectionState = true;
            } else {
                if (LandingPageInstance != null) LandingPageInstance.Set_NotConnected();
            }

            if (ConnectOnDisconnect)
            {
                NewConnectionHandler.postDelayed(NewConnectionRunnable, SIMULTANIOUS_CONNECTION_BUG_INTERVAL);
            }
        } else {
            ConnectEnabled = true;

            if (LandingPageInstance != null) LandingPageInstance.Set_Connecting();

            if (DisconnectionBugEnable) {
                PauseVPN();
                ResumeVPN();

                DisconnectionBugHandler.postDelayed(DisconnectionBugRunnable, CONNECTION_BUG_REFRESH_INTERVAL);

                DisconnectionBugEnable = false;
            }

            ConnectionStartTime = 0l;
        }
    }


    @Override
    public void setConnectedVPN(String uuid) {
    }
    //endregion

    //region StopVPN
    public void Stop_VPN() {
        commun.Log("Stop VPN");
        ReadConnectionState = true;

        Intent disconnectVPN = new Intent(this, DisconnectVPN.class);
        disconnectVPN.setAction(DISCONNECT_VPN);
        disconnectVPN.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
        startActivity(disconnectVPN);
    }
    //endregion

    //region StartVPN
    public void Start_VPN() {
        commun.Log("Start VPN");
        ReadConnectionState = false;

        VpnProfile profile = GetCasperVPNProfile();
        commun.Log("Start VPN " + profile.mIPv4Address);
        commun.Log("Start VPN " + profile.getName());
        commun.Log("Start VPN " + profile.mConnections[0].mServerName);


        if (!checkProfile (profile))
        {
            ConnectEnabled = true;
            if (LandingPageInstance != null) LandingPageInstance.Set_ConnectionFailed();
            return;
        }

        saveProfile(profile);

        Intent intent = new Intent(this, LaunchVPN.class);
        intent.putExtra(LaunchVPN.EXTRA_KEY, profile.getUUID().toString());
        intent.setAction(Intent.ACTION_MAIN);
        //intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
        LandingPageInstance.startActivity(intent);

        TimeOutHandler.postDelayed(TimeOutRunnable, Configuration.VPN_CONNECTION_TIMEOUT);
    }
    //endregion

    //region Pause VPN
    public void PauseVPN()
    {
        commun.Log("Pause VPN");

        Intent pauseVPN = new Intent(MyVpnServiceInstance, OpenVPNService.class);
        pauseVPN.setAction("de.blinkt.openvpn.PAUSE_VPN");

        PendingIntent pauseVPNPending = PendingIntent.getService(MyVpnServiceInstance, 0, pauseVPN, 0);

        try {
            pauseVPNPending.send();
        } catch (PendingIntent.CanceledException e) {
            e.printStackTrace();
        }
    }
    //endregion

    //region Resume VPN
    public void ResumeVPN()
    {
        commun.Log("Resume VPN");

        Intent resumeVPN = new Intent(MyVpnServiceInstance, OpenVPNService.class);
        resumeVPN.setAction("de.blinkt.openvpn.RESUME_VPN");

        PendingIntent resumeVPNPending = PendingIntent.getService(MyVpnServiceInstance, 0, resumeVPN, 0);
        try
        {
            resumeVPNPending.send();
        }
        catch (PendingIntent.CanceledException e)
        {
            e.printStackTrace();
        }
    }
    //endregion

    //region VPN Profile
    public VpnProfile GetCasperVPNProfile() {
        VpnProfile profile = null;

        String Username = userprofile.getLogin();
        String Password = userprofile.getSubscription().getVPNPassword();
        String ServerID = SelectedServer.getServerId();
        String VPN_Config = SelectedServer.getParameters().getOPEN_VPN().getConf();
        String VPN_Certificate = "[[NAME]]" + StringUtils.substringBetween(VPN_Config, "ca ", ".CRT") + ".CRT[[INLINE]]" + SelectedServer.getParameters().getOPEN_VPN().getCert();


        if (getProfileManager().getProfiles().isEmpty())
            profile = CreateCasperVPNProfile(CasperVPNProfileName, Username, Password, VPN_Config, VPN_Certificate, ServerID);
        else
            profile = UpdateCasperVPNProfile(getProfileManager().getProfiles().iterator().next(), Username, Password, VPN_Config, VPN_Certificate, ServerID);

        return profile;
    }

    private ProfileManager getProfileManager() {
        return ProfileManager.getInstance(this);
    }

    private VpnProfile UpdateCasperVPNProfile(VpnProfile mResult, String MyUserName, String MymPassword, String MyVPNConfiguration, String MyCertificate, String ServerID) {
        try {

            ConfigParser cp = new ConfigParser();
            cp.parseConfig(new StringReader(MyVPNConfiguration));

            mResult = cp.convertProfile(mResult);
            mResult.mServerID = ServerID;
            mResult.mUsername = MyUserName;
            mResult.mPassword = MymPassword;
            mResult.mCaFilename = MyCertificate;
            mResult.mAllowLocalLAN = false;

            return mResult;
        } catch (IOException | ConfigParser.ConfigParseError e) {
            e.printStackTrace();
            return null;
        }
    }

    private VpnProfile CreateCasperVPNProfile(String MyProfileName, String MyUserName, String MymPassword, String MyVPNConfiguration, String MyCertificate, String ServerID) {
        try {
            ConfigParser cp = new ConfigParser();
            cp.parseConfig(new StringReader(MyVPNConfiguration));
            VpnProfile mResult = cp.convertProfile(null);

            mResult.mName = MyProfileName;
            mResult.mServerID = ServerID;
            mResult.mUsername = MyUserName;
            mResult.mPassword = MymPassword;
            mResult.mCaFilename = MyCertificate;
            mResult.mAllowLocalLAN = false;

            return mResult;
        } catch (IOException | ConfigParser.ConfigParseError e) {
            e.printStackTrace();
            return null;
        }
    }

    private void saveProfile(VpnProfile mResult) {
        ProfileManager vpl = ProfileManager.getInstance(this);

        vpl.addProfile(mResult);
        vpl.saveProfile(this, mResult);
        vpl.saveProfileList(this);
    }
    //endregion

    //region Correct VPN Profile Checker
    public boolean checkProfile(VpnProfile profile) {
        if (profile.mAuthenticationType == TYPE_KEYSTORE || profile.mAuthenticationType == TYPE_USERPASS_KEYSTORE)
        {
            if (profile.mAlias == null)
                return false;
        } else if (profile.mAuthenticationType == TYPE_CERTIFICATES || profile.mAuthenticationType == TYPE_USERPASS_CERTIFICATES){
            if (TextUtils.isEmpty(profile.mCaFilename))
                return false;
        }

        if (profile.mCheckRemoteCN && profile.mX509AuthType==X509_VERIFY_TLSREMOTE)
            return false;

        if (!profile.mUsePull || profile.mAuthenticationType == TYPE_STATICKEYS) {
            if (profile.mIPv4Address == null || cidrToIPAndNetmask(profile.mIPv4Address) == null)
                return false;
        }
        if (!profile.mUseDefaultRoute) {
            if (!TextUtils.isEmpty(profile.mCustomRoutes) && getCustomRoutes(profile.mCustomRoutes).size() == 0)
                return false;

            if (!TextUtils.isEmpty(profile.mExcludedRoutes) && getCustomRoutes(profile.mExcludedRoutes).size() == 0)
                return false;

        }

        if (profile.mUseTLSAuth && TextUtils.isEmpty(profile.mTLSAuthFilename))
            return false;

        if ((profile.mAuthenticationType == TYPE_USERPASS_CERTIFICATES || profile.mAuthenticationType == TYPE_CERTIFICATES)
                && (TextUtils.isEmpty(profile.mClientCertFilename) || TextUtils.isEmpty(profile.mClientKeyFilename)))
            return false;

        if ((profile.mAuthenticationType == TYPE_CERTIFICATES || profile.mAuthenticationType == TYPE_USERPASS_CERTIFICATES)
                && TextUtils.isEmpty(profile.mCaFilename))
            return false;


        boolean noRemoteEnabled = true;
        for (Connection c : profile.mConnections)
            if (c.mEnabled)
                noRemoteEnabled = false;

        if (noRemoteEnabled)
            return false;

        // Everything okay
        return true;

    }

    private String cidrToIPAndNetmask(String route)
    {
        String[] parts = route.split("/");

        // No /xx, assume /32 as netmask
        if (parts.length == 1)
            parts = (route + "/32").split("/");

        if (parts.length != 2)
            return null;
        int len;
        try {
            len = Integer.parseInt(parts[1]);
        } catch (NumberFormatException ne) {
            return null;
        }
        if (len < 0 || len > 32)
            return null;


        long nm = 0xffffffffL;
        nm = (nm << (32 - len)) & 0xffffffffL;

        String netmask = String.format(Locale.ENGLISH, "%d.%d.%d.%d", (nm & 0xff000000) >> 24, (nm & 0xff0000) >> 16, (nm & 0xff00) >> 8, nm & 0xff);
        return parts[0] + "  " + netmask;
    }

    @NonNull
    private Collection<String> getCustomRoutes(String routes) {
        Vector<String> cidrRoutes = new Vector<>();
        if (routes == null) {
            // No routes set, return empty vector
            return cidrRoutes;
        }
        for (String route : routes.split("[\n \t]")) {
            if (!route.equals("")) {
                String cidrroute = cidrToIPAndNetmask(route);
                if (cidrroute == null)
                    return cidrRoutes;

                cidrRoutes.add(cidrroute);
            }
        }

        return cidrRoutes;
    }
    //endregion

    //region Background Refresh
    private void GetUserProfile()
    {
        Thread thread = new Thread(new Runnable()
        {
            @Override
            public void run()
            {
                try
                {
                    conn.GetData(SERVICE_CLASS_ID, "1", "users/profile", "GET", null, true, MyVpnServiceInstance);
                }
                catch (Exception e) {}
            }
        });

        thread.start();

    }

    public void OnUserProfileResult(String result)
    {
        if (result != null)
        {
            try
            {
                JSONObject j = new JSONObject(result);
                String code = j.getString("code");

                if (code != null && code.equals("success"))
                {
                    JSONObject userdata  = j.getJSONObject("data");
                    commun.SaveUserProfile(userdata);

                    if (!commun.IsActive() && VpnStatus.isVPNActive())  //Drop VPN connection whenever subscription expired
                    {
                        Stop_VPN();
                    }
                }
            }
            catch (Exception e) {}


            Get_Servers();
        }
    }

    private void Get_Servers()
    {
        Thread thread = new Thread(new Runnable()
        {
            @Override
            public void run()
            {
                try
                {
                    conn.GetData(SERVICE_CLASS_ID, "2", "vpn/servers/foruser", "GET", null, true, MyVpnServiceInstance);
                }
                catch (Exception e)
                {
                    e.printStackTrace();
                }
            }
        });

        thread.start();

    }

    public void OnServerResult(String result)
    {
        if (result != null)
        {
            try
            {
                JSONObject j = new JSONObject(result);
                String code = j.getString("code");

                if (code != null && code.equals("success"))
                {
                    JSONArray serverList  = j.getJSONArray("data");
                    if (serverList.length() > 0)
                    {
                        commun.SaveServer(serverList);
                    }
                }
            }
            catch (Exception e)
            {
                e.printStackTrace();
            }
        }
    }

    public void Clear()
    {
        ConnectEnabled = true;
        //MyVpnServiceInstance.Stop_VPN();   Code do not work without a breakpoint

        //Manually Disconnect the VPN
        ServiceConnection mConnection = new ServiceConnection() {
            @Override
            public void onServiceConnected(ComponentName className, IBinder service) {
                IOpenVPNServiceInternal mService = IOpenVPNServiceInternal.Stub.asInterface(service);

                try {
                    mService.stopVPN(false);
                } catch (RemoteException e) {
                    e.printStackTrace();
                    VpnStatus.logException(e);
                }
                unbindService(this);
            }

            @Override
            public void onServiceDisconnected(ComponentName arg0) {
            }

        };

        Intent intent = new Intent(this, OpenVPNService.class);
        intent.setAction(OpenVPNService.START_SERVICE);
        bindService(intent, mConnection, Context.BIND_AUTO_CREATE);
        if (LaunchVPN.MyActivity != null) LaunchVPN.MyActivity.finish();
    }
    //endregion
}