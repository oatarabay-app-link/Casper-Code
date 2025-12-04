<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'subscriptions';

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
        'subscription_name',
        'monthly_price',
        'period_price',
        'currency_type',
        'traffic_size',
        'rate_limit',
        'max_connections',
        'available_for_android',
        'available_for_ios',
        'create_time',
        'is_default',
        'period_length',
        'order_num',
        'product_id'];

    
}
