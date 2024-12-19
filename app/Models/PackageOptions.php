<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageOptions extends Model
{
    use HasFactory;

    protected $fillable = [
        'packages_id', 'number_of_shots', 'price', 'extension'
    ];

    public function package()
    {
        return $this->belongsTo(Packages::class);
    }
}