package com.caspervpn.vpn.screens;

import android.animation.Animator;
import android.animation.AnimatorListenerAdapter;
import android.animation.ValueAnimator;
import android.app.Activity;
import android.content.SharedPreferences;
import android.content.pm.PackageManager;
import android.graphics.Bitmap;
import android.location.Location;
import android.os.Bundle;
import android.preference.PreferenceManager;
import androidx.core.app.ActivityCompat;
import androidx.fragment.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.animation.AccelerateDecelerateInterpolator;
import android.view.animation.Animation;
import android.view.animation.DecelerateInterpolator;
import android.view.animation.LinearInterpolator;
import android.view.animation.RotateAnimation;
import android.view.animation.TranslateAnimation;
import android.widget.Chronometer;
import android.widget.ImageButton;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;

import com.caspervpn.vpn.R;
import com.caspervpn.vpn.classes.Server;
import com.caspervpn.vpn.common.Commun;
import com.caspervpn.vpn.helper.DoubleArrayEvaluator;
import com.caspervpn.vpn.helper.MapWrapperLayout;
import com.caspervpn.vpn.helper.MyApplication;
import com.caspervpn.vpn.helper.MyButton;
import com.caspervpn.vpn.helper.MyTextView;
import com.caspervpn.vpn.helper.OnInfoWindowElemTouchListener;
import com.google.android.gms.common.api.GoogleApiClient;
import com.google.android.gms.common.api.GoogleApiClient.ConnectionCallbacks;
import com.google.android.gms.location.LocationListener;
import com.google.android.gms.location.LocationRequest;
import com.google.android.gms.location.LocationServices;
import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.MapView;
import com.google.android.gms.maps.OnMapReadyCallback;
import com.google.android.gms.maps.model.BitmapDescriptorFactory;
import com.google.android.gms.maps.model.CameraPosition;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.LatLngBounds;
import com.google.android.gms.maps.model.MapStyleOptions;
import com.google.android.gms.maps.model.Marker;
import com.google.android.gms.maps.model.MarkerOptions;

import java.util.ArrayList;

import de.blinkt.openvpn.core.ConnectionStatus;
import de.blinkt.openvpn.core.ProfileManager;
import de.blinkt.openvpn.core.VpnStatus;

import static android.Manifest.permission.ACCESS_COARSE_LOCATION;
import static android.Manifest.permission.ACCESS_FINE_LOCATION;
import static android.graphics.BitmapFactory.decodeResource;
import static com.caspervpn.vpn.R.id.map;
import static com.caspervpn.vpn.common.Configuration.ACCESS_LOCATION_REQUEST;
import static com.caspervpn.vpn.common.Configuration.AlwaysShowMapHint;
import static com.caspervpn.vpn.common.Configuration.LOCATION_REFRESH_INTERVAL;
import static com.caspervpn.vpn.common.Configuration.LandingPageInstance;
import static com.caspervpn.vpn.common.Configuration.MapPageScreenName;
import static com.caspervpn.vpn.common.Configuration.MyVpnServiceInstance;
import static com.caspervpn.vpn.common.Configuration.SelectedServer;
import static com.caspervpn.vpn.common.Configuration.servers;


public class Map_View extends Fragment implements OnMapReadyCallback, ConnectionCallbacks, View.OnClickListener, LocationListener
{
    //region Fields
    private Commun commun;
    private Activity MyActivity;

    private int Marker_Dimensions;
    private float MapIndicatorSize;
    private SharedPreferences prefs;

    private MapView mapView;
    private GoogleMap googleMap;
    private ImageButton Connect_Disconnect;
    private Marker UserLocation_Bg, UserLocation_Ghost;
    private Bitmap UserLocationGhostIcon, UserLocationBackgroundIcon;
    private ArrayList<Marker> MarkerArray = new ArrayList<Marker>();
    private ImageView UpArrow, DownArrow;
    private LinearLayout Connection_Info;
    private RelativeLayout Map_Hint;
    private MyButton Yes_Btn, No_Btn, GotIt;
    private MyTextView Error_Message, ConnectStatus, CountryName, Popup_Message;
    private ViewGroup infoWindow, your_location_infoWindow;
    private Chronometer Counter;

    private Location UserLastLocation;

    private TranslateAnimation TranslateUp, TranslateDown;
    private RotateAnimation rotate;

    private LatLng SourcePosition, DestinationPosition;

    private OnInfoWindowElemTouchListener NoListener, YesListener;

    private ArrayList<Server> ServerList;
    private MapWrapperLayout mapWrapperLayout;
    private GoogleApiClient mGoogleApiClient;
    private LocationRequest locationRequest;
    //endregion

    //TODO add key restriction https://console.developers.google.com/apis/credentials/key/269?project=caspervpn-applink

    //region onCreateView
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState)
    {

        //Toufic 7/3/2017 -- google analytics --
        MyApplication.getInstance().trackScreenView(MapPageScreenName);
        //Toufic 7/3/2017 -- google analytics --

        MyActivity = this.getActivity();
        commun = new Commun(MyActivity);
        commun.Log("MapView onCreateView");
        prefs = PreferenceManager.getDefaultSharedPreferences(MyActivity);

        float scaleRatio = getResources().getDisplayMetrics().density;
        MapIndicatorSize = getResources().getDimensionPixelSize(R.dimen.MapIndicatorSize) / scaleRatio;
        Marker_Dimensions = (int) commun.dipToPixels(MapIndicatorSize);

        View view = inflater.inflate(R.layout.map_view, container, false);
        Init(view);
        Init_Animation();
        Init_Hint_Animation();
        Init_MAP();

        //region Request Location Permission
        if (ActivityCompat.checkSelfPermission(MyActivity, ACCESS_FINE_LOCATION) != PackageManager.PERMISSION_GRANTED ||
            ActivityCompat.checkSelfPermission(MyActivity, ACCESS_COARSE_LOCATION) != PackageManager.PERMISSION_GRANTED)
        {
            ActivityCompat.requestPermissions(MyActivity, new String[]{ACCESS_COARSE_LOCATION, ACCESS_FINE_LOCATION}, ACCESS_LOCATION_REQUEST);
        }
        else
        {
            Show_Map_Hint();
        }

        return view;
        //endregion
    }
    //endregion

    //region Map Hint
    public void Show_Map_Hint()
    {
        Boolean ShowMapHint = prefs.getBoolean("ShowMapHint", true);

        if (ShowMapHint || AlwaysShowMapHint)
        {
            prefs.edit().putBoolean("ShowMapHint", false).commit();

            DownArrow.startAnimation(TranslateUp);
            UpArrow.startAnimation(TranslateDown);
            Map_Hint.setVisibility(View.VISIBLE);
        }

        onConnected(null); //Map hint is shown directly after getting user permission, kick the map to track user location
    }
    //endregion

    //region Init
    private void Init(View v)
    {
        Connection_Info = (LinearLayout) v.findViewById(R.id.Connection_Info);
        Error_Message = (MyTextView) v.findViewById(R.id.Error_Message);
        Map_Hint = (RelativeLayout) v.findViewById(R.id.Map_Hint);

        UpArrow = (ImageView) v.findViewById(R.id.up_arrow);
        DownArrow = (ImageView) v.findViewById(R.id.down_arrow);
        GotIt = (MyButton) v.findViewById(R.id.GotIt);
        Counter = (Chronometer) v.findViewById(R.id.Counter);
        CountryName = (MyTextView) v.findViewById(R.id.CountryName);
        ConnectStatus = (MyTextView) v.findViewById(R.id.ConnectStatus);
        Connect_Disconnect = (ImageButton) v.findViewById(R.id.Connect_Disconnect);

        Connect_Disconnect.setOnClickListener(this);
        GotIt.setOnClickListener(this);
    }

    private void Init_Animation()
    {
        //region Rotate Animation
        rotate = new RotateAnimation(360, 0, RotateAnimation.RELATIVE_TO_SELF, 0.5f, RotateAnimation.RELATIVE_TO_SELF, 0.5f);
        rotate.setDuration(900);
        rotate.setInterpolator(new AccelerateDecelerateInterpolator());
        rotate.setRepeatMode(Animation.INFINITE);
        rotate.setRepeatCount(Animation.INFINITE);
        rotate.setFillAfter(true);
        //endregion
    }

    private void Init_Hint_Animation()
    {
        float TranslationDepth = commun.dipToPixels(10);
        int TranslationDuration = 200;

        TranslateUp = new TranslateAnimation( 0, 0, 0, -TranslationDepth);
        TranslateUp.setFillAfter(true);
        TranslateUp.setDuration(TranslationDuration);
        TranslateUp.setInterpolator(new LinearInterpolator());

        TranslateDown = new TranslateAnimation( 0, 0, -TranslationDepth, 0);
        TranslateDown.setFillAfter(true);
        TranslateDown.setDuration(TranslationDuration);
        TranslateDown.setInterpolator(new LinearInterpolator());

        TranslateUp.setAnimationListener(new Animation.AnimationListener()
        {
            @Override
            public void onAnimationStart(Animation arg0) { }
            @Override
            public void onAnimationRepeat(Animation arg0) { }
            @Override
            public void onAnimationEnd(Animation arg0)
            {
                UpArrow.startAnimation(TranslateDown);
                DownArrow.startAnimation(TranslateUp);
            }
        });

        TranslateDown.setAnimationListener(new Animation.AnimationListener()
        {
            @Override
            public void onAnimationStart(Animation arg0) { }
            @Override
            public void onAnimationRepeat(Animation arg0) { }
            @Override
            public void onAnimationEnd(Animation arg0)
            {
                UpArrow.startAnimation(TranslateUp);
                DownArrow.startAnimation(TranslateDown);
            }
        });
    }

    private void Init_MAP()
    {
        locationRequest = LocationRequest.create().setPriority(LocationRequest.PRIORITY_HIGH_ACCURACY).setInterval(LOCATION_REFRESH_INTERVAL).setFastestInterval(LOCATION_REFRESH_INTERVAL);
        mGoogleApiClient = new GoogleApiClient.Builder(MyActivity).addConnectionCallbacks(this).addApi(LocationServices.API).build();

        Bitmap b1 = decodeResource(getResources(), R.mipmap.your_location_target_bg);
        UserLocationBackgroundIcon = Bitmap.createScaledBitmap(b1, Marker_Dimensions, Marker_Dimensions, false);

        Bitmap b2 = decodeResource(getResources(), R.mipmap.your_location_target_ghost);
        UserLocationGhostIcon = Bitmap.createScaledBitmap(b2, Marker_Dimensions, Marker_Dimensions, false);
    }
    //endregion

    //region onViewCreated
    @Override
    public void onViewCreated(View view, Bundle savedInstanceState)
    {
        mapWrapperLayout = (MapWrapperLayout) view.findViewById(R.id.map_relative_layout);
        mapView = (MapView) view.findViewById(map);
        mapView.onCreate(savedInstanceState);
        mapView.onResume();
        mapView.getMapAsync(this);
    }
    //endregion

    //region onStart and onStop
    @Override
    public void onStart()
    {
        mGoogleApiClient.connect();
        super.onStart();
    }

    @Override
    public void onStop()
    {
        mGoogleApiClient.disconnect();
        super.onStop();
    }
    //endregion

    //region onMapReady
    @Override
    public void onMapReady(GoogleMap map)
    {
        UserLocation_Bg = null;
        UserLocation_Ghost = null;

        // MapIndicatorSize - default marker height
        // 20 - offset between the default InfoWindow bottom edge and it's content bottom edge
        mapWrapperLayout.init(map, commun.getPixelsFromDp((int) MapIndicatorSize + 20));

        try {
            map.setMapStyle(MapStyleOptions.loadRawResourceStyle(getActivity(), R.raw.map_style));
        }catch (Exception ex){
            ex.printStackTrace();
            commun.Log(ex.toString());
        }
        map.setMaxZoomPreference(5);
        map.setMinZoomPreference(2);

        map.setLatLngBoundsForCameraTarget(new LatLngBounds( new LatLng(10, -115 ), new LatLng(66, 140)));

        map.getUiSettings().setTiltGesturesEnabled(false);
        map.getUiSettings().setRotateGesturesEnabled(false);
        map.getUiSettings().setMapToolbarEnabled(false);
        map.getUiSettings().setCompassEnabled(false);
        map.setOnCameraChangeListener(getCameraChangeListener());
        Map_Info_Window(map);

        googleMap = map;

        Load_Servers();


        try {
            //region Entry State
            if (VpnStatus.isVPNActive() && MyVpnServiceInstance.CurrentConnectionStatus != ConnectionStatus.LEVEL_CONNECTED) {
                Start_Animation();
            } else if (VpnStatus.isVPNActive() && MyVpnServiceInstance.CurrentConnectionStatus == ConnectionStatus.LEVEL_CONNECTED) {
                Set_Connected_Flashing();
            } else {
                Set_NotConnected();
            }
            //endregion
        }catch (Exception ex){
            ex.printStackTrace();
            commun.Log(ex.toString());
        }
    }


    //region OnResume
    @Override
    public void onResume()
    {
        commun.Log("MapView onResume");

        super.onResume();
        try {
            if (googleMap == null) return;
            if (VpnStatus.isVPNActive() && MyVpnServiceInstance.CurrentConnectionStatus != ConnectionStatus.LEVEL_CONNECTED) {
                Start_Animation();
            } else if (VpnStatus.isVPNActive() && MyVpnServiceInstance.CurrentConnectionStatus == ConnectionStatus.LEVEL_CONNECTED) {
                Set_Connected_Flashing();
            } else {
                Set_NotConnected();
            }
        }catch (Exception e){
            commun.Log(e.getMessage());}
    }
    //endregion

    private void Map_Info_Window(GoogleMap map)
    {
        infoWindow = (ViewGroup) getLayoutInflater(Bundle.EMPTY).inflate(R.layout.map_popup, null);
        your_location_infoWindow = (ViewGroup) getLayoutInflater(Bundle.EMPTY).inflate(R.layout.map_popup_yourlocation, null);

        Yes_Btn = (MyButton)infoWindow.findViewById(R.id.dialogButtonOK);
        No_Btn = (MyButton)infoWindow.findViewById(R.id.dialogButtonCancel);
        Popup_Message = (MyTextView)infoWindow.findViewById(R.id.Map_Popup_Message);

        NoListener = new OnInfoWindowElemTouchListener(MyActivity, No_Btn)
        {
            @Override
            protected void onClickConfirmed(View v, Marker marker)
            {
                marker.hideInfoWindow();
            }
        };

        YesListener = new OnInfoWindowElemTouchListener(MyActivity, Yes_Btn)
        {
            @Override
            protected void onClickConfirmed(View v, Marker marker)
            {
//                SelectedServer = GetServerByID(marker.getTag().toString());
//
//                marker.hideInfoWindow();
//                LandingPageInstance.Connect(false);


                marker.hideInfoWindow();


                Server ClickedServer = GetServerByID(marker.getTag().toString());


                if ( MyVpnServiceInstance.CurrentConnectionStatus == ConnectionStatus.LEVEL_CONNECTED  && !ProfileManager.getInstance(MyActivity).getProfiles().isEmpty() && !ProfileManager.getInstance(MyActivity).getProfiles().iterator().next().mServerID.equals(ClickedServer.getServerId()))
                {
                    LandingPageInstance.Connect(false);
                    SelectedServer = ClickedServer;
                    MyVpnServiceInstance.ConnectOnDisconnect = true;
                }
                else
                {
                    SelectedServer = ClickedServer;
                    LandingPageInstance.Connect(false);
                }
            }
        };


        map.setInfoWindowAdapter(new GoogleMap.InfoWindowAdapter() {
            @Override
            public View getInfoWindow(Marker marker) {
                return null;
            }

            @Override
            public View getInfoContents(Marker marker) {
                if (marker.getTag() == null)
                {
                    if (MyVpnServiceInstance.CurrentConnectionStatus == ConnectionStatus.LEVEL_CONNECTED)
                        return null;
                    else
                        return your_location_infoWindow;
                }

                Server ClickedServer = GetServerByID(marker.getTag().toString());
                if (ClickedServer != null)
                {
                    if (MyVpnServiceInstance.CurrentConnectionStatus == ConnectionStatus.LEVEL_CONNECTED && !ProfileManager.getInstance(MyActivity).getProfiles().isEmpty() && ProfileManager.getInstance(MyActivity).getProfiles().iterator().next().mServerID.equals(ClickedServer.getServerId()))
                    {
                        Popup_Message.setText(getString(R.string.DisconnectFrom) + " " + (!ClickedServer.getCountry().equals("null") ? ClickedServer.getCountry() : ClickedServer.getServerName()));
                    }
                    else
                    {
                        Popup_Message.setText(getString(R.string.CasperiseTo) + " " + (!ClickedServer.getCountry().equals("null") ? ClickedServer.getCountry() : ClickedServer.getServerName()));
                    }
                }

                YesListener.setMarker(marker);
                NoListener.setMarker(marker);

                No_Btn.setOnTouchListener(NoListener);
                Yes_Btn.setOnTouchListener(YesListener);

                mapWrapperLayout.setMarkerWithInfoWindow(marker, infoWindow);
                return infoWindow;
            }
        });
    }

    @Override
    public void onConnectionSuspended(int i)
    {
    }


    float previousZoomLevel;
    public GoogleMap.OnCameraChangeListener getCameraChangeListener()
    {
        //TODO mapo bound bug not totally fiexed
        return new GoogleMap.OnCameraChangeListener()
        {
            @Override
            public void onCameraChange(CameraPosition position)
            {

                if(previousZoomLevel != position.zoom)
                {
                    commun.Log("Zoom: " + position.zoom);

                    if (position.zoom <= 2.5) googleMap.setLatLngBoundsForCameraTarget(new LatLngBounds( new LatLng(10, -115 ), new LatLng(66, 140)));
                    else if (position.zoom <= 3.5) googleMap.setLatLngBoundsForCameraTarget(new LatLngBounds( new LatLng(-53.5, -162.5), new LatLng(80, 187.5)));
                    else googleMap.setLatLngBoundsForCameraTarget(new LatLngBounds( new LatLng(-36.5, -147), new LatLng(75, 172)));
                }

                previousZoomLevel = position.zoom;
            }
        };
    }
    //endregion

    //region Loading Servers
    private void Load_Servers()
    {
        try {   // toufic 31-5-2018
            if (googleMap == null) return;

            String ConnectedServerID = null;
            MarkerArray = new ArrayList<Marker>();

            if (MyVpnServiceInstance.CurrentConnectionStatus == ConnectionStatus.LEVEL_CONNECTED) {
                if (SelectedServer != null) {
                    ConnectedServerID = SelectedServer.getServerId();
                    googleMap.moveCamera(CameraUpdateFactory.newLatLng(new LatLng(SelectedServer.getServerLatitude(), SelectedServer.getServerLongitude())));
                }
            }
            ServerList = servers.getServers();
            for (int i = 0; i < ServerList.size(); i++) {
                Server server = ServerList.get(i);
                if (!server.getProtocolTypes().contains("OPEN_VPN")) continue;

                LatLng ServerLocation = new LatLng(server.getServerLatitude(), server.getServerLongitude());
                Bitmap resizedBitmap;

                if (ConnectedServerID == null || !ConnectedServerID.equals(server.getServerId())) {
                    Bitmap b = decodeResource(getResources(), GetMapLevel(server.getSystemInfo().getHelathPercent()));
                    resizedBitmap = Bitmap.createScaledBitmap(b, Marker_Dimensions, Marker_Dimensions, false);
                } else {
                    SourcePosition = ServerLocation;
                    Bitmap b = decodeResource(getResources(), GetConnectedMapLevel(server.getSystemInfo().getHelathPercent()));
                    resizedBitmap = Bitmap.createScaledBitmap(b, Marker_Dimensions, Marker_Dimensions, false);
                    //resizedBitmap = UserLocationBackgroundIcon;
                }


                Marker ServerMarker = googleMap.addMarker(new MarkerOptions().position(ServerLocation).title(server.getServerName()));
                ServerMarker.setTag(server.getServerId());
                ServerMarker.setIcon(BitmapDescriptorFactory.fromBitmap(resizedBitmap));
                MarkerArray.add(ServerMarker);
            }
        }catch (Exception ex){
            ex.printStackTrace();
            commun.Log(ex.toString());
        }

        //GenerateRandomServers();
    }

    private void GenerateRandomServers()
    {
        for (int i = 0; i< 50; i++)
        {
            LatLng ServerLocation = new LatLng(commun.getRandom(-90, 90), commun.getRandom(-180, 180));
            Bitmap resizedBitmap;

            Bitmap b = decodeResource(getResources(), GetMapLevel(commun.getRandom(0, 100)));
            resizedBitmap = Bitmap.createScaledBitmap(b, Marker_Dimensions, Marker_Dimensions, false);


            Marker ServerMarker = googleMap.addMarker(new MarkerOptions().position(ServerLocation).title("test"));
            ServerMarker.setTag(i);
            ServerMarker.setIcon(BitmapDescriptorFactory.fromBitmap(resizedBitmap));
            MarkerArray.add(ServerMarker);

        }
    }
    //endregion

    //region UserLocation
    @Override
    public void onConnected(Bundle connectionHint)
    {
        if (ActivityCompat.checkSelfPermission(MyActivity, ACCESS_FINE_LOCATION) == PackageManager.PERMISSION_GRANTED &&
            ActivityCompat.checkSelfPermission(MyActivity, ACCESS_COARSE_LOCATION) == PackageManager.PERMISSION_GRANTED)
        {
            Location location = LocationServices.FusedLocationApi.getLastLocation(mGoogleApiClient);
            onLocationChanged (location);

            if (mGoogleApiClient.isConnected()) LocationServices.FusedLocationApi.requestLocationUpdates(mGoogleApiClient, locationRequest, this);
        }
    }

    @Override
    public void onLocationChanged(Location location)
    {
        try {
            UserLastLocation = location;
            if (UserLastLocation != null) {
                if (MyVpnServiceInstance.CurrentConnectionStatus != null) {
                    if (MyVpnServiceInstance.CurrentConnectionStatus != ConnectionStatus.LEVEL_CONNECTED) {
                        LatLng UserGeoLocation = new LatLng(UserLastLocation.getLatitude(), UserLastLocation.getLongitude());

                        if (UserLocation_Ghost != null && UserLocation_Bg != null) {
                            UserLocation_Ghost.setPosition(UserGeoLocation);
                            UserLocation_Bg.setPosition(UserGeoLocation);
                        } else {
                            UserLocation_Bg = googleMap.addMarker(new MarkerOptions().position(UserGeoLocation));
                            UserLocation_Bg.setIcon(BitmapDescriptorFactory.fromBitmap(UserLocationBackgroundIcon));

                            UserLocation_Ghost = googleMap.addMarker(new MarkerOptions().position(UserGeoLocation));
                            UserLocation_Ghost.setIcon(BitmapDescriptorFactory.fromBitmap(UserLocationGhostIcon));
                        }

                        if (!prefs.getBoolean("IsUserLocationInfoWindowShown", false)) {
                            prefs.edit().putBoolean("IsUserLocationInfoWindowShown", true).commit();
                            UserLocation_Ghost.showInfoWindow();
                        }

                        googleMap.moveCamera(CameraUpdateFactory.newLatLng(UserGeoLocation));
                    }
                }
            }
        }catch (Exception ex){
            ex.printStackTrace();
            commun.Log(ex.toString());
        }
    }

    private void Display_Real_Location()
    {
        if (googleMap == null) return;

        if (UserLocation_Ghost != null)
        {
            UserLocation_Ghost.remove();
            UserLocation_Ghost = null;
        }
        if (UserLocation_Bg != null)
        {
            UserLocation_Bg.remove();
            UserLocation_Bg = null;
        }
        if (UserLastLocation != null)
        {
            LatLng UserGeoLocation = new LatLng(UserLastLocation.getLatitude(), UserLastLocation.getLongitude());

            UserLocation_Bg = googleMap.addMarker(new MarkerOptions().position(UserGeoLocation));
            UserLocation_Bg.setIcon(BitmapDescriptorFactory.fromBitmap(UserLocationBackgroundIcon));

            UserLocation_Ghost = googleMap.addMarker(new MarkerOptions().position(UserGeoLocation));
            UserLocation_Ghost.setIcon(BitmapDescriptorFactory.fromBitmap(UserLocationGhostIcon));
        }
    }
    //endregion

    //region Animation
    public void Start_Connected_Animation()
    {
        if (googleMap == null) return;
        if (SelectedServer == null) return;
        if (UserLocation_Ghost == null)
        {
            DestinationPosition = new LatLng(SelectedServer.getServerLatitude(), SelectedServer.getServerLongitude());

            UserLocation_Ghost = googleMap.addMarker(new MarkerOptions().position(DestinationPosition));
            UserLocation_Ghost.setIcon(BitmapDescriptorFactory.fromBitmap(UserLocationGhostIcon));


            Bitmap b = decodeResource(getResources(), GetConnectedMapLevel(SelectedServer.getSystemInfo().getHelathPercent()));
            Bitmap ConnectedServerIcon = Bitmap.createScaledBitmap(b, Marker_Dimensions, Marker_Dimensions, false);
            Marker server = GetMarker(SelectedServer.getServerId());
            if (server != null) server.setIcon(BitmapDescriptorFactory.fromBitmap(ConnectedServerIcon));//UserLocationBackgroundIcon

            SourcePosition = googleMap.getCameraPosition().target;
            Animate_Map(false);
        }
        else
        {
            SourcePosition = UserLocation_Ghost.getPosition();
            DestinationPosition = new LatLng(SelectedServer.getServerLatitude(), SelectedServer.getServerLongitude());

            Animate_Map_Prepare(googleMap.getCameraPosition().target, SourcePosition);
        }

    }

    private void Animate_Map_Prepare(LatLng SourceCameraPosition, final LatLng DestinationCameraPosition)
    {
        double[] startValues = new double[]{SourceCameraPosition.latitude, SourceCameraPosition.longitude};
        double[] endValues = new double[]{DestinationCameraPosition.latitude, DestinationCameraPosition.longitude};
        ValueAnimator latLngAnimator = ValueAnimator.ofObject(new DoubleArrayEvaluator(), startValues, endValues);
        latLngAnimator.setDuration(500);
        latLngAnimator.setInterpolator(new DecelerateInterpolator());
        latLngAnimator.addUpdateListener(new ValueAnimator.AnimatorUpdateListener()
        {
            @Override
            public void onAnimationUpdate(ValueAnimator animation)
            {
                double[] animatedValue = (double[]) animation.getAnimatedValue();
                LatLng CameraPosition = new LatLng(animatedValue[0], animatedValue[1]);
                googleMap.moveCamera(CameraUpdateFactory.newLatLng(CameraPosition));
            }
        });
        latLngAnimator.addListener(new AnimatorListenerAdapter()
        {
            @Override
            public void onAnimationEnd(Animator animation)
            {
                Animate_Map(true);
            }
        });
        latLngAnimator.start();
    }

    private void Animate_Map(final boolean ShowGhost)
    {
        double[] startValues = new double[]{SourcePosition.latitude, SourcePosition.longitude};
        double[] endValues = new double[]{DestinationPosition.latitude, DestinationPosition.longitude};
        ValueAnimator latLngAnimator = ValueAnimator.ofObject(new DoubleArrayEvaluator(), startValues, endValues);
        latLngAnimator.setDuration(1000);
        latLngAnimator.setInterpolator(new DecelerateInterpolator());
        latLngAnimator.addUpdateListener(new ValueAnimator.AnimatorUpdateListener() {
            @Override
            public void onAnimationUpdate(ValueAnimator animation) {
               double[] animatedValue = (double[]) animation.getAnimatedValue();
               LatLng GhostPosition = new LatLng(animatedValue[0], animatedValue[1]);
               if (ShowGhost)
               {

                   if (UserLocation_Ghost != null) UserLocation_Ghost.setPosition(GhostPosition);
                   else {
                       UserLocation_Ghost = googleMap.addMarker(new MarkerOptions().position(DestinationPosition));
                       UserLocation_Ghost.setIcon(BitmapDescriptorFactory.fromBitmap(UserLocationGhostIcon));
                   }
               }
               googleMap.moveCamera(CameraUpdateFactory.newLatLng(GhostPosition));
            }
        });
        latLngAnimator.addListener(new AnimatorListenerAdapter() {
            @Override
            public void onAnimationEnd(Animator animation) {
                if (UserLocation_Bg != null) UserLocation_Bg.remove();
                if (UserLocation_Ghost != null) UserLocation_Ghost.setPosition(DestinationPosition);

                Bitmap b = decodeResource(MyActivity.getResources(), GetConnectedMapLevel(SelectedServer.getSystemInfo().getHelathPercent()));
                Bitmap ConnectedServerIcon = Bitmap.createScaledBitmap(b, Marker_Dimensions, Marker_Dimensions, false);
                Marker server = GetMarker(SelectedServer.getServerId());
                if (server != null) server.setIcon(BitmapDescriptorFactory.fromBitmap(ConnectedServerIcon));//UserLocationBackgroundIcon

                Start_Flashing();
            }

        });
        latLngAnimator.start();
    }

    private void Start_Flashing()
    {
        commun.StartFlashing(Connect_Disconnect, false);
    }

    public void Start_Animation()
    {
        ConnectStatus.setText(getString(R.string.Connecting));
        Connect_Disconnect.startAnimation(rotate);
    }
    //endregion

    //region States
    public void Set_Connecting()
    {
        Load_Servers();

        Display_Real_Location();

        Connect_Disconnect.startAnimation(rotate);

        Connection_Info.setVisibility(View.INVISIBLE);
        Error_Message.setVisibility(View.INVISIBLE);
        Connect_Disconnect.setImageResource(R.mipmap.map_connect);
        ConnectStatus.setText(getString(R.string.Connecting));
    }

    public void Set_Connected()
    {
        Start_Timer();

        Connect_Disconnect.clearAnimation();
        Connect_Disconnect.setImageResource(R.mipmap.map_disconnect);
        ConnectStatus.setText(getString(R.string.Disconnect));
        Error_Message.setVisibility(View.INVISIBLE);
        Connection_Info.setVisibility(View.VISIBLE);

        Start_Connected_Animation();
    }

    public void Set_Connected_Flashing()
    {
        Start_Timer();

        Connect_Disconnect.clearAnimation();
        Connect_Disconnect.setImageResource(R.mipmap.map_disconnect);
        ConnectStatus.setText(getString(R.string.Disconnect));
        Error_Message.setVisibility(View.INVISIBLE);
        Connection_Info.setVisibility(View.VISIBLE);

        Start_Flashing();



        if (SelectedServer != null)
        {
            commun.Log("SelectedServerLat: " + SelectedServer.getServerLatitude());
            commun.Log("SelectedServerLong: " + SelectedServer.getServerLongitude());

            DestinationPosition = new LatLng(SelectedServer.getServerLatitude(), SelectedServer.getServerLongitude());
        }
        else
        {
            commun.Log("SelectedServer = null");
        }

        if (DestinationPosition == null) commun.Log("DestinationPosition = null");

        if (UserLocation_Ghost != null) UserLocation_Ghost.setPosition(DestinationPosition);
        else
        {
            try { // toufic 31-5-18
                UserLocation_Ghost = googleMap.addMarker(new MarkerOptions().position(DestinationPosition));
                UserLocation_Ghost.setIcon(BitmapDescriptorFactory.fromBitmap(UserLocationGhostIcon));
            }catch (Exception ex){commun.Log(ex.toString());}
        }
        googleMap.moveCamera(CameraUpdateFactory.newLatLng(DestinationPosition));
    }

    public void Set_NotConnected()
    {
        Stop_Timer();

        Connect_Disconnect.clearAnimation();
        Connect_Disconnect.setImageResource(R.mipmap.map_connect);
        ConnectStatus.setText(getString(R.string.Connect));
        Error_Message.setVisibility(View.INVISIBLE);
        Connection_Info.setVisibility(View.INVISIBLE);


//        if(ProfileManager.getInstance(MyActivity).getProfiles().iterator().hasNext())
//        {
//            VpnProfile ConenctedProfile = ProfileManager.getInstance(MyActivity).getProfiles().iterator().next();
//            Marker server = GetMarker(ConenctedProfile.mServerID);
//            if (server != null) {
//                Bitmap b = decodeResource(getResources(), GetMapLevel(SelectedServer.getSystemInfo().getHelathPercent()));
//                Bitmap resizedBitmap = Bitmap.createScaledBitmap(b, Marker_Dimensions, Marker_Dimensions, false);
//                server.setIcon(BitmapDescriptorFactory.fromBitmap(resizedBitmap));
//            }
//        }

        Load_Servers();

        Display_Real_Location();
    }


    public void Set_ConnectionFailed()
    {
        Set_NotConnected();
        Error_Message.setText(getString(R.string.CannotConnectCheckYourInternet));
        Error_Message.setVisibility(View.VISIBLE);

//        if (SelectedServer != null)
//        {
//            Marker server = GetMarker(SelectedServer.getServerId());
//            if (server != null) {
//                Bitmap b = decodeResource(getResources(), GetMapLevel(SelectedServer.getSystemInfo().getHelathPercent()));
//                Bitmap resizedBitmap = Bitmap.createScaledBitmap(b, Marker_Dimensions, Marker_Dimensions, false);
//                server.setIcon(BitmapDescriptorFactory.fromBitmap(resizedBitmap));
//            }
//        }

        Load_Servers();

        Display_Real_Location();
    }

    public void Set_NoSelectedServers()
    {
        Set_NotConnected();

        Error_Message.setText(getString(R.string.CannotConnectNоServerAvailable));
        Error_Message.setVisibility(View.VISIBLE);
    }
    //endregion

    //region helpers
    private Server GetServerByID(String ServerID)
    {
        for (Server Server : ServerList)
        {
            if (Server.getServerId().equals(ServerID)) return Server;
        }
        return null;
    }

    private Marker GetMarker(String serverId)
    {
        for (Marker marker : MarkerArray)
        {
            if (marker.getTag().toString().equals(serverId)) return marker;
        }
        return null;
    }

    private int GetConnectedMapLevel(double health)
    {
        if (health < 25)
            return R.mipmap.map_shield_connected_1;
        else if (health < 50)
            return R.mipmap.map_shield_connected_2;
        else if (health < 75)
            return R.mipmap.map_shield_connected_3;
        else
            return R.mipmap.map_shield_connected_4;
    }

//    private int GetConnectedMapLevel(double health)
//    {
//        if (health < 10)
//            return R.mipmap.map_connected_level_1;
//        else if (health < 20)
//            return R.mipmap.map_connected_level_2;
//        else if (health < 30)
//            return R.mipmap.map_connected_level_3;
//        else if (health < 40)
//            return R.mipmap.map_connected_level_4;
//        else if (health < 50)
//            return R.mipmap.map_connected_level_5;
//        else if (health < 60)
//            return R.mipmap.map_connected_level_6;
//        else if (health < 70)
//            return R.mipmap.map_connected_level_7;
//        else if (health < 80)
//            return R.mipmap.map_connected_level_8;
//        else if (health < 90)
//            return R.mipmap.map_connected_level_9;
//        else
//            return R.mipmap.map_connected_level_10;
//    }

//    private int GetMapLevel(double health)
//    {
//        if (health < 30)
//            return R.mipmap.new_map_level_1;
//        else if (health < 60)
//            return R.mipmap.new_map_level_2;
//        else
//            return R.mipmap.new_map_level_3;
//    }

//    private int GetMapLevel(double health)
//    {
//        if (health < 10)
//            return R.mipmap.map_level_1;
//        else if (health < 20)
//            return R.mipmap.map_level_2;
//        else if (health < 30)
//            return R.mipmap.map_level_3;
//        else if (health < 40)
//            return R.mipmap.map_level_4;
//        else if (health < 50)
//            return R.mipmap.map_level_5;
//        else if (health < 60)
//            return R.mipmap.map_level_6;
//        else if (health < 70)
//            return R.mipmap.map_level_7;
//        else if (health < 80)
//            return R.mipmap.map_level_8;
//        else if (health < 90)
//            return R.mipmap.map_level_9;
//        else
//            return R.mipmap.map_level_10;
//    }

    private int GetMapLevel(double health)
    {
        if (health < 25)
            return R.mipmap.map_shield_1;
        else if (health < 50)
            return R.mipmap.map_shield_2;
        else if (health < 75)
            return R.mipmap.map_shield_3;
        else
            return R.mipmap.map_shield_4;
    }
    //endregion

    //region OnClick
    @Override
    public void onClick(View v)
    {
        if (v == Connect_Disconnect)
        {
            LandingPageInstance.Connect(true);
        }
        else if (v == GotIt)
        {
            Map_Hint.setVisibility(View.GONE);
        }
    }
    //endregion

    //region Timer
    private void Start_Timer()
    {
        if (SelectedServer != null) CountryName.setText(commun.GetConnectedServerName());

        Counter.setBase(MyVpnServiceInstance.ConnectionStartTime);
        Counter.start();
    }

    private void Stop_Timer()
    {
        Counter.stop();
    }
    //endregion
}
