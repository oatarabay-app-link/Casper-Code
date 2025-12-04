<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscriptionRadiusAttribute extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'subscription_radius_attributes';

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
    protected $fillable = ['subscription_id', 'attribute', 'op', 'value', 'description', 'status'];

    public function subscriptions()
    {
        return $this->belongsTo('App\Subscription');
    }
    
}
