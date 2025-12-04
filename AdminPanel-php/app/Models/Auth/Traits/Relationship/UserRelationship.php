<?php

namespace App\Models\Auth\Traits\Relationship;

use App\Models\Auth\PasswordHistory;
use App\Models\Auth\SocialAccount;

/**
 * Class UserRelationship.
 */
trait UserRelationship
{
    /**
     * @return mixed
     */
    public function providers()
    {
        return $this->hasMany(SocialAccount::class);
    }

    /**
     * @return mixed
     */
    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    /**
     * @return mixed
     */
    public function passwordHistories()
    {
        return $this->hasMany(PasswordHistory::class);
    }


    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class, "user_id","id");
    }

    public function subscription()
    {
        return $this->hasOne(UserSubscription::class, "user_id","id");
    }


    public function payment_checks()
    {
        return $this->hasMany(Payments_Check::class, "user_id","id");

    }


    public function payments()
    {
        return $this->hasMany(Payment::class, "user_id","id");

    }
}
