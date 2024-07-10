<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            // 'payer' => new UserResource($this->whenLoaded('payer')),
            // 'payee' => new UserResource($this->whenLoaded('payee')),
            'payer' => new UserResource($this->whenLoaded('payer')),
            'payee' => new UserResource($this->whenLoaded('payee')),
            'value' => $this->value,
            'status' => $this->status,
            'completed_at' => $this->completed_at,
            'canceled_at' => $this->canceled_at,
        ];
    }
}
