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
        'duration',
        'size',
        'size2',
        'size3',
        'inclusion',
        'note',
    ];

    public function options()
    {
        return $this->hasMany(PackageOptions::class);
    }
}
