<?php

namespace Model;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employees';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'last_name',
        'first_name',
        'middle_name',
        'gender',
        'birth_date',
        'address',
        'post'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}