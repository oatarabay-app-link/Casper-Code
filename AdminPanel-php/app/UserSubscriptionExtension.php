<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSubscriptionExtension extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_subscription_extensions';

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
    protected $fillable = ['user_id', 'subscription_id', 'days', 'expiry_date', 'note', 'added_by'];

    public function subscriptions()
    {
        return $this->belongsTo('App\Models\UserSubscriptions',"subscription_id",'id');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\Auth\User');
    }
    
}
