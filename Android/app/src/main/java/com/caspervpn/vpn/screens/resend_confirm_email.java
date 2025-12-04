package com.caspervpn.vpn.screens;

import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.preference.PreferenceManager;
import androidx.fragment.app.FragmentActivity;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.View;
import android.widget.ImageButton;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;

import com.caspervpn.vpn.R;
import com.caspervpn.vpn.classes.SendEmailBlock;
import com.caspervpn.vpn.common.Commun;
import com.caspervpn.vpn.common.DataConnection;
import com.caspervpn.vpn.helper.MyApplication;
import com.caspervpn.vpn.helper.MyButton;
import com.caspervpn.vpn.helper.MyEditText;
import com.caspervpn.vpn.helper.MyTextView;
import com.caspervpn.vpn.util.Purchase;
import com.google.gson.Gson;

import org.json.JSONObject;

import io.intercom.android.sdk.Intercom;

import static com.caspervpn.vpn.common.Configuration.RESEND_CONFIRM_EMAIL;
import static com.caspervpn.vpn.common.Configuration.ResendAction;
import static com.caspervpn.vpn.common.Configuration.ResendEmailScreenName;
import static com.caspervpn.vpn.common.Configuration.SignUpsCategory;

public class resend_confirm_email extends FragmentActivity implements View.OnClickListener {

    //region fields
    private Commun commun;
    private DataConnection conn;
    public static Activity MyActivity;

    private MyTextView DisplayMessage, GoBack, Loading_Text;
    private MyEditText Email;
    private ImageView LoginImage;
    private LinearLayout Message;
    private MyButton Resendemail;
    private ImageButton OnlineChat;
    private RelativeLayout Loading;

    private String UserMail = "";
    private Boolean EmailValidated = false;
    SharedPreferences prefs;
    //endregion

    //region OnCreate
    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_resend_confirm_email);
        Intercom.client().handlePushMessage();

        commun = new Commun (this);
        conn = new DataConnection(this);
        MyActivity = this;

        Init();

        //Toufic 3/1/2018 -- google analytics --
        MyApplication.getInstance().trackScreenView(ResendEmailScreenName);
        //Toufic 3/1/2018
    }
    //endregion

    //region Init
    private void Init()
    {
        DisplayMessage = (MyTextView) findViewById(R.id.Display_Message);
        Message = (LinearLayout) findViewById(R.id.Display_Message_Frame);

        OnlineChat = (ImageButton) findViewById(R.id.OnlineChat);

        Email = (MyEditText) findViewById(R.id.User_Name);
        LoginImage = (ImageView) findViewById(R.id.Login_Image);

        Resendemail = (MyButton) findViewById(R.id.ResendEmail);
        GoBack = (MyTextView) findViewById(R.id.GoBack);

        Loading = (RelativeLayout) findViewById(R.id.loading);
        Loading_Text = (MyTextView) findViewById(R.id.Loading_Text);

        //region EditText Listener
        Email.addTextChangedListener(new TextWatcher()
        {
            @Override
            public void beforeTextChanged(CharSequence s, int start, int count, int after) {}

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count)
            {
                Validate_Email();
            }

            @Override
            public void afterTextChanged(Editable s)
            {
                int imageid = UserMail.equals("") ? R.drawable.user_disabled : R.drawable.user_enabled;
                LoginImage.setImageResource(imageid);
            }
        });
        //endregion

        OnlineChat.setOnClickListener(this);
        Resendemail.setOnClickListener(this);
        GoBack.setOnClickListener(this);

        Intent intent = getIntent();
        String EmailAddress = intent.getStringExtra("Email");
        Email.setText(EmailAddress);
        Email.setSelection(Email.getText().length());
    }
    //endregion

    //region EditText Action
    private void Validate_Email()
    {
        UserMail = Email.getText().toString().trim();
        EmailValidated = commun.VerityEmail(UserMail);
        Enable_Disable_Button();
    }

    private void Enable_Disable_Button()
    {
        Resendemail.setEnabled(EmailValidated);
    }
    //endregion

    //region OnClick
    @Override
    public void onClick(View v)
    {
        commun.HideKeyBoard();

        if (v == Resendemail)
        {
            ResendEmail();
        }
        else if (v == GoBack)
        {
            BackToLogin();
        }
        else if (v == OnlineChat)
        {
            Intercom.client().displayMessenger();
        }
    }
    //endregion

    //region Back To Login
    private void BackToLogin()
    {
        Intent myIntent = new Intent();
        myIntent.putExtra("Email", UserMail);
        setResult(Activity.RESULT_OK, myIntent);
        finish();
    }

    @Override
    public void onBackPressed()
    {
        BackToLogin();
    }
    //endregion

    //region ResendEmail
    private void ResendEmail()
    {

        UserMail = Email.getText().toString().trim();

        prefs = PreferenceManager.getDefaultSharedPreferences(this);
        String json = prefs.getString("blockEmailList", "");
        SendEmailBlock sendEmailBlock = null;
        if (!json.equals("")) {
            sendEmailBlock = new Gson().fromJson(json, SendEmailBlock.class);
        }

        if (!EmailValidated) {
            DisplayMessage.setEnabled(true);
            Message.setVisibility(View.VISIBLE);
            DisplayMessage.setText((getResources().getString(R.string.InvalidUserName)));
        } else {
            if (sendEmailBlock!=null){
                if (sendEmailBlock.getEmails().contains(UserMail)){
                    DisplayMessage.setEnabled(true);
                    Message.setVisibility(View.VISIBLE);
                    Loading.setVisibility(View.GONE);
                    DisplayMessage.setText((getResources().getString(R.string.BlockError)));
                    return;
                }
            }
            Loading_Text.setText(getString(R.string.ResendEmail));
            Loading.setVisibility(View.VISIBLE);
            Message.setVisibility(View.INVISIBLE);
            DisplayMessage.setText("");

            Thread thread = new Thread(new Runnable() {
                @Override
                public void run() {
                    try {
                        if (!commun.isNetworkConnected()) {
                            commun.ShowConnectionDialog();
                            return;
                        }

                        conn.GetData(RESEND_CONFIRM_EMAIL, "2", "registration/users/" + UserMail + "/resendConfirmEmail", "PUT", null, false, MyActivity);
                    } catch (Exception e) {
                        runOnUiThread(new Runnable() {
                            @Override
                            public void run() {
                                DisplayMessage.setEnabled(true);
                                Message.setVisibility(View.VISIBLE);
                                Loading.setVisibility(View.GONE);
                                DisplayMessage.setText((getResources().getString(R.string.Error)));
                            }
                        });
                        e.printStackTrace();
                    }
                }
            });

            thread.start();
        }
    }

    public void OnResendEmailResult(String result)
    {
        Loading.setVisibility(View.GONE);

        if (result == null)
        {
            Message.setVisibility(View.VISIBLE);
            DisplayMessage.setEnabled(true);
            DisplayMessage.setText(getResources().getString(R.string.CheckYourInternet));
        }
        else
        {
            Message.setVisibility(View.INVISIBLE);
            DisplayMessage.setText("");
            try
            {
                JSONObject j = new JSONObject(result);
                String code = j.getString("code");

                if (code.equals("success"))
                {
                    commun.Log("EMAIL>>  " + Email.getText().toString().trim());
                    SendEmailBlock sendEmailBlock = new SendEmailBlock();
                    sendEmailBlock.addEmail(Email.getText().toString().trim());
                    commun.SaveClassToPreference(sendEmailBlock, "blockEmailList");
                    Message.setVisibility(View.VISIBLE);
                    DisplayMessage.setEnabled(false);
                    DisplayMessage.setText((getResources().getString(R.string.CheckYourEmailVerification)));

                    //Toufic 7/3/2017 -- google analytics --
                    MyApplication.getInstance().trackEvent(SignUpsCategory, ResendAction, ResendAction);
                    //Toufic 7/3/2017
                }
                else
                {
                    Message.setVisibility(View.VISIBLE);
                    DisplayMessage.setEnabled(true);
                    DisplayMessage.setText((getResources().getString(R.string.UserNotFound)));
                }
            }
            catch (Exception e)
            {
                Message.setVisibility(View.VISIBLE);
                DisplayMessage.setEnabled(true);
                DisplayMessage.setText((getResources().getString(R.string.Error)));
                e.printStackTrace();
            }
        }
    }
    //endregion
}
