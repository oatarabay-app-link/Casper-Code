package com.caspervpn.vpn.classes;

import java.util.ArrayList;

/**
 * Created by toufics on 7/16/2018.
 */

public class SendEmailBlock {
   private ArrayList<String> emails;

    public SendEmailBlock(){

        emails = new ArrayList<String>();
    }

    public ArrayList<String> getEmails() {
        return emails;
    }

    public void setEmails(ArrayList<String> emails) {
        this.emails = emails;
    }

    public void addEmail(String email) {
        this.emails.add(email);
    }
}
