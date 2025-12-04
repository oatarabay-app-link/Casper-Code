package com.caspervpn.vpn.screens;

import android.app.Activity;
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
import com.caspervpn.vpn.helper.MyPasswordText;
import com.caspervpn.vpn.helper.MyTextView;

import org.json.JSONObject;

import static com.caspervpn.vpn.R.id.back;
import static com.caspervpn.vpn.common.Configuration.CHANGE_PASSWORD_CLASS_ID;
import static com.caspervpn.vpn.common.Configuration.user;

public class Change_Password extends FragmentActivity implements OnClickListener {
    MyTextView DisplayMessage, Loading_Text, ResetPassword, CantRemember;
    MyPasswordText OldPassword, NewPassword, ConfirmPassword;
    ImageView OldPasswordImage, NewPasswordImage, ConfirmPasswordImage;
    ImageButton Back_Btn;
    ImageButton ShowOldPassword, ShowNewPassword, ShowPasswordConfirmation;
    LinearLayout Message;
    MyButton Save;
    RelativeLayout Loading;

    boolean OldPasswordValidated = false, NewPasswordValidated = false, PasswordConfirmationValidated = false;
    String OldUserPassword = "", NewUserPassword = "", UserConfirmedPassword = "";

    Commun commun;
    DataConnection conn;
    Activity MyActivity;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.change_password);
        commun = new Commun(this);
        conn = new DataConnection(this);
        MyActivity = this;

        Init();

    }

    private void Init() {
        DisplayMessage = (MyTextView) findViewById(R.id.Display_Message);
        ResetPassword = (MyTextView) findViewById(R.id.ResetPassword);
        CantRemember = (MyTextView) findViewById(R.id.CantRemember);

        OldPassword = (MyPasswordText) findViewById(R.id.OldPassword);
        NewPassword = (MyPasswordText) findViewById(R.id.NewPassword);
        ConfirmPassword = (MyPasswordText) findViewById(R.id.ConfirmPassword);

        OldPasswordImage = (ImageView) findViewById(R.id.OldPassword_Image);
        NewPasswordImage = (ImageView) findViewById(R.id.NewPassword_Image);
        ConfirmPasswordImage = (ImageView) findViewById(R.id.ConfirmPassword_Image);

        Message = (LinearLayout) findViewById(R.id.Display_Message_Frame);
        Save = (MyButton) findViewById(R.id.Save);

        Back_Btn = (ImageButton) findViewById(back);

        Loading = (RelativeLayout) findViewById(R.id.loading);
        Loading_Text = (MyTextView) findViewById(R.id.Loading_Text);

        ShowOldPassword = (ImageButton) findViewById(R.id.ShowOldPassword);
        ShowOldPassword.setOnTouchListener(commun.ShowPassword(OldPassword, ShowOldPassword));

        ShowNewPassword = (ImageButton) findViewById(R.id.ShowNewPassword);
        ShowNewPassword.setOnTouchListener(commun.ShowPassword(NewPassword, ShowNewPassword));

        ShowPasswordConfirmation = (ImageButton) findViewById(R.id.ShowPasswordConfirmation);
        ShowPasswordConfirmation.setOnTouchListener(commun.ShowPassword(ConfirmPassword, ShowPasswordConfirmation));

        OldPassword.addTextChangedListener(new TextWatcher() {
            @Override
            public void beforeTextChanged(CharSequence s, int start, int count, int after) {}

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count)
            {
                Validate_OldPassword();
                Show_OldPassword();
            }

            @Override
            public void afterTextChanged(Editable s) {
                int imageid = OldUserPassword.equals("") ? R.drawable.password_disabled : R.drawable.password_enabled;
                OldPasswordImage.setImageResource(imageid);
            }
        });

        NewPassword.addTextChangedListener(new TextWatcher() {
            @Override
            public void beforeTextChanged(CharSequence s, int start, int count, int after) {}

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count)
            {
                Validate_Password();
                Show_NewPassword();
            }

            @Override
            public void afterTextChanged(Editable s) {
                int imageid = NewUserPassword.equals("") ? R.drawable.password_disabled : R.drawable.password_enabled;
                NewPasswordImage.setImageResource(imageid);
            }
        });

        ConfirmPassword.addTextChangedListener(new TextWatcher() {
            @Override
            public void beforeTextChanged(CharSequence s, int start, int count, int after) {}

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count)
            {
                Validate_ConfirmedPassword();
                Show_PasswordConfirmation();
            }

            @Override
            public void afterTextChanged(Editable s) {
                int imageid = ConfirmPassword.equals("") ? R.drawable.password_disabled : R.drawable.password_enabled;
                ConfirmPasswordImage.setImageResource(imageid);
            }
        });

        CantRemember.setOnClickListener(this);
        ResetPassword.setOnClickListener(this);
        Save.setOnClickListener(this);
        Back_Btn.setOnClickListener(this);
    }


    private void Show_OldPassword()
    {
        String UserPassword = OldPassword.getText().toString();
        ShowOldPassword.setVisibility(UserPassword.equals("") ? View.INVISIBLE : View.VISIBLE);
    }

    private void Show_NewPassword()
    {
        String UserPassword = NewPassword.getText().toString();
        ShowNewPassword.setVisibility(UserPassword.equals("") ? View.INVISIBLE : View.VISIBLE);
    }

    private void Show_PasswordConfirmation()
    {
        String UserPassword = ConfirmPassword.getText().toString();
        ShowPasswordConfirmation.setVisibility(UserPassword.equals("") ? View.INVISIBLE : View.VISIBLE);
    }


    private void Validate_OldPassword() {
        OldUserPassword = OldPassword.getText().toString();
        OldPasswordValidated = OldUserPassword.length() > 0;
        Enable_Disable_Button();
    }

    private void Validate_Password() {
        NewUserPassword = NewPassword.getText().toString();
        NewPasswordValidated = NewUserPassword.length() > 0;
        Enable_Disable_Button();
    }

    private void Validate_ConfirmedPassword() {
        UserConfirmedPassword = ConfirmPassword.getText().toString();
        PasswordConfirmationValidated = UserConfirmedPassword.length() > 0;
        Enable_Disable_Button();
    }

    private void Enable_Disable_Button() {
        Save.setEnabled(OldPasswordValidated && NewPasswordValidated && PasswordConfirmationValidated && NewUserPassword.length() == UserConfirmedPassword.length());
    }

    @Override
    public void onClick(View v) {
        commun.HideKeyBoard();

        if (v.getId() == R.id.Save) {
            SavePassword();
        } else if (v.getId() == R.id.back) {
            finish();
        } else if (v.getId() == R.id.ResetPassword || v.getId() == R.id.CantRemember)
        {
            ResetPassword();
        }
    }


    private void ResetPassword() {
        Loading_Text.setText(getString(R.string.ResettingYourPassword));
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

                    conn.GetData(CHANGE_PASSWORD_CLASS_ID, "2", "registration/users/" + user.getEmail() + "/resetpassword", "POST", null, false, MyActivity);
                } catch (Exception e) {
                    commun.Log(e.getMessage());
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

    public void OnResetResult(String result) {
        Loading.setVisibility(View.GONE);

        if (result == null) {
            DisplayMessage.setEnabled(true);
            Message.setVisibility(View.VISIBLE);
            DisplayMessage.setText((getResources().getString(R.string.Error)));
        } else {
            try {
                JSONObject j = new JSONObject(result);
                String code = j.getString("code");
                if (code.equals("success")) {
                    DisplayMessage.setEnabled(false);
                    Message.setVisibility(View.VISIBLE);
                    DisplayMessage.setText((getResources().getString(R.string.CheckYourEmailReset)));
                } else if (code.equals("vpn.user-service.not_found")) {
                    DisplayMessage.setEnabled(true);
                    Message.setVisibility(View.VISIBLE);
                    DisplayMessage.setText((getResources().getString(R.string.UserNotFound)));
                } else {
                    DisplayMessage.setEnabled(true);
                    Message.setVisibility(View.VISIBLE);
                    DisplayMessage.setText((getResources().getString(R.string.Error)));
                }
            } catch (Exception e) {
                commun.Log(e.getMessage());
                DisplayMessage.setEnabled(true);
                Message.setVisibility(View.VISIBLE);
                DisplayMessage.setText((getResources().getString(R.string.Error)));
            }
        }
    }


    private void SavePassword() {
        if (!OldPasswordValidated) {
            DisplayMessage.setEnabled(true);
            Message.setVisibility(View.VISIBLE);
            DisplayMessage.setText((getResources().getString(R.string.InvalidOldPassword)));
        } else if (!NewPasswordValidated) {
            DisplayMessage.setEnabled(true);
            Message.setVisibility(View.VISIBLE);
            DisplayMessage.setText((getResources().getString(R.string.InvalidNewPassword)));
        } else if (NewUserPassword.length() < 6) {
            DisplayMessage.setEnabled(true);
            Message.setVisibility(View.VISIBLE);
            DisplayMessage.setText((getResources().getString(R.string.IncorrectPasswordLength)));
        } else if (!NewUserPassword.equals(UserConfirmedPassword)) {
            DisplayMessage.setEnabled(true);
            Message.setVisibility(View.VISIBLE);
            DisplayMessage.setText((getResources().getString(R.string.PasswordsDoNotMatch)));
        } else {
            Loading_Text.setText(R.string.Saving);
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

                        JSONObject obj = new JSONObject();
                        obj.put("oldPassword", OldUserPassword);
                        obj.put("newPassword", NewUserPassword);

                        conn.GetData(CHANGE_PASSWORD_CLASS_ID, "1", "users/changepass", "PUT", obj.toString(), true, MyActivity);
                    } catch (Exception e) {
                        commun.Log(e.getMessage());
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


    public void OnChangePasswordResult(String result) {
        Loading.setVisibility(View.GONE);

        if (result == null) {
            DisplayMessage.setEnabled(true);
            Message.setVisibility(View.VISIBLE);
            DisplayMessage.setText(getResources().getString(R.string.CheckYourInternet));
        } else {
            Message.setVisibility(View.INVISIBLE);
            DisplayMessage.setText("");
            try {
                JSONObject j = new JSONObject(result);
                String code = j.getString("code");

                if (code.equals("success")) {
                    DisplayMessage.setEnabled(false);
                    Message.setVisibility(View.VISIBLE);
                    DisplayMessage.setText((getResources().getString(R.string.PasswordChanged)));
                } else {
                    DisplayMessage.setEnabled(true);
                    Message.setVisibility(View.VISIBLE);
                    DisplayMessage.setText((getResources().getString(R.string.Error)));
                }
            } catch (Exception e) {
                commun.Log(e.getMessage());
                DisplayMessage.setEnabled(true);
                Message.setVisibility(View.VISIBLE);
                DisplayMessage.setText((getResources().getString(R.string.Error)));
                e.printStackTrace();
            }
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
