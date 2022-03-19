<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Person extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    use SoftDeletes;
    protected $fillable = [
        'first_name', 'email', 'last_name', 'document','img','type_person'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
  
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
}
