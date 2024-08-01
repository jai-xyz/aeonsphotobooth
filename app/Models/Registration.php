<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    protected $table = "registration";

    protected $fillable = [
        'user_id',
        'event',
        'date',
        'time',
        'address',
        'backdroptype',
        'backdropcolor',
        'numofpics',
        'suggestion',
    ];
}
