<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RadiusDefaultAttribute extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'radius_default_attributes';

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
    protected $fillable = ['attribute', 'op', 'value', 'description', 'status'];

    
}
