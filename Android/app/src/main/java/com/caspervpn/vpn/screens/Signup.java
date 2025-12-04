package com.caspervpn.vpn.screens;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.ComponentName;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import androidx.fragment.app.FragmentActivity;
import android.text.Editable;
import android.text.Html;
import android.text.TextWatcher;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.ImageButton;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TableRow;
import android.widget.TextView;

import com.caspervpn.vpn.R;
import com.caspervpn.vpn.common.Commun;
import com.caspervpn.vpn.common.DataConnection;
import com.caspervpn.vpn.helper.MyApplication;
import com.caspervpn.vpn.helper.MyButton;
import com.caspervpn.vpn.helper.MyEditText;
import com.caspervpn.vpn.helper.MyPasswordText;
import com.caspervpn.vpn.helper.MyTextView;

import org.json.JSONObject;

import io.intercom.android.sdk.Intercom;

import static com.caspervpn.vpn.common.Configuration.RESEND_CONFIRM_EMAIL;
import static com.caspervpn.vpn.common.Configuration.ResendAction;
import static com.caspervpn.vpn.common.Configuration.SIGNUP_CLASS_ID;
import static com.caspervpn.vpn.common.Configuration.SignUpScreenName;
import static com.caspervpn.vpn.common.Configuration.SignUpsCategory;


public class Signup extends FragmentActivity implements OnClickListener
{
    //region fields
    private Commun commun;
    private DataConnection conn;
    public static Activity MyActivity;

    private MyTextView DisplayMessage, SignIn, AlreadyAMember, Loading_Text, ResendEmail;
    private MyEditText Email;
    private MyPasswordText Password, ConfirmPassword;
    private ImageView LoginImage, PasswordImage, ConfirmPasswordImage;
    private ImageButton ShowPassword, ShowPasswordConfirmation, OnlineChat;
    private LinearLayout Message;
    private MyButton Register, VerifyEmail;
    private RelativeLayout Loading;

    private TableRow Resend_table;

    private Boolean EmailValidated = false, PasswordValidated = false, PasswordConfirmationValidated = false;
    private String UserMail = "", UserPassword = "", UserConfirmedPassword = "";
    //endregion

    //region onCreate
    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.signup);
        Intercom.client().handlePushMessage();

        MyActivity = this;
        commun = new Commun (this);
        conn = new DataConnection(this);

        Init();

        //Toufic 3/1/2018 -- google analytics --
        MyApplication.getInstance().trackScreenView(SignUpScreenName);
        //Toufic 3/1/2018
    }
    //endregion

    //region Init
    private void Init()
    {
        SignIn = (MyTextView) findViewById(R.id.Sign_In);
        DisplayMessage = (MyTextView) findViewById(R.id.Display_Message);
        AlreadyAMember = (MyTextView) findViewById(R.id.AlreadyAMember);

        OnlineChat = (ImageButton) findViewById(R.id.OnlineChat);

        Email = (MyEditText) findViewById(R.id.User_Name);
        Password = (MyPasswordText) findViewById(R.id.Password);
        ConfirmPassword = (MyPasswordText) findViewById(R.id.ConfirmPassword);

        LoginImage = (ImageView) findViewById(R.id.Login_Image);
        PasswordImage = (ImageView) findViewById(R.id.Password_Image);
        ConfirmPasswordImage = (ImageView) findViewById(R.id.ConfirmPassword_Image);

        Message = (LinearLayout) findViewById(R.id.Display_Message_Frame);
        Register = (MyButton) findViewById(R.id.Register);
        VerifyEmail = (MyButton) findViewById(R.id.VerifyEmail);

        Loading = (RelativeLayout) findViewById(R.id.loading);
        Loading_Text = (MyTextView) findViewById(R.id.Loading_Text);
        ResendEmail = (MyTextView) findViewById(R.id.ResendEmail);

        Resend_table = (TableRow) findViewById(R.id.Resend_table);

        ShowPassword = (ImageButton) findViewById(R.id.ShowPassword);
        ShowPassword.setOnTouchListener(commun.ShowPassword(Password, ShowPassword));

        ShowPasswordConfirmation = (ImageButton) findViewById(R.id.ShowPasswordConfirmation);
        ShowPasswordConfirmation.setOnTouchListener(commun.ShowPassword(ConfirmPassword, ShowPasswordConfirmation));

        //region EditText Listeners
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

        Password.addTextChangedListener(new TextWatcher()
        {
            @Override
            public void beforeTextChanged(CharSequence s, int start, int count, int after) {}

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count)
            {
                Validate_Password();
                Show_Password();
            }

            @Override
            public void afterTextChanged(Editable s)
            {
                int imageid = UserPassword.equals("") ? R.drawable.password_disabled : R.drawable.password_enabled;
                PasswordImage.setImageResource(imageid);
            }
        });

        ConfirmPassword.addTextChangedListener(new TextWatcher()
        {
            @Override
            public void beforeTextChanged(CharSequence s, int start, int count, int after) {}

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count)
            {
                Validate_ConfirmedPassword();
                Show_PasswordConfirmation();
            }

            @Override
            public void afterTextChanged(Editable s)
            {
                int imageid = ConfirmPassword.equals("") ? R.drawable.password_disabled : R.drawable.password_enabled;
                ConfirmPasswordImage.setImageResource(imageid);
            }
        });
        //endregion

        OnlineChat.setOnClickListener(this);
        Register.setOnClickListener(this);
        VerifyEmail.setOnClickListener(this);
        SignIn.setOnClickListener(this);
        AlreadyAMember.setOnClickListener(this);
        ResendEmail.setOnClickListener(this);

        Resend_table.setVisibility(View.GONE);
    }
    //endregion

    //region EditText Actions
    private void Show_Password()
    {
        String UserPassword = Password.getText().toString();
        ShowPassword.setVisibility(UserPassword.equals("") ? View.INVISIBLE : View.VISIBLE);
    }

    private void Show_PasswordConfirmation()
    {
        String UserConfirmPassword = ConfirmPassword.getText().toString();
        ShowPasswordConfirmation.setVisibility(UserConfirmPassword.equals("") ? View.INVISIBLE : View.VISIBLE);
    }

    private void Validate_Email()
    {
        UserMail = Email.getText().toString().trim();
        EmailValidated = commun.VerityEmail(UserMail);
        Enable_Disable_Button();
    }

    private void Validate_Password()
    {
        UserPassword=Password.getText().toString();
        PasswordValidated = UserPassword.length() > 0;
        Enable_Disable_Button();
    }

    private void Validate_ConfirmedPassword()
    {
        UserConfirmedPassword = ConfirmPassword.getText().toString();
        PasswordConfirmationValidated = UserConfirmedPassword.length() > 0;
        Enable_Disable_Button();
    }

    private void Enable_Disable_Button()
    {
        Register.setEnabled(PasswordValidated && EmailValidated && PasswordConfirmationValidated  && UserPassword.length() == UserConfirmedPassword.length());
        VerifyEmail.setEnabled(PasswordValidated && EmailValidated && PasswordConfirmationValidated  && UserPassword.length() == UserConfirmedPassword.length());
    }
    //endregion

    //region OnClick
    @Override
    public void onClick(View v)
    {
        commun.HideKeyBoard();

        if (v == SignIn || v == AlreadyAMember)
        {
            BackToLogIn(1);
        }
        else if (v == Register)
        {
            ShowSignUpAlert(1);
        }
        else if (v == VerifyEmail)
        {
            ShowSignUpAlert(2);
        }
        else if (v == OnlineChat)
        {
            Intercom.client().displayMessenger();
        }
        else if(v == ResendEmail){
            ResendEmail();
        }
    }
    //endregion

    //region Back to Login Page
    private void BackToLogIn(int i)
    {

        if (i == 2)
        {
            Intent myIntent = new Intent(this, Login.class);
            myIntent.putExtra("Email", UserMail);
            myIntent.putExtra("Flag", "Register");
            startActivity(myIntent);
        }else{
            ComponentName callingComponent = getCallingActivity();

            if (callingComponent == null) //Activity not called for result.
            {
                Intent intent = new Intent(this, Login.class);
                intent.putExtra("Email", UserMail);
                startActivity(intent);
            }
            else //Activity called for result from the login class
            {
                Intent myIntent = new Intent();
                myIntent.putExtra("Email", UserMail);
                setResult(Activity.RESULT_OK, myIntent);
            }
        }
        finish();
    }
    //endregion

    //region Sign up
    private void ShowSignUpAlert(final int flag)
    {
        AlertDialog.Builder builder = new AlertDialog.Builder(MyActivity);
        AlertDialog alertDialog = null;

        View dialogView = getLayoutInflater().inflate(R.layout.terms_and_conditions_notification, null);
        TextView NotificationText = (TextView) dialogView.findViewById(R.id.TermsAndConditions);
        NotificationText.setText(Html.fromHtml( MyActivity.getResources().getString(R.string.TermsNotification)));
        NotificationText.setOnClickListener(new OnClickListener()
        {
            @Override
            public void onClick(View v)
            {
                MyActivity.startActivity(new Intent(MyActivity, Terms_And_Conditions.class));
            }
        });

        builder.setIcon(R.mipmap.alert_green);
        builder.setTitle(MyActivity.getResources().getString(R.string.TermsAndConditions));
        builder.setPositiveButton((MyActivity.getResources().getString(R.string.ACCEPT)), new DialogInterface.OnClickListener()
        {
            public void onClick(DialogInterface dialog, int whichButton)
            {

                SignUp(flag);
            }
        });
        builder.setNegativeButton(MyActivity.getResources().getString(R.string.CANCEL), null);
        builder.setView(dialogView);
        alertDialog = builder.create();
        alertDialog.show();
    }

    private void SignUp(final int flag)
    {
        if (!EmailValidated)
        {
            Message.setVisibility(View.VISIBLE);
            DisplayMessage.setEnabled(true);
            DisplayMessage.setText((getResources().getString(R.string.InvalidUserName)));
        }
        else if (!PasswordValidated)
        {
            Message.setVisibility(View.VISIBLE);
            DisplayMessage.setEnabled(true);
            DisplayMessage.setText((getResources().getString(R.string.InvalidPassword)));
        }
        else if (UserPassword.length() <6)
        {
            Message.setVisibility(View.VISIBLE);
            DisplayMessage.setEnabled(true);
            DisplayMessage.setText((getResources().getString(R.string.IncorrectPasswordLength)));
        }
        else if (!UserPassword.equals(UserConfirmedPassword))
        {
            Message.setVisibility(View.VISIBLE);
            DisplayMessage.setEnabled(true);
            DisplayMessage.setText((getResources().getString(R.string.PasswordsDoNotMatch)));
        }
        else
        {
            Loading_Text.setText(getString(R.string.SigningIn));
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

                        JSONObject obj = new JSONObject();
                        obj.put("login", UserMail);
                        obj.put("password", UserPassword);

                        //Verify email  flag == 2   ;  fct = flag
                        String fctid = "2";
                        String url = "registration/users";
                        if(flag == 1){ // Register
                            fctid = "1";
                            url = "registration/users/private";
                        }
                        conn.GetData(SIGNUP_CLASS_ID, fctid, url, "POST", obj.toString(), false, MyActivity);
                    }
                    catch (Exception e)
                    {
                        commun.Log(e.getMessage());
                        runOnUiThread(new Runnable()
                        {
                            @Override
                            public void run() {
                                Message.setVisibility(View.VISIBLE);
                                Loading.setVisibility(View.GONE);
                                DisplayMessage.setEnabled(true);
                                DisplayMessage.setText((getResources().getString(R.string.Error)));
                            }});
                    }
                }
            });
            thread.start();
        }
    }



    public void OnSignUpResult(String result, int flag)
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
                    Message.setVisibility(View.VISIBLE);
                    DisplayMessage.setEnabled(false);

                    if (flag == 2){ // flag ==2 is verify email
                        DisplayMessage.setText((getResources().getString(R.string.CheckYourEmailVerification)));
                        Resend_table.setVisibility(View.VISIBLE);  // toufic sleiman
                    }else{
                        BackToLogIn(2);
                    }
                }
                else if (code.equals("vpn.user-service.duplicate_name"))
                {
                    Message.setVisibility(View.VISIBLE);
                    DisplayMessage.setEnabled(true);
                    DisplayMessage.setText((getResources().getString(R.string.UserAlreadyExists)));
                }
                else
                {
                    Message.setVisibility(View.VISIBLE);
                    DisplayMessage.setEnabled(true);
                    DisplayMessage.setText((getResources().getString(R.string.Error)));
                    commun.Log("Sign up code= " + code);
                }
            }
            catch (Exception e)
            {
                commun.Log(e.getMessage());
                Message.setVisibility(View.VISIBLE);
                DisplayMessage.setEnabled(true);
                DisplayMessage.setText((getResources().getString(R.string.Error)));
            }
        }
    }


    public void ResendEmail()
    {
        if (!EmailValidated)
        {
            Message.setVisibility(View.VISIBLE);
            DisplayMessage.setEnabled(true);
            DisplayMessage.setText((getResources().getString(R.string.InvalidUserName)));
        }
        else
        {
            Loading_Text.setText(getString(R.string.ResendEmail));
            Loading.setVisibility(View.VISIBLE);
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

                        conn.GetData(RESEND_CONFIRM_EMAIL, "1", "registration/users/" + UserMail + "/resendConfirmEmail", "PUT", null, false, MyActivity);
                    }
                    catch (Exception e)
                    {
                        commun.Log(e.getMessage());
                        runOnUiThread(new Runnable()
                        {
                            @Override
                            public void run() {
                                Message.setVisibility(View.VISIBLE);
                                Loading.setVisibility(View.GONE);
                                DisplayMessage.setEnabled(true);
                                DisplayMessage.setText((getResources().getString(R.string.Error)));
                            }});
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
                    Resend_table.setVisibility(View.GONE);
                    Message.setVisibility(View.VISIBLE);
                    DisplayMessage.setEnabled(false);
                    DisplayMessage.setText((getResources().getString(R.string.ReCheckYourEmailVerification)));

                    //Toufic 3/1/2018 -- google analytics --
                    MyApplication.getInstance().trackEvent(SignUpsCategory, ResendAction, ResendAction);
                    //Toufic 3/1/2018
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
                commun.Log(e.getMessage());
                Message.setVisibility(View.VISIBLE);
                DisplayMessage.setEnabled(true);
                DisplayMessage.setText((getResources().getString(R.string.Error)));
            }
        }
    }
    //endregion
}
