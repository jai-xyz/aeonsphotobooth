<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Yajra\Address\HasAddress;


class Registration extends Model
{
    use HasFactory, HasAddress;

    protected $table = "registrations";

    protected $fillable = [
        'user_id',
        'event',
        'street',
        'barangay_id',
        'city_id',
        'province_id',
        'region_id',
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
