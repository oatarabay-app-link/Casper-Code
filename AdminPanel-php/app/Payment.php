<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payments';

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
        'subscription_uuid',
        'subscription_id',
        'user_id',
        'period_in_months',
        'payment_id',
        'status',
        'payment_sum',
        'details',
        'check_code',
        'product_description',
        'product_description_corrected',
        'create_date'
    ];

    public function subscriptions()
    {
        return $this->belongsTo('App\Subscription',"subscription_id","id");
    }
    public function users()
    {
        return $this->belongsTo('App\Models\Auth\User',"user_id","id");

    }

    public function payment_check()
    {
        return $this->belongsTo('App\Payments_Check',"check_code","uuid");
    }

}
