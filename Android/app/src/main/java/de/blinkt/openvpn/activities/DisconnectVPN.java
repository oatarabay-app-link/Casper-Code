/*
 * Copyright (c) 2012-2016 Arne Schwabe
 * Distributed under the GNU GPL v2 with additional terms. For full terms see the file doc/LICENSE.txt
 */

package de.blinkt.openvpn.activities;

import android.app.Activity;
import android.content.ComponentName;
import android.content.Context;
import android.content.Intent;
import android.content.ServiceConnection;
import android.os.IBinder;
import android.os.RemoteException;

import de.blinkt.openvpn.core.IOpenVPNServiceInternal;
import de.blinkt.openvpn.core.OpenVPNService;
import de.blinkt.openvpn.core.ProfileManager;
import de.blinkt.openvpn.core.VpnStatus;


public class DisconnectVPN extends Activity {
    private IOpenVPNServiceInternal mService;
    private ServiceConnection mConnection = new ServiceConnection() {



        @Override
        public void onServiceConnected(ComponentName className,
                                       IBinder service) {

            mService = IOpenVPNServiceInternal.Stub.asInterface(service);


            try {
                mService.stopVPN(false);
            } catch (RemoteException e) {
                VpnStatus.logException(e);
            }

            unbindService(mConnection);
            finish();
        }

        @Override
        public void onServiceDisconnected(ComponentName arg0) {
            mService = null;
        }

    };

    @Override
    protected void onResume() {
        super.onResume();

        Intent intent = new Intent(getBaseContext(), OpenVPNService.class);
        intent.setAction(OpenVPNService.START_SERVICE);
        bindService(intent, mConnection, Context.BIND_AUTO_CREATE);

        ProfileManager.setConntectedVpnProfileDisconnected(getBaseContext());

    }

    @Override
    protected void onPause()
    {
        super.onPause();
    }
}
