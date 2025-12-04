<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_subscriptions';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['uuid', 'subscription_uuid', 'subscription_start_date', 'subscription_end_date', 'vpn_pass', 'is_active', 'subscription_id', 'user_id'];

    public function subscriptions()
    {
        return $this->belongsTo('App\Subscription',"subscription_id","id");
    }
    public function users()
    {
        return $this->belongsTo('App\Models\Auth\User');
    }
    
}
