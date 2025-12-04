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
import com.caspervpn.vpn.classes.User;
import com.caspervpn.vpn.common.Commun;
import com.caspervpn.vpn.common.Configuration;
import com.caspervpn.vpn.common.DataConnection;
import com.caspervpn.vpn.helper.MyApplication;
import com.caspervpn.vpn.helper.MyButton;
import com.caspervpn.vpn.helper.MyEditText;
import com.caspervpn.vpn.helper.MyPasswordText;
import com.caspervpn.vpn.helper.MyTextView;
import com.caspervpn.vpn.services.Check_Recurring_Subscription;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.Map;

import io.intercom.android.sdk.Intercom;

import static com.caspervpn.vpn.R.id.Sign_Up;
//import static com.caspervpn.vpn.common.Configuration.DefaultPassword;
//import static com.caspervpn.vpn.common.Configuration.DefaultUsername;
import static com.caspervpn.vpn.common.Configuration.LOGIN_CLASS_ID;
import static com.caspervpn.vpn.common.Configuration.LoginScreenName;
import static com.caspervpn.vpn.common.Configuration.SignInAction;
import static com.caspervpn.vpn.common.Configuration.SignUpsCategory;
import static com.caspervpn.vpn.common.Configuration.UnconfirmedAction;
import static com.caspervpn.vpn.common.Configuration.user;
import static com.caspervpn.vpn.common.Configuration.userprofile;


public class Login extends FragmentActivity implements OnClickListener
{
    //region fields
    private Commun commun;
    private DataConnection conn;
    private static Activity MyActivity;

    private MyTextView DisplayMessage, ForgotPassword, SignUp, DontHaveAnAccount, Loading_Text, ResendEmail;
    private MyEditText Email;
    private MyPasswordText Password;
    private ImageView LoginImage, PasswordImage;
    private ImageButton ShowPassword, OnlineChat;
    private LinearLayout Message;
    private MyButton Login;
    private RelativeLayout Loading;

    private String UserMail = "", UserPassword = "";
    private Boolean EmailValidated = false, PasswordValidated = false;
    private final int  FORGOT_PASSWORD_REQUEST_CODE = 1, SIGN_UP_REQUEST_CODE = 2, RESEND_CONFIRM_EMAIL = 3;

    //endregion

    //region onCreate
    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.login);

        // Intercom Push Notification
        Intercom.client().handlePushMessage();

        //Toufic 7/3/2017 -- google analytics
        MyApplication.getInstance().trackScreenView(LoginScreenName);
        //Toufic 7/3/2017

        MyActivity = this;
        commun = new Commun(this);
        conn = new DataConnection(this);

        Init();

        if(Configuration.SetDefaultLoginCredentials)
        {
            Email.setSelection(Email.getText().length());
            Password.setSelection(Password.getText().length());
        }

    }
    //endregion

    //region onResume
    @Override
    protected void onResume()
    {
        super.onResume();

        //Referer: https://caspervpn.com/en/confirmEmail?userId=68d53b72-8916-440e-bdb4-b50952caf534&check=1eb9d931-5200-4e5c-a289-073e6d3ab918
        String link = this.getIntent().getDataString();
        if (link != null) verify (link);

        if (this.getIntent().hasExtra("Flag")){
            String flag = this.getIntent().getStringExtra("Flag");
            Email.setText(this.getIntent().getStringExtra("Email"));
            if (flag.equals("Register")){
                Message.setVisibility(View.VISIBLE);
                DisplayMessage.setEnabled(false);
                DisplayMessage.setText((getResources().getString(R.string.EmailVerified)));

                Email.setSelection(Email.getText().length());
                Password.setText("");
            }
        }
    }
    //endregion

    //region onNewIntent
    @Override
    protected void onNewIntent(Intent intent)
    {
        super.onNewIntent(intent);
        setIntent(intent);

        Email.setText(this.getIntent().getStringExtra("Email"));
        Email.setSelection(Email.getText().length());
        Password.setText("");

        Login.setEnabled(true);
    }
    //endregion

    //region Init
    private void Init()
    {
        DisplayMessage = (MyTextView) findViewById(R.id.Display_Message);
        ForgotPassword = (MyTextView) findViewById(R.id.Forgot_Password);
        DontHaveAnAccount = (MyTextView) findViewById(R.id.DontHaveAnAccount);
        SignUp = (MyTextView) findViewById(Sign_Up);

        Email = (MyEditText) findViewById(R.id.User_Name);
        Password = (MyPasswordText) findViewById(R.id.Password);

        LoginImage = (ImageView) findViewById(R.id.Login_Image);
        PasswordImage = (ImageView) findViewById(R.id.Password_Image);

        Message = (LinearLayout) findViewById(R.id.Display_Message_Frame);
        Login = (MyButton) findViewById(R.id.Login);

        Loading = (RelativeLayout) findViewById(R.id.loading);
        Loading_Text = (MyTextView) findViewById(R.id.Loading_Text);
        ResendEmail = (MyTextView) findViewById(R.id.ResendEmail);

        OnlineChat = (ImageButton) findViewById(R.id.OnlineChat);

        ShowPassword = (ImageButton) findViewById(R.id.ShowPassword);
        ShowPassword.setOnTouchListener(commun.ShowPassword(Password, ShowPassword));

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
            public void beforeTextChanged(CharSequence s, int start, int count, int after) { }

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
        //endregion

        OnlineChat.setOnClickListener(this);
        Login.setOnClickListener(this);
        SignUp.setOnClickListener(this);
        DontHaveAnAccount.setOnClickListener(this);
        ForgotPassword.setOnClickListener(this);
        ResendEmail.setOnClickListener(this);
    }
    //endregion

    //region EditText Actions
    private void Show_Password()
    {
        String UserPassword = Password.getText().toString();
        ShowPassword.setVisibility(UserPassword.equals("") ? View.INVISIBLE : View.VISIBLE);
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
        PasswordValidated = UserPassword.length() > 5;
        Enable_Disable_Button();
    }

    private void Enable_Disable_Button()
    {
        Login.setEnabled(PasswordValidated && EmailValidated);
    }
    //endregion

    //region onClick
    @Override
    public void onClick(View v)
    {
        commun.HideKeyBoard();

        if (v == Login)
        {
            Login();
        }
        else if (v == ForgotPassword)
        {
            ResetPassword();
        }
        else if (v == SignUp || v == DontHaveAnAccount)
        {
            SignUp();
        }
        else if (v == OnlineChat)
        {
            Intercom.client().displayMessenger();
        }
        else if (v == ResendEmail)
        {
            ResendEmail();
        }
    }
    //endregion

    //region Sign up
    private void SignUp()
    {
        Intent myIntent = new Intent(this, Signup.class);
        //myIntent.putExtra("Email", UserMail);
        this.startActivityForResult(myIntent, SIGN_UP_REQUEST_CODE);
    }
    //endregion

    //region Resend Confirm Email
    private void ResendEmail()
    {
        Intent myIntent = new Intent(this, resend_confirm_email.class);
        myIntent.putExtra("Email", UserMail);
        this.startActivityForResult(myIntent, RESEND_CONFIRM_EMAIL);
    }
    //endregion

    //region Reset Password
    private void ResetPassword()
    {
        Intent myIntent = new Intent(this, Forgot_Password.class);
        myIntent.putExtra("Email", UserMail);
        this.startActivityForResult(myIntent, FORGOT_PASSWORD_REQUEST_CODE);
    }
    //endregion

    //region Verify Email Confirmation
    private void verify(final String link)
    {
        Loading_Text.setText(getString(R.string.LoggingIn));
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

                    Map<String, String> Params = commun.readParamsIntoMap(link);
                    JSONObject obj = new JSONObject();
                    obj.put("userId", Params.get("userId"));
                    obj.put("confirmCode", Params.get("check"));

                    conn.GetData(LOGIN_CLASS_ID, "1", "registration/users/confirmEmail", "POST", obj.toString(), false, MyActivity);
                }
                catch (Exception e)
                {
                    commun.Log(e.toString());
                    runOnUiThread(new Runnable()
                    {
                        @Override
                        public void run()
                        {
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
    //endregion

    //region Login
    private void Login()
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
        else
        {
            Loading_Text.setText(getString(R.string.LoggingIn));
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

                        conn.GetData(LOGIN_CLASS_ID, "1", "auth/token", "POST", obj.toString(), false, MyActivity);
                    }
                    catch (Exception e)
                    {
                        commun.Log(e.toString());
                        runOnUiThread(new Runnable()
                        {
                            @Override
                            public void run()
                            {
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

    public void OnLoginResult(String result)
    {
        if (result == null)
        {
            commun.Log("On Login Result is null");
            Loading.setVisibility(View.GONE);
            Message.setVisibility(View.VISIBLE);
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
                    JSONObject data = j.getJSONObject("data");
                    JSONObject userInfo = data.getJSONObject("authInfo");

                    //String userid ,String username, String email, String password, String token, int tokenExpire
                    user = new User
                            (
                                    userInfo.getString("userId"),
                                    userInfo.getString("displayName"),
                                    userInfo.getString("displayName"),//Email
                                    data.getString("accessToken"),
                                    data.getString("refreshToken"),
                                    data.getLong("accessTokenExpire"),
                                    userInfo.getString("intHash"),
                                    userInfo.getString("intAppId")
                            );

                    commun.SaveClassToPreference(user, "user");

                    GetUserProfile();
                }
                else if (result.contains("Email not confirmed!"))
                {
                    Loading.setVisibility(View.GONE);
                    Message.setVisibility(View.VISIBLE);
                    DisplayMessage.setEnabled(true);
                    DisplayMessage.setText((getResources().getString(R.string.PleaseVerifyYourEmail)));

                    //Toufic sleiman 3/1/18 -- google analytics --
                    MyApplication.getInstance().trackEvent(SignUpsCategory, UnconfirmedAction, UnconfirmedAction);
                    //Toufic sleiman 3/1/18
                }
                else if (result.contains("Email already confirmed!"))
                {
                    Loading.setVisibility(View.GONE);
                    Message.setVisibility(View.VISIBLE);
                    DisplayMessage.setEnabled(true);
                    DisplayMessage.setText((getResources().getString(R.string.EmailAlreadyVerified)));
                }
                else if (result.contains("User is blocked"))
                {
                    Loading.setVisibility(View.GONE);
                    Message.setVisibility(View.VISIBLE);
                    DisplayMessage.setEnabled(true);
                    DisplayMessage.setText((getResources().getString(R.string.UserIsBlocked)));
                }
                else
                {
                    commun.Log("On Login Result: " + result);
                    Loading.setVisibility(View.GONE);
                    Message.setVisibility(View.VISIBLE);
                    DisplayMessage.setEnabled(true);
                    DisplayMessage.setText((getResources().getString(R.string.Invalid_Login_Credentials)));
                }


            }
            catch (Exception e)
            {
                commun.Log(e.toString());
                Loading.setVisibility(View.GONE);
                Message.setVisibility(View.VISIBLE);
                DisplayMessage.setText((getResources().getString(R.string.Error)));
                e.printStackTrace();
            }
        }
    }
    //endregion

    //region Get User Profile
    private void GetUserProfile()
    {
        Loading_Text.setText(getString(R.string.Loading));
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

                    conn.GetData(LOGIN_CLASS_ID, "2", "users/profile", "GET", null, true, MyActivity);
                }
                catch (Exception e)
                {
                    commun.Log(e.toString());
                    runOnUiThread(new Runnable()
                    {
                        @Override
                        public void run()
                        {
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

    public void OnUserProfileResult(String result)
    {
        if (result == null)
        {
            commun.Log("On User Profile Result is NULL");
            Loading.setVisibility(View.GONE);
            Message.setVisibility(View.VISIBLE);
            DisplayMessage.setText(getResources().getString(R.string.Error));
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
                    JSONObject userdata  = j.getJSONObject("data");
                    commun.SaveUserProfile(userdata);
                    Get_Servers();
                }
                else
                {
                    commun.Log("On User Profile Result: " + result);
                    Loading.setVisibility(View.GONE);
                    Message.setVisibility(View.VISIBLE);
                    DisplayMessage.setText((getResources().getString(R.string.Error)));
                }


            }
            catch (Exception e)
            {
                commun.Log(e.toString());
                Loading.setVisibility(View.GONE);
                Message.setVisibility(View.VISIBLE);
                DisplayMessage.setText((getResources().getString(R.string.Error)));
                e.printStackTrace();
            }
        }
    }
    //endregion

    //region Get Servers
    private void Get_Servers()
    {
        Loading_Text.setText(getString(R.string.Loading));
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

                    conn.GetData(LOGIN_CLASS_ID, "3", "vpn/servers/foruser", "GET", null, true, MyActivity);
                }
                catch (Exception e)
                {
                    commun.Log(e.toString());
                    runOnUiThread(new Runnable()
                    {
                        @Override
                        public void run()
                        {
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

    public void OnServerResult(String result)
    {
        Loading.setVisibility(View.GONE);

        if (result == null)
        {
            commun.Log("On Server Result is null");
            Message.setVisibility(View.VISIBLE);
            DisplayMessage.setText(getResources().getString(R.string.Error));
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
                    JSONArray serverList  = j.getJSONArray("data");
                    if (serverList.length() > 0)
                    {
                        commun.SaveServer(serverList);
                    }

                    //Toufic sleiman 3/1/18 -- google analytics --
                    if(userprofile != null) MyApplication.getInstance().trackEvent(SignUpsCategory, SignInAction, userprofile.getId());
                    //Toufic sleiman 3/1/18

                    Go_To_Check_Recurring_Subscription();
                }
                else
                {
                    commun.Log("On Server Result: " + result);
                    Message.setVisibility(View.VISIBLE);
                    DisplayMessage.setText((getResources().getString(R.string.Error)));
                }
            }
            catch (Exception e)
            {
                commun.Log(e.toString());
                Message.setVisibility(View.VISIBLE);
                DisplayMessage.setText((getResources().getString(R.string.Error)));
                e.printStackTrace();
            }
        }
    }
    //endregion

    //region Start Application
    private void Go_To_Check_Recurring_Subscription()
    {
        try {
            startService(new Intent(this, Check_Recurring_Subscription.class));
            Go_To_LandingPage();
        }catch (Exception e)
        {
            commun.Log(e.toString());
        }
    }
    private void Go_To_LandingPage()
    {
        commun.StartCasperVPNApplication();
    }
    //endregion

    //region OnActivityResult
    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data)
    {
        Message.setVisibility(View.INVISIBLE);
        DisplayMessage.setText("");

        if (requestCode == FORGOT_PASSWORD_REQUEST_CODE)
        {
            if(resultCode == Activity.RESULT_OK)
            {
                if (data != null)
                {
                    String EmailAddress = data.getStringExtra("Email");
                    Email.setText(EmailAddress);
                    Email.setSelection(Email.getText().length());
                }
            }
        }

        if (requestCode == SIGN_UP_REQUEST_CODE)
        {
            if(resultCode == Activity.RESULT_OK)
            {
                String EmailAddress = data.getStringExtra("Email");
                Email.setText(EmailAddress);
                Email.setSelection(Email.getText().length());
                Password.setText("");
            }
        }
    }
    //endregion
}