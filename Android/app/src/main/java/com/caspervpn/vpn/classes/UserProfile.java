package com.caspervpn.vpn.classes;

/**
 * Created by zaherZ on 1/3/2017.
 */

public class UserProfile
{

    private String id, login, phone, firstName, lastName, description, userRoleType, role;
    private SubscriptionData SubscriptionData;
    private Subscription Subscription;
    boolean blocked;
    long createTime,bwlimit=0,bwRemainigLimit=0;


    public UserProfile(String id, String login, String phone, String firstName, String lastName, String description, String userRoleType, String role, com.caspervpn.vpn.classes.SubscriptionData subscriptionData, com.caspervpn.vpn.classes.Subscription subscription, boolean blocked, long createTime, long bwlimit, long bwRemainigLimit) {
        this.id = id;
        this.login = login;
        this.phone = phone;
        this.firstName = firstName;
        this.lastName = lastName;
        this.description = description;
        this.userRoleType = userRoleType;
        this.role = role;
        SubscriptionData = subscriptionData;
        Subscription = subscription;
        this.blocked = blocked;
        this.createTime = createTime;
        this.bwlimit = bwlimit ==0  ? (500):bwlimit;
        this.bwRemainigLimit = bwRemainigLimit ==0  ? (500):bwRemainigLimit;
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public String getLogin() {
        return login;
    }

    public void setLogin(String login) {
        this.login = login;
    }

    public String getPhone() {
        return phone;
    }

    public void setPhone(String phone) {
        this.phone = phone;
    }

    public String getFirstName() {
        return firstName;
    }

    public void setFirstName(String firstName) {
        this.firstName = firstName;
    }

    public String getLastName() {
        return lastName;
    }

    public void setLastName(String lastName) {
        this.lastName = lastName;
    }

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public String getUserRoleType() {
        return userRoleType;
    }

    public void setUserRoleType(String userRoleType) {
        this.userRoleType = userRoleType;
    }

    public String getRole() {
        return role;
    }

    public void setRole(String role) {
        this.role = role;
    }

    public com.caspervpn.vpn.classes.SubscriptionData getSubscriptionData() {
        return SubscriptionData;
    }

    public void setSubscriptionData(com.caspervpn.vpn.classes.SubscriptionData subscriptionData) {
        SubscriptionData = subscriptionData;
    }

    public com.caspervpn.vpn.classes.Subscription getSubscription() {
        return Subscription;
    }

    public void setSubscription(com.caspervpn.vpn.classes.Subscription subscription) {
        Subscription = subscription;
    }

    public boolean isBlocked() {
        return blocked;
    }

    public void setBlocked(boolean blocked) {
        this.blocked = blocked;
    }

    public long getCreateTime() {
        return createTime;
    }

    public void setCreateTime(long createTime) {
        this.createTime = createTime;
    }

    public long getBwlimit() {
        return bwlimit;
    }

    public void setBwlimit(long bwlimit) {
        this.bwlimit = bwlimit;
    }

    public long getBwRemainigLimit() {
        return bwRemainigLimit;
    }

    public void setBwRemainigLimit(long bwRemainigLimit) {
        this.bwRemainigLimit = bwRemainigLimit;
    }
}
