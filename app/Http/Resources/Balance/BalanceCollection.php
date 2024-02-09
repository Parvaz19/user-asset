<?php

namespace App\Http\Resources\Balance;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BalanceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'message' => 'balances list',
            'data' => $this->collection,
        ];
    }
}
