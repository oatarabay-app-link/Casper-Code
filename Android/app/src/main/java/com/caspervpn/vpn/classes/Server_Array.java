package com.caspervpn.vpn.classes;

import java.util.ArrayList;


public class Server_Array
{

    ArrayList<Server> servers;

    public Server_Array(ArrayList<Server> servers) {
        this.servers = servers;
    }

    public ArrayList<Server> getServers() {

        return servers;
    }

    public void setServers(ArrayList<Server> servers) {
        this.servers = servers;
    }
}