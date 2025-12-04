<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AppSignupReport
 * @package App\Models
 * @version January 17, 2021, 2:24 am UTC
 *
 * @property integer $user_id
 * @property string $email
 * @property string $status
 * @property string $signup_date
 * @property string $signedin_date
 * @property string $subscription
 * @property integer $emails_sent
 * @property integer $emails_problems
 * @property string $device
 * @property string $Country
 * @property string $OS
 * @property string $last_seen
 *
 */
class AppSignupReport extends Model
{
    use SoftDeletes;

    public $table = 'app_signup_reports';


    protected $dates = ['deleted_at'];



    public $fillable = [
        'user_id',
        'email',
        'status',
        'signup_date',
        'signedin_date',
        'subscription',
        'emails_sent',
        'emails_problems',
        'device',
        'Country',
        'OS',
        'last_seen'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'email' => 'string',
        'status' => 'string',
        'signup_date' => 'datetime',
        'signedin_date' => 'datetime',
        'subscription' => 'string',
        'emails_sent' => 'integer',
        'emails_problems' => 'integer',
        'device' => 'string',
        'Country' => 'string',
        'OS' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'email' => 'required',
        'subscription' => 'required'
    ];


}
