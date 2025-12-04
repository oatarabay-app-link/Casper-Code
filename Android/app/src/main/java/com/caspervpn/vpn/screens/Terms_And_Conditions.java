package com.caspervpn.vpn.screens;

import android.app.Activity;
import android.os.Bundle;
import androidx.appcompat.app.AppCompatActivity;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageButton;
import android.widget.TextView;

import com.caspervpn.vpn.R;
import com.caspervpn.vpn.common.Commun;

import static com.caspervpn.vpn.R.id.back;

/**
 * Created by zaherZ on 1/28/2017.
 */

public class Terms_And_Conditions extends AppCompatActivity implements View.OnClickListener
{
    ImageButton Back_Btn, Shrink_Font, Grow_Font;
    Activity MyActivity;
    Commun commun;

    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.terms_and_conditions);
        MyActivity = this;
        commun = new Commun(this);

        TextView ApplicationTitle = (TextView) findViewById(R.id.ApplicationTitle);
        ApplicationTitle.setText(getString(R.string.TERMSANDCONDITIONS));

        Back_Btn = (ImageButton) findViewById(back);
        Back_Btn.setOnClickListener(this);

        Shrink_Font = (ImageButton) findViewById(R.id.Shrink_Font);
        Shrink_Font.setVisibility(View.VISIBLE);
        Shrink_Font.setOnClickListener(this);

        Grow_Font = (ImageButton) findViewById(R.id.Grow_Font);
        Grow_Font.setVisibility(View.VISIBLE);
        Grow_Font.setOnClickListener(this);
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