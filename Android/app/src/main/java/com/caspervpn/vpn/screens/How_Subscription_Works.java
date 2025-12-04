package com.caspervpn.vpn.screens;

import android.app.Activity;
import android.content.Intent;
import android.net.Uri;
import androidx.appcompat.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageButton;
import android.widget.TextView;

import com.caspervpn.vpn.R;
import com.caspervpn.vpn.common.Commun;
import com.caspervpn.vpn.helper.MyJustifiedTextView;
import com.caspervpn.vpn.helper.MyTextView;

import static com.caspervpn.vpn.R.id.back;
import static com.caspervpn.vpn.common.Configuration.PRIVACY_POLICY;
import static com.caspervpn.vpn.common.Configuration.TERMS_CONDITIONS;

public class How_Subscription_Works extends AppCompatActivity implements View.OnClickListener{
    ImageButton Back_Btn, Shrink_Font, Grow_Font;
    MyTextView ourservicetxt ;
    MyJustifiedTextView PrivacyPolicy;
    Activity MyActivity;
    Commun commun;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_how__subscription__works);
        MyActivity = this;
        commun = new Commun(this);

        TextView ApplicationTitle = (TextView) findViewById(R.id.ApplicationTitle);
        ApplicationTitle.setText(getString(R.string.TERMSANDCONDITIONS));

        Back_Btn = (ImageButton) findViewById(back);
        Back_Btn.setOnClickListener(this);

        Shrink_Font = (ImageButton) findViewById(R.id.Shrink_Font);
        ourservicetxt = (MyTextView) findViewById(R.id.ourservicetxt);
        PrivacyPolicy = (MyJustifiedTextView) findViewById(R.id.PrivacyPolicytxt);
        Shrink_Font.setVisibility(View.VISIBLE);
        Shrink_Font.setOnClickListener(this);

        Grow_Font = (ImageButton) findViewById(R.id.Grow_Font);
        Grow_Font.setVisibility(View.VISIBLE);
        Grow_Font.setOnClickListener(this);
        ourservicetxt.setOnClickListener(this);
        PrivacyPolicy.setOnClickListener(this);
    }

    @Override
    public void onClick(View v)
    {
        if (v == Back_Btn)
        {
            this.finish();
        }
        if (v == Grow_Font)
        {
            commun.ZoomViews((ViewGroup) MyActivity.findViewById(R.id.ScrollView), 1);
        }
        if (v == Shrink_Font)
        {
            commun.ZoomViews((ViewGroup) MyActivity.findViewById(R.id.ScrollView), -1);
        }
        if (v == ourservicetxt)
        {
            startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse(TERMS_CONDITIONS)));
        }
        if (v == PrivacyPolicy)
        {
            startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse(PRIVACY_POLICY)));
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
