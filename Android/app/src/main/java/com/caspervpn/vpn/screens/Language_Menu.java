package com.caspervpn.vpn.screens;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.preference.PreferenceManager;
import androidx.fragment.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.RadioButton;

import com.caspervpn.vpn.R;
import com.caspervpn.vpn.common.Commun;

public class Language_Menu extends Fragment implements View.OnClickListener
{
    Activity MyActivity;
    Commun commun;
    String SelectedLanguage;

    private RadioButton English, French, German, Russian, Spanish, Turkish, Arabic, Persian, Chinese, Hindi;
    private LinearLayout LL_English, LL_French, LL_German, LL_Russian, LL_Spanish, LL_Turkish, LL_Arabic, LL_Persian, LL_Chinese, LL_Hindi;
    SharedPreferences prefs;

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState)
    {
        View view = inflater.inflate(R.layout.language_menu, container, false);

        MyActivity = getActivity();
        commun = new Commun(MyActivity);
        prefs = PreferenceManager.getDefaultSharedPreferences(MyActivity);
        SelectedLanguage = prefs.getString("language", "");

        Init(view);

        return view;
    }

    private void Init(View v)
    {
        LL_English = (LinearLayout) v.findViewById(R.id.English);
        LL_French = (LinearLayout) v.findViewById(R.id.French);
        LL_German = (LinearLayout) v.findViewById(R.id.German);
        LL_Russian = (LinearLayout) v.findViewById(R.id.Russian);
        LL_Spanish = (LinearLayout) v.findViewById(R.id.Spanish);
        LL_Turkish = (LinearLayout) v.findViewById(R.id.Turkish);
        LL_Arabic = (LinearLayout) v.findViewById(R.id.Arabic);
        LL_Persian = (LinearLayout) v.findViewById(R.id.Persian);
        LL_Chinese = (LinearLayout) v.findViewById(R.id.Chinese);
        LL_Hindi = (LinearLayout) v.findViewById(R.id.Hindi);


        English = (RadioButton) v.findViewById(R.id.radio_English);
        French = (RadioButton) v.findViewById(R.id.radio_French);
        German = (RadioButton) v.findViewById(R.id.radio_German);
        Russian = (RadioButton) v.findViewById(R.id.radio_Russian);
        Spanish = (RadioButton) v.findViewById(R.id.radio_Spanish);
        Turkish = (RadioButton) v.findViewById(R.id.radio_Turkish);
        Arabic = (RadioButton) v.findViewById(R.id.radio_Arabic);
        Persian = (RadioButton) v.findViewById(R.id.radio_Persian);
        Chinese = (RadioButton) v.findViewById(R.id.radio_Chinese);
        Hindi = (RadioButton) v.findViewById(R.id.radio_Hindi);

        LL_English.setOnClickListener(this);
        LL_French.setOnClickListener(this);
        LL_German.setOnClickListener(this);
        LL_Russian.setOnClickListener(this);
        LL_Spanish.setOnClickListener(this);
        LL_Turkish.setOnClickListener(this);
        LL_Arabic.setOnClickListener(this);
        LL_Persian.setOnClickListener(this);
        LL_Chinese.setOnClickListener(this);
        LL_Hindi.setOnClickListener(this);


        English.setChecked(SelectedLanguage.equals("en"));
        French.setChecked(SelectedLanguage.equals("fr"));
        German.setChecked(SelectedLanguage.equals("de"));
        Russian.setChecked(SelectedLanguage.equals("ru"));
        Spanish.setChecked(SelectedLanguage.equals("es"));
        Turkish.setChecked(SelectedLanguage.equals("tr"));
        Arabic.setChecked(SelectedLanguage.equals("ar"));
        Persian.setChecked(SelectedLanguage.equals("fa"));
        Chinese.setChecked(SelectedLanguage.equals("zh"));
        Hindi.setChecked(SelectedLanguage.equals("hi"));

    }


    @Override
    public void onClick(View v)
    {
        if (v == LL_English)
        {
            if (English.isChecked()) return;

            English.setChecked(true);
            French.setChecked(false);
            German.setChecked(false);
            Russian.setChecked(false);
            Spanish.setChecked(false);
            Turkish.setChecked(false);
            Arabic.setChecked(false);
            Persian.setChecked(false);
            Chinese.setChecked(false);
            Hindi.setChecked(false);

            SelectedLanguage = "en";
        }
        else if (v == LL_French)
        {
            if (French.isChecked()) return;

            English.setChecked(false);
            French.setChecked(true);
            German.setChecked(false);
            Russian.setChecked(false);
            Spanish.setChecked(false);
            Turkish.setChecked(false);
            Arabic.setChecked(false);
            Persian.setChecked(false);
            Chinese.setChecked(false);
            Hindi.setChecked(false);

            SelectedLanguage = "fr";
        }
        else if (v == LL_German)
        {
            if (German.isChecked()) return;

            English.setChecked(false);
            French.setChecked(false);
            German.setChecked(true);
            Russian.setChecked(false);
            Spanish.setChecked(false);
            Turkish.setChecked(false);
            Arabic.setChecked(false);
            Persian.setChecked(false);
            Chinese.setChecked(false);
            Hindi.setChecked(false);

            SelectedLanguage = "de";
        }
        else if (v == LL_Russian)
        {
            if (Russian.isChecked()) return;

            English.setChecked(false);
            French.setChecked(false);
            German.setChecked(false);
            Russian.setChecked(true);
            Spanish.setChecked(false);
            Turkish.setChecked(false);
            Arabic.setChecked(false);
            Persian.setChecked(false);
            Chinese.setChecked(false);
            Hindi.setChecked(false);

            SelectedLanguage = "ru";
        }
        else if (v == LL_Spanish)
        {
            if (Spanish.isChecked()) return;

            English.setChecked(false);
            French.setChecked(false);
            German.setChecked(false);
            Russian.setChecked(false);
            Spanish.setChecked(true);
            Turkish.setChecked(false);
            Arabic.setChecked(false);
            Persian.setChecked(false);
            Chinese.setChecked(false);
            Hindi.setChecked(false);

            SelectedLanguage = "es";
        }
        else if (v == LL_Turkish)
        {
            if (Turkish.isChecked()) return;

            English.setChecked(false);
            French.setChecked(false);
            German.setChecked(false);
            Russian.setChecked(false);
            Spanish.setChecked(false);
            Turkish.setChecked(true);
            Arabic.setChecked(false);
            Persian.setChecked(false);
            Chinese.setChecked(false);
            Hindi.setChecked(false);

            SelectedLanguage = "tr";
        }
        else if (v == LL_Arabic)
        {
            if (Arabic.isChecked()) return;

            English.setChecked(false);
            French.setChecked(false);
            German.setChecked(false);
            Russian.setChecked(false);
            Spanish.setChecked(false);
            Turkish.setChecked(false);
            Arabic.setChecked(true);
            Persian.setChecked(false);
            Chinese.setChecked(false);
            Hindi.setChecked(false);

            SelectedLanguage = "ar";
        }
        else if (v == LL_Persian)
        {
            if (Persian.isChecked()) return;

            English.setChecked(false);
            French.setChecked(false);
            German.setChecked(false);
            Russian.setChecked(false);
            Spanish.setChecked(false);
            Turkish.setChecked(false);
            Arabic.setChecked(false);
            Persian.setChecked(true);
            Chinese.setChecked(false);
            Hindi.setChecked(false);

            SelectedLanguage = "fa";
        }
        else if (v == LL_Chinese)
        {
            if (Chinese.isChecked()) return;

            English.setChecked(false);
            French.setChecked(false);
            German.setChecked(false);
            Russian.setChecked(false);
            Spanish.setChecked(false);
            Turkish.setChecked(false);
            Arabic.setChecked(false);
            Persian.setChecked(false);
            Chinese.setChecked(true);
            Hindi.setChecked(false);

            SelectedLanguage = "zh";
        }
        else if (v == LL_Hindi)
        {
            if (Hindi.isChecked()) return;

            English.setChecked(false);
            French.setChecked(false);
            German.setChecked(false);
            Russian.setChecked(false);
            Spanish.setChecked(false);
            Turkish.setChecked(false);
            Arabic.setChecked(false);
            Persian.setChecked(false);
            Chinese.setChecked(false);
            Hindi.setChecked(true);

            SelectedLanguage = "hi";
        }
        SetLanguage();
    }


    private void SetLanguage()
    {
        AlertDialog.Builder builder = new AlertDialog.Builder(MyActivity);
        builder.setIcon(R.mipmap.alert_green);
        builder.setTitle((MyActivity.getResources().getString(R.string.RestartConfirmation)));
        builder.setMessage((MyActivity.getResources().getString(R.string.ApplicationRestart)));
        builder.setPositiveButton((MyActivity.getResources().getString(R.string.OK)), new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int whichButton)
            {
                commun.ChangeLanguage(SelectedLanguage);

                Intent i = MyActivity.getBaseContext().getPackageManager().getLaunchIntentForPackage(MyActivity.getBaseContext().getPackageName());
                i.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
                startActivity(i);

                MyActivity.finish();
            }
        });
        builder.setNegativeButton((MyActivity.getResources().getString(R.string.Cancel)), new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int whichButton)
            {
                String OldLanguage = prefs.getString("language", "");

                English.setChecked(OldLanguage.equals("en"));
                French.setChecked(OldLanguage.equals("fr"));
                German.setChecked(OldLanguage.equals("de"));
                Russian.setChecked(OldLanguage.equals("ru"));
                Spanish.setChecked(OldLanguage.equals("es"));
                Turkish.setChecked(OldLanguage.equals("tr"));
                Arabic.setChecked(OldLanguage.equals("ar"));
                Persian.setChecked(OldLanguage.equals("fa"));
                Chinese.setChecked(OldLanguage.equals("zh"));
                Hindi.setChecked(OldLanguage.equals("hi"));
            }
        });
        AlertDialog alertDialog = builder.create();
        alertDialog.show();
    }
}
