<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RadPostAuth extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'rad_post_auths';

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
    protected $fillable = ['username', 'pass', 'reply', 'priority'];

    
}
