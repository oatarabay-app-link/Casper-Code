package com.caspervpn.vpn.classes;

import java.util.ArrayList;

/**
 * Created by zaherZ on 3/2/2017.
 */
public class SubscriptionData
{

    private String subscriptionId, subscriptionName;
    private double monthlyPrice, periodPrice, trafficSize, rateLimit;
    private String numServers, numCountries;
    private Boolean availableForAndroid, availableForIos;
    private long createTime;
    private int maxConnections, periodLength;
    private ArrayList<String> protocols = new ArrayList<String>();


    public SubscriptionData(String subscriptionId, String subscriptionName, double monthlyPrice, double periodPrice, int periodLength, double trafficSize, double rateLimit, int maxConnections, String numServers, String numCountries, Boolean availableForAndroid, Boolean availableForIos, long createTime, ArrayList<String> protocols)
    {
        this.subscriptionId = subscriptionId;
        this.subscriptionName = subscriptionName;
        this.monthlyPrice = monthlyPrice;
        this.periodPrice = periodPrice;
        this.periodLength = periodLength;
        this.trafficSize = trafficSize;
        this.rateLimit = rateLimit;
        this.maxConnections = maxConnections;
        this.numServers = numServers;
        this.numCountries = numCountries;
        this.availableForAndroid = availableForAndroid;
        this.availableForIos = availableForIos;
        this.createTime = createTime;
        this.protocols = protocols;
    }


    public int getMaxConnections() {return maxConnections;}

    public void setMaxConnections(int maxConnections) {this.maxConnections = maxConnections;}

    public String getSubscriptionId() {return subscriptionId;}

    public void setSubscriptionId(String subscriptionId) {this.subscriptionId = subscriptionId;}

    public String getSubscriptionName() {return subscriptionName;}

    public void setSubscriptionName(String subscriptionName) {this.subscriptionName = subscriptionName;}

    public double getMonthlyPrice() {return monthlyPrice;}

    public void setMonthlyPrice(double monthlyPrice) {this.monthlyPrice = monthlyPrice;}

    public double getperiodPrice() {return periodPrice;}

    public void setperiodPrice(double periodPrice) {this.periodPrice = periodPrice;}

    public double getperiodLength() {return periodLength;}

    public void setperiodLength(int periodLength) {this.periodLength = periodLength;}

    public double getTrafficSize() {return trafficSize;}

    public void setTrafficSize(double trafficSize) {this.trafficSize = trafficSize;}

    public double getRateLimit() {return rateLimit;}

    public void setRateLimit(double rateLimit) {this.rateLimit = rateLimit;}

    public String getNumServers() {return numServers;}

    public void setNumServers(String numServers) {this.numServers = numServers;}

    public String getNumCountries() {return numCountries;}

    public void setNumCountries(String numCountries) {this.numCountries = numCountries;}

    public Boolean getAvailableForAndroid() {return availableForAndroid;}

    public void setAvailableForAndroid(Boolean availableForAndroid) {this.availableForAndroid = availableForAndroid;}

    public Boolean getAvailableForIos() {return availableForIos;}

    public void setAvailableForIos(Boolean availableForIos) {this.availableForIos = availableForIos;}

    public long getCreateTime() {return createTime;}

    public void setCreateTime(long createTime) {this.createTime = createTime;}

    public ArrayList<String> getProtocols() {return protocols;}

    public void setProtocols(ArrayList<String> protocols) {this.protocols = protocols;}
}
