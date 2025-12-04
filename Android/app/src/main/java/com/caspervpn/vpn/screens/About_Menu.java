package com.caspervpn.vpn.screens;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import androidx.fragment.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;

import com.caspervpn.vpn.R;
import com.caspervpn.vpn.common.Commun;

public class About_Menu extends Fragment implements View.OnClickListener
{
    Activity MyActivity;
    Commun commun;

    LinearLayout AboutCasperVPN, WhatIsCasperVPN, FAQ, PrivacyPolicy, TermsAndConditions;

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState)
    {
        View view = inflater.inflate(R.layout.about_menu, container, false);

        MyActivity = getActivity();
        commun = new Commun(MyActivity);

        Init(view);

        return view;
    }

    private void Init(View v)
    {
        AboutCasperVPN = (LinearLayout) v.findViewById(R.id.AboutCasperVPN);
        WhatIsCasperVPN = (LinearLayout) v.findViewById(R.id.WhatIsCasperVPN);
        FAQ = (LinearLayout) v.findViewById(R.id.FAQ);
        PrivacyPolicy = (LinearLayout) v.findViewById(R.id.PrivacyPolicy);
        TermsAndConditions = (LinearLayout) v.findViewById(R.id.TermsAndConditions);

        AboutCasperVPN.setOnClickListener(this);
        WhatIsCasperVPN.setOnClickListener(this);
        FAQ.setOnClickListener(this);
        PrivacyPolicy.setOnClickListener(this);
        TermsAndConditions.setOnClickListener(this);
    }


    @Override
    public void onClick(View v)
    {
        if (v == AboutCasperVPN)
        {
            Intent myIntent = new Intent(MyActivity, About_CasperVPN.class);
            this.startActivity(myIntent);
        }
        else if (v == WhatIsCasperVPN)
        {
            Intent myIntent = new Intent(MyActivity, What_Is_CasperVPN.class);
            this.startActivity(myIntent);
        }
        else if (v == FAQ)
        {
            Intent myIntent = new Intent(MyActivity, FAQ.class);
            this.startActivity(myIntent);
        }
        else if (v == PrivacyPolicy)
        {
            Intent myIntent = new Intent(MyActivity, Privacy_Policy.class);
            this.startActivity(myIntent);
        }
        else if (v == TermsAndConditions)
        {
            Intent myIntent = new Intent(MyActivity, Terms_And_Conditions.class);
            this.startActivity(myIntent);
        }
    }
}
