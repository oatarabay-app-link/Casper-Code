<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RadAcct extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'rad_accts';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'radacctid';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['acctsessionid','acctuniqueid', 'username', 'groupname', 'realm', 'nasipaddress', 'nasidentifier', 'nasportid', 'nasporttype', 'acctstarttime', 'acctstoptime', 'acctsessiontime', 'acctauthentic', 'connectinfo_start', 'connectinfo_stop', 'acctinputoctet', 'acctoutputoctet', 'calledstationid', 'callingstationid', 'acctterminatecause', 'servicetype', 'framedprotocol', 'framedipaddress', 'acctstartdelay', 'acctstopdelay', 'xascendsessionsvrkey'];

    
}
