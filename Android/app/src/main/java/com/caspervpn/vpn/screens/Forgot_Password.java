package com.caspervpn.vpn.screens;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import androidx.fragment.app.FragmentActivity;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.ImageButton;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;

import com.caspervpn.vpn.R;
import com.caspervpn.vpn.common.Commun;
import com.caspervpn.vpn.common.DataConnection;
import com.caspervpn.vpn.helper.MyButton;
import com.caspervpn.vpn.helper.MyEditText;
import com.caspervpn.vpn.helper.MyTextView;

import org.json.JSONObject;

import io.intercom.android.sdk.Intercom;

import static com.caspervpn.vpn.common.Configuration.FORGOT_PASSWORD_CLASS_ID;

public class Forgot_Password extends FragmentActivity implements OnClickListener
{
    //region fields
    private Commun commun;
    private  DataConnection conn;
    public static Activity MyActivity;

    private MyTextView DisplayMessage, GoBack, Loading_Text;
    private MyEditText Email;
    private ImageView LoginImage;
    private LinearLayout Message;
    private MyButton Reset;
    private ImageButton OnlineChat;
    private RelativeLayout Loading;

    private String UserMail = "";
    private Boolean EmailValidated = false;
    //endregion

    //region OnCreate
    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.forgot_password);
        Intercom.client().handlePushMessage();

        commun = new Commun (this);
        conn = new DataConnection(this);
        MyActivity = this;

        Init();
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

        Reset = (MyButton) findViewById(R.id.ResetPassword);
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
        Reset.setOnClickListener(this);
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
        Reset.setEnabled(EmailValidated);
    }
    //endregion

    //region OnClick
    @Override
    public void onClick(View v)
    {
        commun.HideKeyBoard();

        if (v == Reset)
        {
            ResetPassword();
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

    //region Reset Password
    private void ResetPassword()
    {
        UserMail = Email.getText().toString().trim();

        if (!EmailValidated)
        {
            DisplayMessage.setEnabled(true);
            Message.setVisibility(View.VISIBLE);
            DisplayMessage.setText((getResources().getString(R.string.InvalidUserName)));
        }
        else
        {
            Loading_Text.setText(getString(R.string.ResettingYourPassword));
            Loading.setVisibility(View.VISIBLE);
            Message.setVisibility(View.INVISIBLE);
            DisplayMessage.setText("");

            Thread thread = new Thread(new Runnable()
            {
                @Override
                public void run()
                {
                    try
                    {
                        if (!commun.isNetworkConnected())
                        {
                            commun.ShowConnectionDialog();
                            return;
                        }

                        conn.GetData(FORGOT_PASSWORD_CLASS_ID, "1", "registration/users/" + UserMail + "/resetpassword", "POST", null, false, MyActivity);
                    }
                    catch (Exception e)
                    {
                        commun.Log(e.getMessage());
                        runOnUiThread(new Runnable()
                        {
                            @Override
                            public void run() {
                                DisplayMessage.setEnabled(true);
                                Message.setVisibility(View.VISIBLE);
                                Loading.setVisibility(View.GONE);
                                DisplayMessage.setText((getResources().getString(R.string.Error)));
                            }});
                    }
                }
            });

            thread.start();
        }
    }

    public void OnResetResult(String result)
    {
        Loading.setVisibility(View.GONE);

        if (result == null)
        {
            DisplayMessage.setEnabled(true);
            Message.setVisibility(View.VISIBLE);
            DisplayMessage.setText((getResources().getString(R.string.Error)));
        }
        else
        {
            try
            {
                JSONObject j = new JSONObject(result);
                String code = j.getString("code");
                if (code.equals("success"))
                {
                    DisplayMessage.setEnabled(false);
                    Message.setVisibility(View.VISIBLE);
                    DisplayMessage.setText((getResources().getString(R.string.CheckYourEmailReset)));
                }
                else if (code.equals("vpn.user-service.not_found"))
                {
                    DisplayMessage.setEnabled(true);
                    Message.setVisibility(View.VISIBLE);
                    DisplayMessage.setText((getResources().getString(R.string.UserNotFound)));
                }
                else
                {
                    DisplayMessage.setEnabled(true);
                    Message.setVisibility(View.VISIBLE);
                    DisplayMessage.setText((getResources().getString(R.string.Error)));
                }
            }
            catch (Exception e)
            {
                commun.Log(e.getMessage());
                DisplayMessage.setEnabled(true);
                Message.setVisibility(View.VISIBLE);
                DisplayMessage.setText((getResources().getString(R.string.Error)));
            }
        }
    }
    //endregion
}
