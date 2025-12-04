package com.caspervpn.vpn.screens;

import android.app.Activity;
import android.content.Intent;
import android.os.Build;
import android.os.Bundle;
import androidx.fragment.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;

import com.caspervpn.vpn.BuildConfig;
import com.caspervpn.vpn.R;
import com.caspervpn.vpn.common.Commun;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;

import io.intercom.android.sdk.Intercom;

import static com.caspervpn.vpn.common.Configuration.Support_Email;

//import com.jaredrummler.android.device.DeviceName;

public class Contact_Menu extends Fragment implements View.OnClickListener
{
    Activity MyActivity;
    Commun commun;

    LinearLayout ContactUs, OnlineChat, ReportBug, SendLogFile;

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState)
    {
        View view = inflater.inflate(R.layout.contactus_menu, container, false);

        MyActivity = getActivity();
        commun = new Commun(MyActivity);

        Init(view);

        return view;
    }

    private void Init(View v)
    {
        ContactUs = (LinearLayout) v.findViewById(R.id.ContactUs);
        OnlineChat = (LinearLayout) v.findViewById(R.id.OnlineChat);
        ReportBug = (LinearLayout) v.findViewById(R.id.ReportBug);
        SendLogFile = (LinearLayout) v.findViewById(R.id.SendLogFile);

        ContactUs.setOnClickListener(this);
        OnlineChat.setOnClickListener(this);
        ReportBug.setOnClickListener(this);
        SendLogFile.setOnClickListener(this);
    }




    @Override
    public void onClick(View v)
    {
        if (v == ContactUs)
        {
            Send_Email();
        }
        else if (v == OnlineChat)
        {
            Open_Chat();
        }
        else if (v == ReportBug)
        {
            Report_Bug();
        }
        else if (v == SendLogFile)
        {
            SendLogs();
        }

    }

    private void Report_Bug()
    {
//        DeviceName.with(MyActivity).request(new DeviceName.Callback()
//        {
//            @Override public void onFinished(DeviceName.DeviceInfo info, Exception error)
//            {
                String Body = "";
                Body += "Device Info:" + "\n";
                Body += "-------------------" + "\n";
                Body += "Device Code: " + android.os.Build.MODEL + "\n";                 // SM-G935A
                Body += "Device Type: " + android.os.Build.DEVICE + "\n";                // zeroflte
                Body += "Product Name: " + android.os.Build.PRODUCT + "\n" + "\n";       // zerofltexx

                Body += "Android Info:" + "\n";
                Body += "---------------------" + "\n";
                Body += "API Level: " + Build.VERSION.SDK + "\n";                           // 23
                Body += "Android Version: " + Build.VERSION.RELEASE + "\n";                 // 6.0.1
                Body += "Kernel Version: " + System.getProperty("os.version") + "\n";      // 3.10.61-9151931
                Body += "Firmware Version: " + Build.VERSION.INCREMENTAL + "\n" + "\n";     // G920FXXU5DQA3

                Body += "Application Info:" + "\n";
                Body += "--------------------------" + "\n";
                Body += "SDK Version Code: " + BuildConfig.VERSION_CODE + "\n";             // 5
                Body += "SDK Version Name: " + BuildConfig.VERSION_NAME + "\n" + "\n";      // 1.0.5

                Body += "Bug Description:" + "\n";
                Body += "--------------------------" + "\n";
                Body += "***ENTER YOUR TEXT HERE***";

                Intent intent = new Intent(Intent.ACTION_SEND);
                intent.setType("text/text");
                intent.putExtra(Intent.EXTRA_EMAIL, new String[] { Support_Email });
                intent.putExtra(Intent.EXTRA_SUBJECT, "CasperVPN Bug Report");
                intent.putExtra(Intent.EXTRA_TEXT, Body);
                startActivity(Intent.createChooser(intent, "Send Email"));
//            }
//        });
    }


    private void Send_Email()
    {
        Intent intent = new Intent(Intent.ACTION_SEND);
        intent.setType("text/text");
        intent.putExtra(Intent.EXTRA_EMAIL, new String[] { Support_Email });
        startActivity(Intent.createChooser(intent, "Send Email"));
    }

    private void Open_Chat()
    {

        Intercom.client().displayMessenger();
    }





    public void SendLogs()
    {
        //set a file
        String result = "";

        //write log to file
        int pid = android.os.Process.myPid();
        try {
            String command = String.format("logcat -d -v threadtime *:*");
            Process process = Runtime.getRuntime().exec(command);

            BufferedReader reader = new BufferedReader(new InputStreamReader(process.getInputStream()));
            StringBuilder resultbuilder = new StringBuilder();
            String currentLine = null;

            while ((currentLine = reader.readLine()) != null)
            {
                if (currentLine != null && currentLine.contains(String.valueOf(pid))&& currentLine.contains(String.valueOf(" E ")))
                {
                    resultbuilder.append(currentLine);
                    resultbuilder.append("\n");
                }
            }
            Runtime.getRuntime().exec("logcat -c");

            result = resultbuilder.toString();
        } catch (IOException e) {
            commun.Log(e.getMessage());}



        String Body = "";
        Body += "Device Info:" + "\n";
        Body += "-------------------" + "\n";
        Body += "Device Code: " + android.os.Build.MODEL + "\n";                 // SM-G935A
        Body += "Device Type: " + android.os.Build.DEVICE + "\n";                // zeroflte
        Body += "Product Name: " + android.os.Build.PRODUCT + "\n" + "\n";       // zerofltexx

        Body += "Android Info:" + "\n";
        Body += "---------------------" + "\n";
        Body += "API Level: " + Build.VERSION.SDK + "\n";                           // 23
        Body += "Android Version: " + Build.VERSION.RELEASE + "\n";                 // 6.0.1
        Body += "Kernel Version: " + System.getProperty("os.version") + "\n";      // 3.10.61-9151931
        Body += "Firmware Version: " + Build.VERSION.INCREMENTAL + "\n" + "\n";     // G920FXXU5DQA3

        Body += "Application Info:" + "\n";
        Body += "--------------------------" + "\n";
        Body += "SDK Version Code: " + BuildConfig.VERSION_CODE + "\n";             // 5
        Body += "SDK Version Name: " + BuildConfig.VERSION_NAME + "\n" + "\n";      // 1.0.5

        Body += "Application Logs:" + "\n";
        Body += "--------------------------" + "\n";
        Body += result;

        Intent intent = new Intent(Intent.ACTION_SEND);
        intent.setType("text/text");
        intent.putExtra(Intent.EXTRA_EMAIL, new String[] { Support_Email });
        intent.putExtra(Intent.EXTRA_SUBJECT, "CasperVPN Bug Report");
        intent.putExtra(Intent.EXTRA_TEXT, Body);
        startActivity(Intent.createChooser(intent, "Send Email"));
    }
}
