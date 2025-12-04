<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payments_Check extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payments__checks';

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
    protected $fillable = [
        'uuid',
        'create_date',
        'subscription_uuid',
        'user_uuid',
        'user_id',
        'user_email',
        'subscription_id',
        'token',
        'status'
    ];

    public function subscriptions()
    {
        return $this->belongsTo('App\Subscription',"subscription_id","id");
    }
    public function users()
    {
        return $this->belongsTo('App\Models\Auth\User',"user_id","id");
    }

    public function payment()
    {
        return $this->hasOne('App\Payment',"check_code","uuid");
    }

    public function payments()
    {
        return $this->hasMany('App\Payment',"check_code","uuid");
    }


    
}
