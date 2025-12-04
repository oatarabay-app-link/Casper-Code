package com.caspervpn.vpn.screens;

import android.app.Activity;
import android.app.Dialog;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.location.Location;
import android.net.Uri;
import android.os.Bundle;
import androidx.core.app.ActivityCompat;
import androidx.appcompat.app.AppCompatActivity;
import android.view.View;
import android.widget.Button;
import android.widget.ImageButton;
import android.widget.RadioButton;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.caspervpn.vpn.R;
import com.caspervpn.vpn.classes.Server;
import com.caspervpn.vpn.common.Commun;
import com.caspervpn.vpn.common.Configuration;
import com.caspervpn.vpn.helper.MyApplication;
import com.caspervpn.vpn.helper.MyButton;
import com.caspervpn.vpn.helper.MyTextView;
import com.caspervpn.vpn.services.MyVpnService;
import com.google.android.gms.common.api.GoogleApiClient;
import com.google.android.gms.location.LocationListener;
import com.google.android.gms.location.LocationRequest;
import com.google.android.gms.location.LocationServices;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.util.Calendar;

import de.blinkt.openvpn.core.ConnectionStatus;

import static android.Manifest.permission.ACCESS_COARSE_LOCATION;
import static android.Manifest.permission.ACCESS_FINE_LOCATION;
import static com.caspervpn.vpn.common.Configuration.LOCATION_REFRESH_INTERVAL;
import static com.caspervpn.vpn.common.Configuration.MyVpnServiceInstance;
import static com.caspervpn.vpn.common.Configuration.SelectedServer;
import static com.caspervpn.vpn.common.Configuration.ServerDetailsScreenName;
import static com.caspervpn.vpn.common.Configuration.ServerListSelectedServer;
import static com.caspervpn.vpn.common.Configuration.VPNConnectionURL;
import static com.caspervpn.vpn.common.Configuration.user;


/**
 * Created by zaherZ on 1/28/2017.
 */

public class Server_Detail extends AppCompatActivity implements View.OnClickListener, LocationListener, GoogleApiClient.ConnectionCallbacks
{
    MyTextView Country, Bandwidth, Distance, Load, RAM, HardDisk, PingResult;
    RadioButton radio_PPTP, radio_SSTP, radio_IKEv2, radio_L2TP, radio_OpenVPN;
    ImageButton Back_Btn;
    MyButton Connect, Ping;
    Commun commun;
    Activity MyActivity;
    Button DialogButtonOk, DialogButtonRetry;
    Dialog SubscriptionDialog;
    RelativeLayout Loading;
    MyTextView Loading_Text;

    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.server_detail);

        MyActivity = this;
        commun = new Commun(this);

        TextView ApplicationTitle = (TextView) findViewById(R.id.ApplicationTitle);
        if (ServerListSelectedServer != null) ApplicationTitle.setText(ServerListSelectedServer.getServerName());
        else ApplicationTitle.setText(R.string.ServerList);

        Init();
        Init_Map();

        //Toufic 3/1/2018 -- google analytics --
        MyApplication.getInstance().trackScreenView(ServerDetailsScreenName);
        //Toufic 3/1/2018
    }


    @Override
    protected void onResume()
    {
        super.onResume();
        if (!commun.IsServiceRunning(MyVpnService.class))
        {
            MyActivity.startService(new Intent(MyActivity, MyVpnService.class));
        }
    }

    private void Init()
    {
        Back_Btn = (ImageButton) findViewById(R.id.back);
        Back_Btn.setOnClickListener(this);

        Connect = (MyButton) findViewById(R.id.Connect);
        Ping = (MyButton) findViewById(R.id.Ping);

        Connect.setOnClickListener(this);
        Ping.setOnClickListener(this);

        Load = (MyTextView) findViewById(R.id.Load);
        Country = (MyTextView) findViewById(R.id.Country);
        Bandwidth = (MyTextView) findViewById(R.id.Bandwidth);
        Distance = (MyTextView) findViewById(R.id.Distance);
        RAM = (MyTextView) findViewById(R.id.RAM);
        HardDisk = (MyTextView) findViewById(R.id.HardDisk);

        radio_PPTP = (RadioButton) findViewById(R.id.radio_PPTP);
        radio_SSTP = (RadioButton) findViewById(R.id.radio_SSTP);
        radio_IKEv2 = (RadioButton) findViewById(R.id.radio_IKEv2);
        radio_L2TP = (RadioButton) findViewById(R.id.radio_L2TP);
        radio_OpenVPN = (RadioButton) findViewById(R.id.radio_OpenVPN);
        Loading = (RelativeLayout) findViewById(R.id.loading);
        Loading_Text = (MyTextView) findViewById(R.id.Loading_Text);
        Loading_Text.setText(getString(R.string.Connecting));
        Loading.setVisibility(View.GONE);
        Loading_Text.setVisibility(View.GONE);

        Fill_Data();

    }

    private void Fill_Data()
    {
        commun.Log("Server Detail - Fill Data");
        if (SelectedServer == null)  commun.Log("Server Detail - Fill Data - SelectedServer = null");
        if (ServerListSelectedServer == null)  commun.Log("Server Detail - Fill Data - ServerListSelectedServer = null");

        try { // toufic
            if (MyVpnServiceInstance.CurrentConnectionStatus == ConnectionStatus.LEVEL_CONNECTED && SelectedServer.getServerId().equals(ServerListSelectedServer.getServerId()))
                Connect.setText(getString(R.string.DISCONNECT));
            else
                Connect.setText(getString(R.string.CONNECT));
        }catch (Exception e){
            Connect.setText(getString(R.string.CONNECT));
        }

        Country.setText(ServerListSelectedServer.getCountry().equals("null") ? getString(R.string.Unknown) : ServerListSelectedServer.getCountry());
        HardDisk.setText((int) Math.round (ServerListSelectedServer.getSystemInfo().getHDD_All() / 1000000) + " " + getString(R.string.GB));
        RAM.setText((int) Math.round (ServerListSelectedServer.getSystemInfo().getRAM_ALL() / 1000000) + " " + getString(R.string.GB));
        Load.setText((int) (ServerListSelectedServer.getSystemInfo().getHelathPercent()) + " " + getString(R.string.Percent));
        Bandwidth.setText(String.valueOf((int) Math.round (ServerListSelectedServer.getSystemInfo().getNet_All() / 1000000)) + " " + getString(R.string.Mbps));

        radio_PPTP.setEnabled(ServerListSelectedServer.getProtocolTypes().contains("PPTP")) ;
        radio_SSTP.setEnabled(ServerListSelectedServer.getProtocolTypes().contains("SSTP")) ;
        radio_L2TP.setEnabled(ServerListSelectedServer.getProtocolTypes().contains("L2TP")) ;
        radio_IKEv2.setEnabled(ServerListSelectedServer.getProtocolTypes().contains("IKEV2")) ;
        radio_OpenVPN.setEnabled(ServerListSelectedServer.getProtocolTypes().contains("OPEN_VPN")) ;

        if (radio_OpenVPN.isEnabled()) radio_OpenVPN.setChecked(true);
        else if (radio_SSTP.isEnabled()) radio_SSTP.setChecked(true);
        else if (radio_PPTP.isEnabled()) radio_PPTP.setChecked(true);
        else if (radio_L2TP.isEnabled()) radio_L2TP.setChecked(true);
        else if (radio_IKEv2.isEnabled()) radio_IKEv2.setChecked(true);
    }

    @Override
    public void onClick(View v)
    {
        if (v == Back_Btn)
        {
            this.finish();
        }
        else if (v == Connect)
        {
            if (radio_OpenVPN.isChecked())
            {
                try {
                    user.setUserConnectServerDetails(true);  // toufic sleiman 6-11-2017
                    Configuration.ServerListInstance.finish();
                    Server oldserver = SelectedServer;
                    SelectedServer = ServerListSelectedServer;
                    commun.SaveClassToPreference(SelectedServer, "SelectedServer");

                    // if connected and not selected server (he can connect) - app should disconnect then connect
                    if (MyVpnServiceInstance != null && ServerListSelectedServer != null) {     //toufic
                        if (MyVpnServiceInstance.CurrentConnectionStatus == ConnectionStatus.LEVEL_CONNECTED && !oldserver.getServerId().equals(ServerListSelectedServer.getServerId())) {
                            Loading.setVisibility(View.VISIBLE);
                            Loading_Text.setVisibility(View.VISIBLE);

                            commun.Log("Stop VPN Connection");
                            MyVpnServiceInstance.ConnectEnabled = true;
                            MyVpnServiceInstance.Stop_VPN();

                            WaitToConnect();
                        } else Configuration.LandingPageInstance.Connect(false);
                    } else {
                        commun.Log("MyVpnServiceInstance = null");
                    }
                }catch (Exception ex){
                    ex.printStackTrace();
                    commun.Log(ex.toString());
                }

                this.finish();
            }
            else
            {
                Intent i = new Intent(Intent.ACTION_VIEW);
                i.setData(Uri.parse(VPNConnectionURL));
                startActivity(i);
            }
        }
        else if (v == Ping)
        {
            ping(ServerListSelectedServer.getServerIp());
            commun.DisplayToast(getString(R.string.PingStarted), false);
        }
        else if (v ==  DialogButtonOk)
        {
            SubscriptionDialog.cancel();
        }
        else if (v ==  DialogButtonRetry)
        {
            SubscriptionDialog.cancel();
            onClick(Ping);
        }

    }

    private void WaitToConnect(){
        new Thread()
        {
            @Override
            public void run()
            {
                try
                {
                    synchronized (this)
                    {
                        wait(Configuration.CONNET_WAITING_TIME);
                    }
                }
                catch (InterruptedException e)
                {
                    e.printStackTrace();
                }
                finally
                {
                    commun.Log("Start VPN Connection");
                    MyVpnServiceInstance.Start_VPN();
                }
            }
        }.start();
    }

    public void ping(final String url)
    {

        Thread thread = new Thread(new Runnable()
        {

            @Override
            public void run()
            {
                try
                {
                    Process p1 = java.lang.Runtime.getRuntime().exec("/system/bin/ping -c 4 " + url);
                    int returnVal = p1.waitFor();
                    final boolean Success = (returnVal==0);

                    BufferedReader stdInput = new BufferedReader(new InputStreamReader(p1.getInputStream()));

                    String s;
                    String concat = "";
                    while ((s = stdInput.readLine()) != null) concat += s + "\n";
                    p1.destroy();

                    final String Result = concat;

                    if (!isFinishing()) MyActivity.runOnUiThread(new Runnable()
                    {
                        @Override
                        public void run()
                        {
                            commun.DisplayToast(Success ? "Ping Successful" : "Ping Failed", false);

                            SubscriptionDialog = new Dialog(MyActivity);
                            SubscriptionDialog.setContentView(R.layout.ping_popup);
                            SubscriptionDialog.getWindow().setBackgroundDrawableResource(android.R.color.transparent);
                            PingResult = (MyTextView) SubscriptionDialog.findViewById(R.id.Pingtext);
                            PingResult.setText(Result);
                            DialogButtonOk = (Button) SubscriptionDialog.findViewById(R.id.dialogButtonOK);
                            DialogButtonRetry = (Button) SubscriptionDialog.findViewById(R.id.dialogButtonCancel);
                            DialogButtonOk.setOnClickListener((View.OnClickListener) MyActivity);
                            DialogButtonRetry.setOnClickListener((View.OnClickListener) MyActivity);
                            SubscriptionDialog.show();
                        }
                    });

                }
                catch (Exception e)
                {
                    e.printStackTrace();
                    if (!isFinishing()) commun.DisplayToast("Ping Failed", false);
                }
            }
        });
        thread.start();
    }  public static boolean active = false;

    @Override
    public void onStart()
    {
        super.onStart();
        mGoogleApiClient.connect();
        active = true;
    }

    @Override
    public void onDestroy()
    {
        super.onDestroy();
        active = false;
    }

    @Override
    public void onStop()
    {
        super.onStop();
        mGoogleApiClient.disconnect();
    }


    private GoogleApiClient mGoogleApiClient;
    private LocationRequest locationRequest;

    private void Init_Map()
    {
        locationRequest = LocationRequest.create().setPriority(LocationRequest.PRIORITY_HIGH_ACCURACY).setInterval(LOCATION_REFRESH_INTERVAL).setFastestInterval(LOCATION_REFRESH_INTERVAL);
        mGoogleApiClient = new GoogleApiClient.Builder(MyActivity).addConnectionCallbacks(this).addApi(LocationServices.API).build();


        if (ActivityCompat.checkSelfPermission(MyActivity, ACCESS_FINE_LOCATION) != PackageManager.PERMISSION_GRANTED ||
                ActivityCompat.checkSelfPermission(MyActivity, ACCESS_COARSE_LOCATION) != PackageManager.PERMISSION_GRANTED)
        {
            ActivityCompat.requestPermissions(MyActivity, new String[]{ACCESS_COARSE_LOCATION, ACCESS_FINE_LOCATION}, 100);
        }
    }


    //region UserLocation
    @Override
    public void onConnected(Bundle connectionHint)
    {
        if (ActivityCompat.checkSelfPermission(MyActivity, ACCESS_FINE_LOCATION) == PackageManager.PERMISSION_GRANTED &&
                ActivityCompat.checkSelfPermission(MyActivity, ACCESS_COARSE_LOCATION) == PackageManager.PERMISSION_GRANTED)
        {
            Location location = LocationServices.FusedLocationApi.getLastLocation(mGoogleApiClient);
            onLocationChanged (location);

            LocationServices.FusedLocationApi.requestLocationUpdates(mGoogleApiClient, locationRequest, this);
        }
    }

    @Override
    public void onLocationChanged(Location location)
    {
        if (location == null) Distance.setText(getString(R.string.Unknown));
        else
        {
            String distance = commun.CalculateDistance(ServerListSelectedServer.getServerLatitude(), ServerListSelectedServer.getServerLongitude(), location.getLatitude(), location.getLongitude());
            Distance.setText(distance  + " " + getString(R.string.KM));
        }
    }

    @Override
    public void onConnectionSuspended(int i)
    {

    }
}