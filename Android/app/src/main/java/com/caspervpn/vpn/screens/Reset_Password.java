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
import android.view.View.OnClickListener;
import android.widget.ImageButton;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;

import com.caspervpn.vpn.R;
import com.caspervpn.vpn.common.Commun;
import com.caspervpn.vpn.common.DataConnection;
import com.caspervpn.vpn.helper.MyButton;
import com.caspervpn.vpn.helper.MyPasswordText;
import com.caspervpn.vpn.helper.MyTextView;

import org.json.JSONObject;

import java.util.Map;

import static com.caspervpn.vpn.common.Configuration.RESET_PASSWORD_CLASS_ID;


public class Reset_Password extends FragmentActivity implements OnClickListener
{
    MyTextView DisplayMessage, SignIn, GoBackTo, Loading_Text, GoBack;
    LinearLayout GoBackFrame, BackToSignInFrame;
    MyPasswordText NewPassword, ConfirmPassword;
    ImageView NewPasswordImage, ConfirmPasswordImage;
    ImageButton ShowPassword, ShowPasswordConfirmation;
    LinearLayout Message;
    MyButton Save;
    RelativeLayout Loading;

    boolean PasswordValidated = false, PasswordConfirmationValidated = false;
    String NewUserPassword = "", UserConfirmedPassword = "", UserMail, Check;

    Commun commun;
    DataConnection conn;
    Activity MyActivity;
    SharedPreferences prefs;

    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.reset_password);
        commun = new Commun (this);
        conn = new DataConnection(this);
        MyActivity = this;

        prefs = PreferenceManager.getDefaultSharedPreferences(this);

        Init();
    }

    @Override
    protected void onResume()
    {
        super.onResume();

        try
        {
            //http://vpn.host/resetPassword?login=zaher.zohbi@hotmail.com&check=f63b9f8a-76db-41e9-b0dd-4c6f95dbac21
            String link = this.getIntent().getDataString();
            Map<String, String> Params = commun.readParamsIntoMap(link);
            UserMail = Params.get("login");
            Check = Params.get("check");
        }
        catch (Exception e)
        {
            commun.Log(e.getMessage());
            Message.setVisibility(View.VISIBLE);
            DisplayMessage.setText((getResources().getString(R.string.InvalidPassword)));
        }
    }


    private void Init()
    {
        DisplayMessage = (MyTextView) findViewById(R.id.Display_Message);
        SignIn = (MyTextView) findViewById(R.id.Sign_In);
        GoBackTo = (MyTextView) findViewById(R.id.GoBackTo);
        GoBack = (MyTextView) findViewById(R.id.GoBack);

        NewPassword = (MyPasswordText) findViewById(R.id.NewPassword);
        ConfirmPassword = (MyPasswordText) findViewById(R.id.ConfirmPassword);

        NewPasswordImage = (ImageView) findViewById(R.id.NewPassword_Image);
        ConfirmPasswordImage = (ImageView) findViewById(R.id.ConfirmPassword_Image);

        Message = (LinearLayout) findViewById(R.id.Display_Message_Frame);
        Save = (MyButton) findViewById(R.id.Save);

        Loading = (RelativeLayout) findViewById(R.id.loading);
        Loading_Text = (MyTextView) findViewById(R.id.Loading_Text);

        GoBackFrame = (LinearLayout) findViewById(R.id.GoBackFrame);
        BackToSignInFrame = (LinearLayout) findViewById(R.id.BackToSignInFrame);

        if (prefs.getBoolean("Islogin", false))
        {
            BackToSignInFrame.setVisibility(View.GONE);
            GoBackFrame.setVisibility(View.VISIBLE);
        }
        else
        {
            GoBackFrame.setVisibility(View.GONE);
            BackToSignInFrame.setVisibility(View.VISIBLE);
        }

        GoBack.setOnClickListener(this);
        SignIn.setOnClickListener(this);
        GoBackTo.setOnClickListener(this);
        Save.setOnClickListener(this);

        ShowPassword = (ImageButton) findViewById(R.id.ShowPassword);
        ShowPassword.setOnTouchListener(commun.ShowPassword(NewPassword, ShowPassword));

        ShowPasswordConfirmation = (ImageButton) findViewById(R.id.ShowPasswordConfirmation);
        ShowPasswordConfirmation.setOnTouchListener(commun.ShowPassword(ConfirmPassword, ShowPasswordConfirmation));

        NewPassword.addTextChangedListener(new TextWatcher()
        {
            @Override
            public void beforeTextChanged(CharSequence s, int start, int count, int after) {}

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count)
            {
                Validate_NewPassword();
                Show_Password();
            }

            @Override
            public void afterTextChanged(Editable s)
            {
                int imageid = NewUserPassword.equals("") ? R.drawable.password_disabled : R.drawable.password_enabled;
                NewPasswordImage.setImageResource(imageid);
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
    }

    private void Show_Password()
    {
        String UserPassword = NewPassword.getText().toString();
        ShowPassword.setVisibility(UserPassword.equals("") ? View.INVISIBLE : View.VISIBLE);
    }


    private void Show_PasswordConfirmation()
    {
        String UserPassword = ConfirmPassword.getText().toString();
        ShowPasswordConfirmation.setVisibility(UserPassword.equals("") ? View.INVISIBLE : View.VISIBLE);
    }

    private void Validate_NewPassword()
    {
        NewUserPassword = NewPassword.getText().toString();
        PasswordValidated = NewUserPassword.length() > 0;
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
        Save.setEnabled(PasswordValidated && PasswordConfirmationValidated  && NewUserPassword.length() == UserConfirmedPassword.length());
    }

    @Override
    public void onClick(View v)
    {
        commun.HideKeyBoard();

        if (v.getId() == R.id.Sign_In || v.getId() == R.id.GoBackTo)
        {
            BackToLogin();
        }
        else if (v.getId() == R.id.Save)
        {
            Save_New_Password();
        }
        else if (v.getId() == R.id.GoBack)
        {
            BackToChangePassword();
        }
    }

    private void BackToChangePassword()
    {
        if (Change_Password.active)
        {
            Intent myIntent = new Intent(this, Change_Password.class);
            this.startActivity(myIntent);
            this.finish();
        }
        else if (Edit_Profile.active)
        {
            Intent myIntent = new Intent(this, Edit_Profile.class);
            this.startActivity(myIntent);
            this.finish();
        }
        else if (Landing_Page.active)
        {
            Intent myIntent = new Intent(this, Landing_Page.class);
            this.startActivity(myIntent);
            this.finish();
        }
        else if (Server_List.active)
        {
            Intent myIntent = new Intent(this, Server_List.class);
            this.startActivity(myIntent);
            this.finish();
        }
        else if (Server_Detail.active)
        {
            Intent myIntent = new Intent(this, Server_Detail.class);
            this.startActivity(myIntent);
            this.finish();
        }
        else if (Subscribe.active)
        {
            Intent myIntent = new Intent(this, Subscribe.class);
            this.startActivity(myIntent);
            this.finish();
        }
        else if (Social_Media.active)
        {
            Intent myIntent = new Intent(this, Social_Media.class);
            this.startActivity(myIntent);
            this.finish();
        }
        else if (Terms_And_Conditions.active)
        {
            Intent myIntent = new Intent(this, Terms_And_Conditions.class);
            this.startActivity(myIntent);
            this.finish();
        }
        else if (Privacy_Policy.active)
        {
            Intent myIntent = new Intent(this, Privacy_Policy.class);
            this.startActivity(myIntent);
            this.finish();
        }
        else if (FAQ.active)
        {
            Intent myIntent = new Intent(this, FAQ.class);
            this.startActivity(myIntent);
            this.finish();
        }
        else if (About_CasperVPN.active)
        {
            Intent myIntent = new Intent(this, About_CasperVPN.class);
            this.startActivity(myIntent);
            this.finish();
        }

        else if (What_Is_CasperVPN.active)
        {
            Intent myIntent = new Intent(this, What_Is_CasperVPN.class);
            this.startActivity(myIntent);
            this.finish();
        }
    }

    private void BackToLogin()
    {
        if (Signup.MyActivity != null) Signup.MyActivity.finish();
        if (Forgot_Password.MyActivity != null) Forgot_Password.MyActivity.finish();

        if (!prefs.getBoolean("Islogin", false))
        {
            Intent myIntent = new Intent(this, Login.class);
            myIntent.putExtra("Email", UserMail);
            this.startActivity(myIntent);
        }
        else
        {
            commun.StartCasperVPNApplication();
        }

        this.finish();

    }

    private void Save_New_Password()
    {
        if (!PasswordValidated)
        {
            Message.setVisibility(View.VISIBLE);
            DisplayMessage.setText((getResources().getString(R.string.InvalidPassword)));
        }
        else if (NewUserPassword.length() <6)
        {
            Message.setVisibility(View.VISIBLE);
            DisplayMessage.setText((getResources().getString(R.string.IncorrectPasswordLength)));
        }
        else if (!NewUserPassword.equals(UserConfirmedPassword))
        {
            Message.setVisibility(View.VISIBLE);
            DisplayMessage.setText((getResources().getString(R.string.PasswordsDoNotMatch)));
        }
        else
        {
            Loading.setVisibility(View.VISIBLE);
            Loading_Text.setText(getString(R.string.Saving));
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
                        obj.put("resetPasswordUuid", Check);
                        obj.put("password", NewUserPassword);

                        conn.GetData(RESET_PASSWORD_CLASS_ID, "1", "registration/users/" + UserMail + "/resetpassword", "PUT", obj.toString(), false, MyActivity);
                    }
                    catch (Exception e)
                    {
                        commun.Log(e.getMessage());
                        runOnUiThread(new Runnable()
                        {
                            @Override
                            public void run()
                            {
                                Loading.setVisibility(View.GONE);
                                Message.setVisibility(View.VISIBLE);
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



    public void OnSaveNewPassword(String result)
    {
        Loading.setVisibility(View.GONE);

        if (result == null)
        {
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
                    BackToLogin();
                }
                else if (code.equals("vpn.user-service.not_found"))
                {
                    Message.setVisibility(View.VISIBLE);
                    DisplayMessage.setText((getResources().getString(R.string.InvalidResetLink)));
                }
                else if (code.equals("vpn.user-service.token_already_used"))
                {
                    Message.setVisibility(View.VISIBLE);
                    DisplayMessage.setText((getResources().getString(R.string.LinkAlreadyUsed)));
                }
                else if (code.equals("vpn.user-service.token_expired"))
                {
                    Message.setVisibility(View.VISIBLE);
                    DisplayMessage.setText((getResources().getString(R.string.LinkHasExpired)));
                }
                else
                {
                    Message.setVisibility(View.VISIBLE);
                    DisplayMessage.setText((getResources().getString(R.string.Error)));
                }
            }
            catch (Exception e)
            {
                commun.Log(e.getMessage());
                Message.setVisibility(View.VISIBLE);
                DisplayMessage.setText((getResources().getString(R.string.Error)));
            }
        }
    }
}
