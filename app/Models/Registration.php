<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{

    protected $table = "registrations";

    protected $fillable = [
        'user_id',
        'event',
        'region',
        'province',
        'city',
        'barangay',
        'street',
        'zipcode',
        'contactperson',
        'contactno',
        'email',
        'date',
        'time',
        'packageid',
        'packagename',  
        'packagesize',
        'backdroptype',
        'backdropcolor',
        'number_of_shots',
        'price',
        'extension',
        'theme',
        'suggestion',
        'images',
    ];
}
