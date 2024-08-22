<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    protected $table = "registrations";

    protected $fillable = [
        'user_id',
        'event',
        'address',
        'contactperson',
        'contactno',
        'date',
        'time',
        'packageid',
        'packagename',  
        'packagesize',
        'backdroptype',
        'backdropcolor',
        'suggestion',
    ];
}
