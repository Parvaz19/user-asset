<?php

namespace App\Http\Resources\Balance;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BalanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'amount' => (int) $this->amount,
            'user_id' => $this->user_id,
            'user_name' => $this->user->name,
            'asset_id' => $this->asset_id,
            'asset_name' => $this->asset->name,
            'asset_price' => (int) $this->asset->price,
        ];
    }
}
