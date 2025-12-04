package com.caspervpn.vpn.Subscriptions.Models;

import java.util.ArrayList;

public class Subcription {


    private String subscriptionId, subscriptionName,expiresOn,pricestring,package_offer,package_saving;
    private double monthlyPrice;
    private double periodPrice;
    private double trafficSize;


    private double rateLimit;
    private String numServers, numCountries;
    private Boolean availableForAndroid, availableForIos,subscribed;
    private long createTime;
    private int maxConnections, periodLength;
    private ArrayList<String> protocols = new ArrayList<String>();
    private ArrayList<String> payment_methods = new ArrayList<String>();

    public Subcription(String subscriptionId, String subscriptionName, String expiresOn, String pricestring, String package_offer, String package_saving, double monthlyPrice, double periodPrice, double trafficSize, double rateLimit, String numServers, String numCountries, Boolean availableForAndroid, Boolean availableForIos, Boolean subscribed, long createTime, int maxConnections, int periodLength, ArrayList<String> protocols, ArrayList<String> payment_methods) {
        this.subscriptionId = subscriptionId;
        this.subscriptionName = subscriptionName;
        this.expiresOn = expiresOn;
        this.pricestring = pricestring;
        this.package_offer = package_offer;
        this.package_saving = package_saving;
        this.monthlyPrice = monthlyPrice;
        this.periodPrice = periodPrice;
        this.trafficSize = trafficSize;
        this.rateLimit = rateLimit;
        this.numServers = numServers;
        this.numCountries = numCountries;
        this.availableForAndroid = availableForAndroid;
        this.availableForIos = availableForIos;
        this.subscribed = subscribed;
        this.createTime = createTime;
        this.maxConnections = maxConnections;
        this.periodLength = periodLength;
        this.protocols = protocols;
        this.payment_methods = payment_methods;
    }

    public String getSubscriptionId() {
        return subscriptionId;
    }

    public void setSubscriptionId(String subscriptionId) {
        this.subscriptionId = subscriptionId;
    }

    public String getSubscriptionName() {
        return subscriptionName;
    }

    public void setSubscriptionName(String subscriptionName) {
        this.subscriptionName = subscriptionName;
    }

    public String getExpiresOn() {
        return expiresOn;
    }

    public void setExpiresOn(String expiresOn) {
        this.expiresOn = expiresOn;
    }

    public String getPricestring() {
        return pricestring;
    }

    public void setPricestring(String pricestring) {
        this.pricestring = pricestring;
    }

    public String getPackage_offer() {
        return package_offer;
    }

    public void setPackage_offer(String package_offer) {
        this.package_offer = package_offer;
    }

    public String getPackage_saving() {
        return package_saving;
    }

    public void setPackage_saving(String package_saving) {
        this.package_saving = package_saving;
    }

    public double getMonthlyPrice() {
        return monthlyPrice;
    }

    public void setMonthlyPrice(double monthlyPrice) {
        this.monthlyPrice = monthlyPrice;
    }

    public double getPeriodPrice() {
        return periodPrice;
    }

    public void setPeriodPrice(double periodPrice) {
        this.periodPrice = periodPrice;
    }

    public double getTrafficSize() {
        return trafficSize;
    }

    public void setTrafficSize(double trafficSize) {
        this.trafficSize = trafficSize;
    }

    public double getRateLimit() {
        return rateLimit;
    }

    public void setRateLimit(double rateLimit) {
        this.rateLimit = rateLimit;
    }

    public String getNumServers() {
        return numServers;
    }

    public void setNumServers(String numServers) {
        this.numServers = numServers;
    }

    public String getNumCountries() {
        return numCountries;
    }

    public void setNumCountries(String numCountries) {
        this.numCountries = numCountries;
    }

    public Boolean getAvailableForAndroid() {
        return availableForAndroid;
    }

    public void setAvailableForAndroid(Boolean availableForAndroid) {
        this.availableForAndroid = availableForAndroid;
    }

    public Boolean getAvailableForIos() {
        return availableForIos;
    }

    public void setAvailableForIos(Boolean availableForIos) {
        this.availableForIos = availableForIos;
    }

    public Boolean getSubscribed() {
        return subscribed;
    }

    public void setSubscribed(Boolean subscribed) {
        this.subscribed = subscribed;
    }

    public long getCreateTime() {
        return createTime;
    }

    public void setCreateTime(long createTime) {
        this.createTime = createTime;
    }

    public int getMaxConnections() {
        return maxConnections;
    }

    public void setMaxConnections(int maxConnections) {
        this.maxConnections = maxConnections;
    }

    public int getPeriodLength() {
        return periodLength;
    }

    public void setPeriodLength(int periodLength) {
        this.periodLength = periodLength;
    }

    public ArrayList<String> getProtocols() {
        return protocols;
    }

    public void setProtocols(ArrayList<String> protocols) {
        this.protocols = protocols;
    }

    public ArrayList<String> getPayment_methods() {
        return payment_methods;
    }

    public void setPayment_methods(ArrayList<String> payment_methods) {
        this.payment_methods = payment_methods;
    }
}
