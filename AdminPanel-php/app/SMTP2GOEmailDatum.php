<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SMTP2GOEmailDatum extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 's_m_t_p2_g_o_email_datas';

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
    protected $fillable = ['subject', 'delivered_at', 'process_status', 'email_id', 'status', 'response', 'email_tx', 'host', 'smtpcode', 'sender', 'recipient', 'stmp2gousername', 'headers', 'total_opens', 'opens'];

    
}
