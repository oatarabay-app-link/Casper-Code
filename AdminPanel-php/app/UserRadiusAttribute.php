<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRadiusAttribute extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_radius_attributes';

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
    protected $fillable = ['user_id', 'attribute', 'op', 'value', 'description', 'status'];

    public function user()
    {
        return $this->belongsTo('App\Models\Auth\User');
    }
    
}
