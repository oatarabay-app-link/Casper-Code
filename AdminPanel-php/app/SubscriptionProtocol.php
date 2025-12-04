<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscriptionProtocol extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'subscription_protocols';

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
    protected $fillable = ['subscription_uuid', 'protocol_uuid', 'protocol_id', 'subscription_id'];

    public function subscriptions()
    {
        return $this->belongsTo('App\Subscription');
    }
    public function protocols()
    {
        return $this->belongsTo('App\Protocol');
    }
    
}
