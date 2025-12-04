<?php

namespace App\Models\Auth\Traits\Method;

/**
 * Trait UserMethod.
 */

use App\UserSubscription;
use App\UserSubscriptionExtension;
use Carbon\Carbon;

trait UserMethod
{
    /**
     * @return mixed
     */
    public function canChangeEmail()
    {
        return config('access.users.change_email');
    }

    /**
     * @return bool
     */
    public function canChangePassword()
    {
        return ! app('session')->has(config('access.socialite_session_name'));
    }

    /**
     * @param bool $size
     *
     * @throws \Illuminate\Container\EntryNotFoundException
     * @return bool|\Illuminate\Contracts\Routing\UrlGenerator|mixed|string
     */
    public function getPicture($size = false)
    {
        switch ($this->avatar_type) {
            case 'gravatar':
                if (! $size) {
                    $size = config('gravatar.default.size');
                }

                return gravatar()->get($this->email, ['size' => $size]);

            case 'storage':
                return url('storage/'.$this->avatar_location);
        }

        $social_avatar = $this->providers()->where('provider', $this->avatar_type)->first();

        if ($social_avatar && strlen($social_avatar->avatar)) {
            return $social_avatar->avatar;
        }

        return false;
    }

    /**
     * @param $provider
     *
     * @return bool
     */
    public function hasProvider($provider)
    {
        foreach ($this->providers as $p) {
            if ($p->provider == $provider) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function isAdmin()
    {
        return $this->hasRole(config('access.users.admin_role'));
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @return bool
     */
    public function isConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * @return bool
     */
    public function isPending()
    {
        return config('access.users.requires_approval') && ! $this->confirmed;
    }


    public function getExpiryDate()
    {
       //Get Current Subscription of the user
        //echo $this->id;
        $subscription =  UserSubscription::where("user_id","=",$this->id)
            ->where("is_active","=",1)
            ->orderBy('id', 'DESC')
            ->first();
        //Get Subscroiptions Dates

        $subscription_start_date =  $subscription->subscription_start_date;
        $subscription_end_date =  $subscription->subscription_end_date;
        //Generate Timestamp from it.
        $subscription_start_date_ts =  Carbon::parse($subscription->subscription_start_date)->timestamp;
        $subscription_end_date_ts =  Carbon::parse($subscription->subscription_end_date)->timestamp;
        \Log::debug("". "Subscription Start and end Date According to Subscription");
        \Log::debug("". $subscription_start_date . "    -    " . $subscription_end_date);
        \Log::debug("". $subscription_start_date_ts . "   -   " . $subscription_end_date_ts);

        //Get Any Active Subscription Extensions User have.
        //todo improve the logic here --- !!! Imporved

        \Log::debug( "". "Looking for Subscription Extensions");
        $subscriptionExtension =  UserSubscriptionExtension::where("user_id","=",$this->id)
            ->whereRaw("(expiry_date > NOW() or expiry_date > '" .$subscription_end_date. "')")
            ->orderBy('id', 'DESC')
            ->first();
        //

        if ($subscriptionExtension){
        $subscriptionExtension_end_date =  $subscriptionExtension->expiry_date;
        //Generate Timestamp from it.
        $subscriptionExtension_end_date_ts =  Carbon::parse($subscriptionExtension->expiry_date)->timestamp;
        \Log::debug("". $subscriptionExtension_end_date . " -  " . $subscriptionExtension_end_date_ts);

        if ($subscriptionExtension_end_date > $subscription_end_date ){
            \Log::debug( "". "Subscription Extension Found. ");
            \Log::debug( "". "Overriding End date. ");
            $subscription_end_date= $subscriptionExtension_end_date ;
            \Log::debug("". $subscription_end_date);
        }

        }

        return $subscription_end_date;


    }
}
