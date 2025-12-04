package com.caspervpn.vpn.classes;

import java.util.Comparator;


public class ServerComparator implements Comparator<Server>
{
    @Override
    public int compare(Server o1, Server o2)
    {
        return Double.compare(o2.getSystemInfo().getHelathPercent(), o1.getSystemInfo().getHelathPercent());
    }
}
