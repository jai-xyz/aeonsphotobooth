<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackdropColor extends Model
{
    use HasFactory;

    protected $table = "backdropcolors";

    protected $fillable = [
        'color',
        'backdroptype_id',
        'image'
    ];
}
