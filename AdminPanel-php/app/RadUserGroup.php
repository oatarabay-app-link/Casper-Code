<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RadUserGroup extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'rad_user_groups';

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
    protected $fillable = ['username', 'groupname', 'priority'];

    
}
