package com.caspervpn.vpn.screens;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.LinearLayout;

import com.caspervpn.vpn.R;
import com.caspervpn.vpn.classes.Server;
import com.caspervpn.vpn.helper.MyTextView;

import java.util.ArrayList;

import static com.caspervpn.vpn.common.Configuration.ServerListSelectedServer;

/**
 * Created by zaherZ on 1/28/2017.
 */

public class Server_List_Adapter extends ArrayAdapter<Server> implements View.OnClickListener
{
    private ArrayList<Server> dataSet;
    Context mContext;
    Activity MyActivity;

    // View lookup cache
    private static class ViewHolder
    {
        ImageView Server_Flag;
        MyTextView Server_Name;
        MyTextView Server_IP;
        ImageView Server_Health;
        LinearLayout Server_Item;
    }

    public Server_List_Adapter(ArrayList<Server> data, Context context, Activity activity)
    {
        super(context, R.layout.list_row_server, data);
        this.dataSet = data;
        this.mContext=context;
        this.MyActivity = activity;
    }

    @Override
    public void onClick(View v)
    {
        int position=(Integer) v.getTag();
        Object object= getItem(position);
        ServerListSelectedServer = (Server)object;

        Intent ServerDetail = new Intent(MyActivity, Server_Detail.class);
        MyActivity.startActivity(ServerDetail);
    }

    private int lastPosition = -1;

    @Override
    public View getView(int position, View convertView, ViewGroup parent)
    {
        // Get the data item for this position
        Server dataModel = getItem(position);
        // Check if an existing view is being reused, otherwise inflate the view
        ViewHolder viewHolder; // view lookup cache stored in tag

        //final View result;

        //if (convertView == null)
        //{
            viewHolder = new ViewHolder();
            LayoutInflater inflater = LayoutInflater.from(getContext());
            convertView = inflater.inflate(R.layout.list_row_server, parent, false);
            viewHolder.Server_Flag = (ImageView) convertView.findViewById(R.id.Server_Flag);
            viewHolder.Server_Health = (ImageView) convertView.findViewById(R.id.Server_Health);
            viewHolder.Server_Name = (MyTextView) convertView.findViewById(R.id.Server_Name);
            viewHolder.Server_IP = (MyTextView) convertView.findViewById(R.id.Server_IP);
            viewHolder.Server_Item = (LinearLayout) convertView.findViewById(R.id.Server_Item);
            viewHolder.Server_Item.setTag(position);

            //result=convertView;

          //  convertView.setTag(viewHolder);
        //}
       // else
        //{
        //    viewHolder = (ViewHolder) convertView.getTag();
            //result=convertView;
        //}

//        Animation animation = AnimationUtils.loadAnimation(mContext, (position > lastPosition) ? R.anim.up_from_bottom : R.anim.down_from_top);
//        result.startAnimation(animation);
       // lastPosition = position;

        viewHolder.Server_Health.setImageResource(Get_Health_Level(dataModel));
        viewHolder.Server_Flag.setImageResource(R.mipmap.fr);
        viewHolder.Server_Name.setText(dataModel.getServerName());
        viewHolder.Server_IP.setText(dataModel.getServerIp());

        viewHolder.Server_Item.setOnClickListener(this);

        return convertView;
    }

    private int Get_Health_Level(Server dataModel)
    {
        double health = dataModel.getSystemInfo().getHelathPercent();
        if (health < 20)
            return R.drawable.level_1;
        else if (health < 40)
            return R.drawable.level_2;
        else if (health < 60)
            return R.drawable.level_3;
        else if (health < 80)
            return R.drawable.level_4;
        else
            return R.drawable.level_5;
    }
}
