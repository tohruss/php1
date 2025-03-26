<?php

namespace Model;

use Illuminate\Database\Eloquent\Model;

class Departament extends Model
{
    protected $table = 'departaments';
    protected $primaryKey = 'id';

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}