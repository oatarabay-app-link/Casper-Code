<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserServer extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_servers';

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
    protected $fillable = ['user_id', 'vpnserver_id'];

    public function v_p_n_servers()
    {
        return $this->belongsTo('App\VPNServer','vpnserver_id',"id");
    }
    public function user()
    {
        return $this->belongsTo('App\Models\Auth\User');
    }
    
}
