<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Packages extends Model
{
    use HasFactory;

protected $table = "packages";

    protected $fillable = [
        'name',
        'alias',
        'price',
        'duration',
        'size',
        'size2',
        'size3',
        'size4',
        'size5',
        'number_of_shots',
        'inclusion',
        'note',
        'extension',
    ];
}
