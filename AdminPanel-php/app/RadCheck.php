<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RadCheck extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'rad_checks';

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
    protected $fillable = ['username', 'attribute', 'op', 'value', 'protocol','user_id'];


    public function user()
    {
        return $this->belongsTo('App\Models\Auth\User');
    }


    
}
