package com.caspervpn.vpn.classes;

/**
 * Created by toufics on 9/20/2017.
 */

public class Payment {
    private String subscriptionId;
    private String code;
    private String checkoutUrl;

    public Payment(String subscriptionId, String checkouturl, String code){
        this.subscriptionId = subscriptionId;
        this.checkoutUrl = checkouturl;
        this.code = code;
    }

    public String getSubscriptionId() {
        return subscriptionId;
    }

    public void setSubscriptionId(String subscriptionId) {
        this.subscriptionId = subscriptionId;
    }

    public String getcode() {
        return code;
    }

    public void setcode(String data_code) {
        this.code = data_code;
    }

    public String getCheckouturl() {
        return checkoutUrl;
    }

    public void setCheckouturl(String checkouturl) {
        this.checkoutUrl = checkouturl;
    }

    public String getCode() {
        return code;
    }

    public void setCode(String code) {
        this.code = code;
    }
}
