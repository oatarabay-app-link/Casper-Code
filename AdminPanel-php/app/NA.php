<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NA extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'n_a_s';

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
    protected $fillable = ['nasname', 'shortname', 'type', 'ports', 'secret', 'server', 'community', 'description', 'details', 'check_code'];

    
}
