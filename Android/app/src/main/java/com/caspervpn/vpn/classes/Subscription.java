package com.caspervpn.vpn.classes;

/**
 * Created by zaherZ on 3/2/2017.
 */
public class Subscription
{
    private String subscriptionId, vpnPassword;
    private long subscriptionStartDate, subscriptionEndDate;

    public Subscription(String subscriptionId, String VPNPassword, long subscriptionStartDate, long subscriptionEndDate)
    {
        this.subscriptionId = subscriptionId;
        this.vpnPassword = VPNPassword;
        this.subscriptionStartDate = subscriptionStartDate;
        this.subscriptionEndDate = subscriptionEndDate;
    }

    public String getSubscriptionId() {return subscriptionId;}

    public void setSubscriptionId(String subscriptionId) {this.subscriptionId = subscriptionId;}

    public String getVPNPassword() {return vpnPassword;}

    public void setVPNPassword(String VPNPassword) {this.vpnPassword = VPNPassword;}

    public long getSubscriptionStartDate() {return subscriptionStartDate;}

    public void setSubscriptionStartDate(int subscriptionStartDate) {this.subscriptionStartDate = subscriptionStartDate;}

    public long getSubscriptionEndDate() {return subscriptionEndDate;}

    public void setSubscriptionEndDate(int subscriptionEndDate) {this.subscriptionEndDate = subscriptionEndDate;}
}
