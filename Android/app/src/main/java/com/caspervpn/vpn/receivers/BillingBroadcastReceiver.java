/*
 * Copyright 2012 Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

package com.caspervpn.vpn.receivers;

import android.content.Context;
import android.content.Intent;

import com.caspervpn.vpn.Subscriptions.Screens.Subscriptions;
import com.caspervpn.vpn.screens.Subscribe;
import com.parse.ParsePushBroadcastReceiver;


/**
 * Before attempting to run this sample, please read the README file. It
 * contains important information on how to set up this project.
 */

public class BillingBroadcastReceiver extends ParsePushBroadcastReceiver
{
    @Override
    public void onPushOpen(Context context, Intent intent)
    {
        Intent i = new Intent(context, Subscriptions.class);
        i.putExtras(intent.getExtras());
        i.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
        context.startActivity(i);
    }
}