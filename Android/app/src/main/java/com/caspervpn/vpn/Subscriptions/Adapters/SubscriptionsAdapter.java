package com.caspervpn.vpn.Subscriptions.Adapters;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import com.caspervpn.vpn.R;
import com.caspervpn.vpn.Subscriptions.Models.Subcription;
import android.content.Context;

//import com.caspervpn.vpn.classes.Subscription;
import com.caspervpn.vpn.helper.MyButton;
import com.caspervpn.vpn.helper.MyTextView;


import java.util.List;

public class SubscriptionsAdapter extends RecyclerView.Adapter<SubscriptionsAdapter.ViewHolder> {

    private Context context;

    private List<Subcription> list;

    public SubscriptionsAdapter(Context context, List<Subcription> list) {
        this.context = context;
        this.list = list;
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup viewGroup, int i) {
        View v = LayoutInflater.from(context).inflate(R.layout.subscription_row,viewGroup,false);
        return new ViewHolder(v);
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder viewHolder, int i) {
        com.caspervpn.vpn.Subscriptions.Models.Subcription s = list.get(i);
        viewHolder.package_name.setText(s.getSubscriptionName());
        viewHolder.package_devices.setText(Integer.toString(s.getMaxConnections()));
        viewHolder.package_price.setText(s.getPricestring());
        viewHolder.package_offer.setText(s.getPackage_offer());
        viewHolder.package_saving.setText(s.getPackage_saving());
        viewHolder.package_button.setText("Subscribe");


    }

    @Override
    public int getItemCount() {
        return 0;
    }

    public class ViewHolder extends RecyclerView.ViewHolder{

        public MyButton package_button;
        public MyTextView package_name,package_devices,package_price,package_offer,package_saving;

        public ViewHolder(@NonNull View itemView) {
            super(itemView);
            package_button = itemView.findViewById(R.id.package_button);
            package_name= itemView.findViewById(R.id.package_name);
            package_devices= itemView.findViewById(R.id.package_devices);
            package_price= itemView.findViewById(R.id.package_price);
            package_offer= itemView.findViewById(R.id.package_offer);
            package_saving= itemView.findViewById(R.id.package_saving);

        }
    }
}
