<?php

namespace App\Http\Resources\Conversion;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversionFactorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'from_asset_id' => $this->from_asset_id,
            'from_asset_name' => $this->fromAsset->name,
            'to_asset_id' => $this->to_asset_id,
            'to_asset_name' => $this->toAsset->name,
            'fee' => (int) $this->fee,
        ];
    }
}
