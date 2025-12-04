<?php

namespace App\Models\Auth;

use Altek\Accountant\Contracts\Recordable;
use Altek\Accountant\Recordable as RecordableTrait;
use Altek\Eventually\Eventually;
use App\Models\Auth\Traits\SendUserPasswordReset;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;


/**
 * Class User.
 */
abstract class BaseUser extends Authenticatable implements Recordable
{
    use HasRoles,
        Eventually,
        Impersonate,
        Notifiable,
        RecordableTrait,
        SendUserPasswordReset,
        SoftDeletes,
        Uuid,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'avatar_type',
        'avatar_location',
        'password',
        'password_changed_at',
        'active',
        'confirmation_code',
        'confirmed',
        'timezone',
        'last_login_at',
        'last_login_ip',
        'to_be_logged_out',
        'login',
        'old_login',
        'login_pass_id',
        'is_confirmed',
        'confirm_code',
        'version',
        'create_time',
        'user_login',
        'old_user_login',
        'phone',
        'user_subscription_id',
        'is_blocked',
        'is_deleted',
        'description',
        'tsv',
        'affliate_ref',
        'last_active_date',
        'create_date',
        'uuid',
        'vpn_pass',
        'user_uuid'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attributes to exclude from the Audit.
     *
     * @var array
     */
    protected $auditExclude = [
        'id',
        'password',
        'remember_token',
        'confirmation_code',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
        'confirmed' => 'boolean',
        'to_be_logged_out' => 'boolean',
        'is_blocked'=> 'boolean',

    ];

    /**
     * @var array
     */
    protected $dates = [
        'last_login_at',
        'password_changed_at',
    ];

    /**
     * The dynamic attributes from mutators that should be returned with the user object.
     * @var array
     */
    protected $appends = [
        'full_name',
    ];

    /**
     * Return true or false if the user can impersonate an other user.
     *
     * @param void
     * @return  bool
     */
    public function canImpersonate()
    {
        return $this->isAdmin();
    }

    /**
     * Return true or false if the user can be impersonate.
     *
     * @param void
     * @return  bool
     */
    public function canBeImpersonated()
    {
        return $this->id !== 1;
    }
}
