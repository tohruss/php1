<?php

namespace Model;

use Illuminate\Database\Eloquent\Model;
class Employee extends Model
{
    protected  $table = 'employees';
    protected  $primaryKey = 'id';
}