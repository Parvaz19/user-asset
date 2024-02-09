<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'balances')->withPivot('amount');
    }

    public function balances()
    {
        return $this->hasMany(Balance::class);
    }

}
