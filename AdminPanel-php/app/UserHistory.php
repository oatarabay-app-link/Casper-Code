<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserHistory extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_histories';

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
    protected $fillable = ['user_id', 'event', 'operation', 'result', 'description'];

    public function user()
    {
        return $this->belongsTo('App\Models\Auth\User');
    }
    
}
