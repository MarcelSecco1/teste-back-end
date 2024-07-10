<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'payer' => UserResource::collection($this->whenLoaded('payer')),
            'payee' => UserResource::collection($this->whenLoaded('payee')),
            'value' => $this->value,
            'status' => $this->status,
            'completed_at' => $this->completed_at,
            'canceled_at' => $this->canceled_at,
        ];
    }
}
