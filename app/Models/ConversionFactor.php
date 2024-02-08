<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversionFactor extends Model
{
    use HasFactory;

    protected $fillable = ['from_asset_id', 'to_asset_id', 'fee'];

    public function fromAsset()
    {
        return $this->belongsTo(Asset::class, 'from_asset_id');
    }

    public function toAsset()
    {
        return $this->belongsTo(Asset::class, 'to_asset_id');
    }

}
