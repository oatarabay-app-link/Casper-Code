<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'services';

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
    protected $fillable = ['name', 'type', 'amount', 'is_recurring', 'is_autobill', 'setup_fee', 'purchase_date', 'renewal_date', 'notify', 'notify_days', 'service_provider_id', 'notes'];

    public function service_provider()
    {
        return $this->belongsTo('App\ServiceProvider');
    }
    
}
