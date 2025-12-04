<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VPNServer extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'v_p_n_servers';

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
        'create_date',
        'is_deleted',
        'is_disabled',
        'ip',
        'latitude',
        'longitude',
        'name',
        'country',
        'parameters',
        'server_provider',
        'notes',
        'service_id',
        'nas_fqdn',
        'nas_type',
        'nas_ports',
        'nas_secret',
        'nas_server',
        'nas_community',
        'nas_description',
        'nas_id',

    ];

    public function service()
    {
        return $this->belongsTo('App\Service');
    }

    public function protocols()
    {
        return $this->hasMany('\App\VPNServerProtocol', 'vpnserver_id' ,'id');

    }

    protected $casts = [
        'disabled'=> 'boolean',
    ];
}
