package com.caspervpn.vpn.classes;

import java.util.ArrayList;

/**
 * Created by toufics on 10/25/2017.
 */

public class SusbcriptionDataList {
    ArrayList<SubscriptionData> SubscriptionDataList;

    public SusbcriptionDataList(ArrayList<SubscriptionData> SubscriptionDataList) {
        this.SubscriptionDataList = SubscriptionDataList;
    }

    public ArrayList<SubscriptionData> getSubscriptionDataList() {
        return SubscriptionDataList;
    }

    public void setSubscriptionDataList(ArrayList<SubscriptionData> SubscriptionDataList) {
        this.SubscriptionDataList = SubscriptionDataList;
    }
}
