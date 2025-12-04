package com.caspervpn.vpn.screens;

import android.app.Activity;
import android.os.Bundle;
import androidx.appcompat.app.AppCompatActivity;
import android.view.View;
import android.widget.ImageButton;
import android.widget.RelativeLayout;

import com.caspervpn.vpn.R;
import com.caspervpn.vpn.classes.Server;
import com.caspervpn.vpn.classes.Server_Array;
import com.caspervpn.vpn.common.Commun;
import com.caspervpn.vpn.common.Configuration;
import com.caspervpn.vpn.common.DataConnection;
import com.caspervpn.vpn.helper.MyApplication;
import com.caspervpn.vpn.helper.MyTextView;
import com.caspervpn.vpn.helper.PullToRefresh;
import com.caspervpn.vpn.helper.PullToRefresh.OnRefreshListener;
import com.google.gson.Gson;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;

import static com.caspervpn.vpn.common.Configuration.SERVER_LIST_CLASS_ID;
import static com.caspervpn.vpn.common.Configuration.ServerListScreenName;
import static com.caspervpn.vpn.common.Configuration.servers;

/**
 * Created by zaherZ on 1/28/2017.
 */

public class Server_List extends AppCompatActivity implements View.OnClickListener
{
    ArrayList<Server> ServerList;
    PullToRefresh listView;
    RelativeLayout Loading;
    MyTextView Loading_Text, Message;
    private static Server_List_Adapter adapter;
    Activity MyActivity;
    ImageButton Back_Btn;
    Commun commun;
    DataConnection conn;
    Boolean ShowLoading = true;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.server_list);
        MyActivity = this;
        commun = new Commun(MyActivity);
        conn = new DataConnection(this);

        Configuration.ServerListInstance = this;

        Init();

        servers = new Gson().fromJson(commun.LoadClassFromPreference("ServerList"), Server_Array.class);
        if (servers != null) {
            adapter = new Server_List_Adapter(servers.getServers(), getApplicationContext(), MyActivity);
            listView.setAdapter(adapter);
            ShowLoading = false;
        }

        //Toufic 3/1/2018 -- google analytics --
        MyApplication.getInstance().trackScreenView(ServerListScreenName);
        //Toufic 3/1/2018
    }

    private void Init()
    {
        Back_Btn = (ImageButton) findViewById(R.id.back);
        listView=(PullToRefresh) findViewById(R.id.server_list);
        Loading = (RelativeLayout) findViewById(R.id.loading);
        Message = (MyTextView) findViewById(R.id.server_list_message);
        Loading_Text = (MyTextView) findViewById(R.id.Loading_Text);
        Loading_Text.setText(getString(R.string.Loading));

        Message.setOnClickListener(this);
        Back_Btn.setOnClickListener(this);

        listView.setOnRefreshListener(new OnRefreshListener()
        {
            @Override
            public void onRefresh()
            {
                ShowLoading = false;
                GetServers();
            }
        });
    }

    private void GetServers()
    {
        if (ShowLoading) Loading.setVisibility(View.VISIBLE);
        Message.setVisibility(View.INVISIBLE);

        Thread thread = new Thread(new Runnable()
        {
            @Override
            public void run()
            {
                try
                {

                    if (!commun.isNetworkConnected())
                    {
                        listView.onRefreshComplete();
                        commun.ShowConnectionDialog();

                        if (adapter == null)
                        {
                            Message.setVisibility(View.VISIBLE);
                            Message.setText(getResources().getString(R.string.ConnectionErrorClickToRefresh));
                        }
                        return;
                    }

                    conn.GetData(SERVER_LIST_CLASS_ID, "1", "vpn/servers/foruser", "GET", null, true, MyActivity);
                }
                catch (Exception e)
                {
                    runOnUiThread(new Runnable()
                    {
                        @Override
                        public void run()
                        {
                            listView.onRefreshComplete();
                            Message.setVisibility(View.VISIBLE);
                            Message.setText(getResources().getString(R.string.ConnectionErrorClickToRefresh));
                            Loading.setVisibility(View.GONE);
                            listView.onRefreshComplete();
                        }
                    });
                    e.printStackTrace();
                }
            }
        });

        thread.start();

    }


    @Override
    public void onClick(View v)
    {
        if (v == Back_Btn)
        {
            this.finish();
        }
        else if (v == Message)
        {
            Message.setVisibility(View.INVISIBLE);
            ShowLoading = true;
            GetServers();
        }
    }

    public void OnGetServersResult(String result)
    {
        Loading.setVisibility(View.GONE);
        listView.onRefreshComplete();

        if (result == null && adapter == null)
        {
            Message.setVisibility(View.VISIBLE);
            Message.setText(getResources().getString(R.string.ConnectionErrorClickToRefresh));
        }
        else
        {
            Message.setVisibility(View.INVISIBLE);
            try
            {
                JSONObject jsonObj  = new JSONObject(result);
                String code = jsonObj.getString("code");

                if (code.equals("success"))
                {


                    JSONArray serverList  = jsonObj.getJSONArray("data");
                    if (serverList.length() > 0)
                    {
                        ServerList = commun.SaveServer(serverList);

                        adapter = new Server_List_Adapter(ServerList, getApplicationContext(), MyActivity);

                        listView.setAdapter(adapter);
                    }
                    else
                    {
                        ServerList = null;
                        listView.setAdapter(null);
                        Message.setVisibility(View.VISIBLE);
                        Message.setText(getResources().getString(R.string.NoDataAvailable));
                    }
                }
                else
                {
                    ServerList = null;
                    listView.setAdapter(null);
                    Message.setVisibility(View.VISIBLE);
                    Message.setText(getResources().getString(R.string.ConnectionErrorClickToRefresh));
                }


            }
            catch (Exception e)
            {
                ServerList = null;
                listView.setAdapter(null);
                Message.setVisibility(View.VISIBLE);
                Message.setText(getResources().getString(R.string.ConnectionErrorClickToRefresh));
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