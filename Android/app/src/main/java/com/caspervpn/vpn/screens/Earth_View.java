package com.caspervpn.vpn.screens;

import android.app.Activity;
import android.graphics.Color;
import android.os.Bundle;
import androidx.fragment.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.animation.Animation;
import android.view.animation.AnimationSet;
import android.view.animation.LinearInterpolator;
import android.view.animation.RotateAnimation;
import android.view.animation.ScaleAnimation;
import android.view.animation.TranslateAnimation;
import android.widget.Chronometer;
import android.widget.ImageButton;
import android.widget.ImageView;
import android.widget.LinearLayout;

import com.caspervpn.vpn.R;
import com.caspervpn.vpn.common.Commun;
import com.caspervpn.vpn.helper.Circle;
import com.caspervpn.vpn.helper.CircleAngleAnimation;
import com.caspervpn.vpn.helper.MyApplication;
import com.caspervpn.vpn.helper.MyTextView;

import de.blinkt.openvpn.core.ConnectionStatus;
import de.blinkt.openvpn.core.VpnStatus;
import io.intercom.android.sdk.Intercom;

import static android.view.animation.Animation.RELATIVE_TO_SELF;
import static com.caspervpn.vpn.R.id.Connect_Disconnect;
import static com.caspervpn.vpn.common.Configuration.EarthPageScreenName;
import static com.caspervpn.vpn.common.Configuration.LandingPageInstance;
import static com.caspervpn.vpn.common.Configuration.MyVpnServiceInstance;
import static com.caspervpn.vpn.common.Configuration.SelectedServer;


public class Earth_View extends Fragment implements View.OnClickListener
{
    //region Fields
    private Commun commun;
    private Activity MyActivity;
    private Boolean ClearAnimation = false, PreventEarlyRotating = true, LastStateConnected = false;

    private LinearLayout Connection_Info;
    private MyTextView Error_Message, ConnectStatus, CountryName, NeedHelp;
    private ImageView ConnectRotate, ConnectAnimation, Connect;
    private Chronometer Counter,ByteCounter;
    private View CompleteCircle;
    private Circle Circle;

    private AnimationSet animSet;
    private RotateAnimation rotate;
    private CircleAngleAnimation circle;
    private ImageButton OnlineChat;
    //endregion

    //region OnCreate
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState)
    {

        //Toufic 7/3/2017 -- google analytics --
        MyApplication.getInstance().trackScreenView(EarthPageScreenName);
        //Toufic 7/3/2017 -- google analytics --

        View view = inflater.inflate(R.layout.earth_view, container, false);

        MyActivity = getActivity();
        commun = new Commun(MyActivity);
        commun.Log("Earth onCreateView");

        Init(view);

        return view;
    }
    //endregion

    //region OnResume
    @Override
    public void onResume()
    {
        commun.Log("Earth onResume");

        super.onResume();
        try {
            if (VpnStatus.isVPNActive() && MyVpnServiceInstance.CurrentConnectionStatus != ConnectionStatus.LEVEL_CONNECTED) {
                Start_Rotating(true);
            } else if (VpnStatus.isVPNActive() && MyVpnServiceInstance.CurrentConnectionStatus == ConnectionStatus.LEVEL_CONNECTED) {
                Set_Connected();
            } else {
                Set_NotConnected();
            }
        }catch (Exception e){}
    }
    //endregion

    //region Init
    private void Init(View v)
    {
        Circle = (Circle) v.findViewById(R.id.circle);
        CompleteCircle =  v.findViewById(R.id.complete_circle);
        Error_Message = (MyTextView) v.findViewById(R.id.Error_Message);
        Connection_Info = (LinearLayout) v.findViewById(R.id.Connection_Info);
        ConnectAnimation = (ImageView) v.findViewById(R.id.Connect_Animation);
        ConnectRotate = (ImageView) v.findViewById(R.id.Connect_Rotate);
        Counter = (Chronometer) v.findViewById(R.id.Counter);
        ByteCounter = (Chronometer) v.findViewById(R.id.ByteCounter);

        CountryName =  (MyTextView) v.findViewById(R.id.CountryName);
        ConnectStatus =  (MyTextView) v.findViewById(R.id.ConnectStatus);
        Connect =  (ImageButton) v.findViewById(Connect_Disconnect);
        OnlineChat = (ImageButton) v.findViewById(R.id.OnlineChat);
        NeedHelp = (MyTextView) v.findViewById(R.id.NeedHelp);
        NeedHelp.setTextColor(Color.WHITE);

        OnlineChat.setOnClickListener(this);
        Connect.setOnClickListener(this);
    }

    private void InitAnimation()
    {
        //region Translate and Scale Animation
        int DestinationPos[] = new int[2];
        ConnectRotate.getLocationInWindow( DestinationPos );

        int SourcePos[] = new int[2];
        ConnectAnimation.getLocationInWindow( SourcePos );

        //int TranslateDuration = Integer.parseInt(commun.CalculateDistance(SourcePos[0], SourcePos[1], DestinationPos[0], DestinationPos[1])) / 9;

        animSet = new AnimationSet(true);
        animSet.setFillAfter(false);
        animSet.setDuration(500);
        animSet.setInterpolator(new LinearInterpolator());

        float ScaleFactor = getResources().getDimension(R.dimen.ScaledConnectButtonDimensions) / getResources().getDimension(R.dimen.ConnectButtonDimensions);
        ScaleAnimation scale = new ScaleAnimation(1f, ScaleFactor, 1f, ScaleFactor,  RELATIVE_TO_SELF, 0f,  RELATIVE_TO_SELF, 0f);
        animSet.addAnimation(scale);

        TranslateAnimation translate = new TranslateAnimation( 0, DestinationPos[0]  - SourcePos[0], 0, DestinationPos[1] - SourcePos[1]);
        animSet.addAnimation(translate);

        animSet.setAnimationListener(new Animation.AnimationListener()
        {
            @Override
            public void onAnimationStart(Animation arg0) { }
            @Override
            public void onAnimationRepeat(Animation arg0) { }
            @Override
            public void onAnimationEnd(Animation arg0)
            {
                PreventEarlyRotating = false;
                if (!ClearAnimation) Start_Rotating(false);
            }
        });
        //endregion

        //region Rotate Animation
        float scaleRatio = getResources().getDisplayMetrics().density;
        float RotateScaleDimen = getResources().getDimensionPixelSize(R.dimen.RotateScaleValue) / scaleRatio;
        float value = commun.dipToPixels(RotateScaleDimen);
        rotate = new RotateAnimation(360, 0, RotateAnimation.ABSOLUTE, value, RotateAnimation.ABSOLUTE, value);
        rotate.setDuration(2000);
        rotate.setInterpolator(new LinearInterpolator());
        rotate.setRepeatMode(Animation.INFINITE);
        rotate.setRepeatCount(Animation.INFINITE);
        //endregion

        //region Circle Angle Animation
        circle = new CircleAngleAnimation(Circle, -360);
        circle.setInterpolator(new LinearInterpolator());
        circle.setDuration(2000);
        circle.setFillAfter(true);
        //endregion
    }
    //endregion

    //region Animation
    public void Start_Animation()
    {
        ClearAnimation = false;
        PreventEarlyRotating = true;

        Error_Message.setVisibility(View.INVISIBLE);

        InitAnimation(); //Do not move Init animation to onCreateView

        ConnectAnimation.setVisibility(View.VISIBLE);
        ConnectAnimation.startAnimation(animSet);
    }

    private void Start_Rotating(boolean ShowCompletedCircle)
    {
        if (!PreventEarlyRotating)
        {
            ConnectStatus.setText(getString(R.string.Connecting));
            CompleteCircle.setVisibility(ShowCompletedCircle ? View.VISIBLE : View.GONE);

            ConnectAnimation.setVisibility(View.INVISIBLE);

            InitAnimation();

            ConnectRotate.startAnimation(rotate);
            Circle.startAnimation(circle);

            Circle.setVisibility(View.VISIBLE);
            ConnectRotate.setVisibility(View.VISIBLE);
        }
    }

    private void Start_Connected_Animation()
    {
        commun.StartFlashing(CompleteCircle, true);
    }
    //endregion

    //region Status
    public void Set_Connecting()
    {
        Connection_Info.setVisibility(View.INVISIBLE);
        Error_Message.setVisibility(View.INVISIBLE);

        Connect.setSelected(false);
        ConnectStatus.setText(getString(R.string.Connecting));

        if (LastStateConnected)
        {
            if (ConnectRotate.getVisibility() != View.VISIBLE)
            {
                InitAnimation();

                ConnectRotate.setVisibility(View.VISIBLE);
                ConnectRotate.startAnimation(rotate);
            }
            Start_Rotating (true);
            LastStateConnected = false;
        }
    }

    public void Set_Connected()
    {
        Start_Timer();
        LastStateConnected = true;

        if (SelectedServer != null) CountryName.setText(commun.GetConnectedServerName());

        Error_Message.setVisibility(View.INVISIBLE);
        Connection_Info.setVisibility(View.VISIBLE);

        ConnectRotate.clearAnimation();
        ConnectRotate.setVisibility(View.INVISIBLE);
        Circle.clearAnimation();
        Circle.setVisibility(View.INVISIBLE);
        CompleteCircle.setVisibility(View.VISIBLE);

        Start_Connected_Animation();

        Connect.setSelected(true);
        ConnectStatus.setText(getString(R.string.Disconnect));
    }

    public void Set_NotConnected()
    {
        Stop_Timer();
        LastStateConnected = false;
        ClearAnimation = true;

        ConnectRotate.clearAnimation();
        ConnectRotate.setVisibility(View.INVISIBLE);
        Circle.clearAnimation();
        Circle.setVisibility(View.INVISIBLE);
        CompleteCircle.removeCallbacks(null);
        CompleteCircle.setVisibility(View.INVISIBLE);
        Connection_Info.setVisibility(View.INVISIBLE);

        Connect.setSelected(false);
        ConnectStatus.setText(getString(R.string.Connect));
    }

    public void Set_ConnectionFailed()
    {
        Set_NotConnected();

        Error_Message.setText(getString(R.string.CannotConnectCheckYourInternet));
        Error_Message.setVisibility(View.VISIBLE);
    }


    public void UpdateBytes(String Bytes)
    {
        ByteCounter.setText(Bytes);
    }

    public void Set_NoSelectedServers()
    {
        Set_NotConnected();

        Error_Message.setText(getString(R.string.CannotConnectNоServerAvailable));
        Error_Message.setVisibility(View.VISIBLE);
    }
    //endregion

    //region Onclick
    @Override
    public void onClick(View v)
    {
        if (v == Connect)
        {
            LandingPageInstance.Connect(true);
        }
        else if (v == OnlineChat)
        {
            Intercom.client().displayMessenger();
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
