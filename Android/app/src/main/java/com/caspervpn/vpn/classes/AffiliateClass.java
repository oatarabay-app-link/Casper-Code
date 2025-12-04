package com.caspervpn.vpn.classes;

/**
 * Created by toufics on 7/11/2018.
 */

public class AffiliateClass {
    private String status;
    private String link;
    private String password;
    private String username;
    private String email;

    public AffiliateClass(){}

    public AffiliateClass(String email, String status, String link, String password, String username)
    {
        this.setLink(link);
        this.setPassword(password);
        this.setUsername(username);
        this.setStatus(status);
        this.setEmail(email);
    }

    public String getLink() {
        return link;
    }

    public void setLink(String link) {
        this.link = link;
    }

    public String getPassword() {
        return password;
    }

    public void setPassword(String password) {
        this.password = password;
    }

    public String getUsername() {
        return username;
    }

    public void setUsername(String username) {
        this.username = username;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }
}
