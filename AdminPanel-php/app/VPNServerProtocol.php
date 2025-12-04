<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VPNServerProtocol extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'v_p_n_server_protocols';

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
    protected $fillable = ['vpnserver_uuid', 'protocol_uuid', 'vpnserver_id', 'protocol_id'];

    public function v_p_n_servers()
    {
        return $this->belongsTo('App\VPNServer');
    }
    public function protocols()
    {
        return $this->belongsTo('App\Protocol','protocol_id',"id");
    }
    
}
