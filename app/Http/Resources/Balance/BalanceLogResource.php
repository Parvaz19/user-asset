<?php

namespace App\Http\Resources\Balance;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BalanceLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'change' => (int) $this->change,
            'balance' => (int) $this->balance,
            'user_id' => $this->user_id,
            'user_name' => $this->user->name,
            'asset_id' => $this->asset_id,
            'asset_name' => $this->asset->name,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
