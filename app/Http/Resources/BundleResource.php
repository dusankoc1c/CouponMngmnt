<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BundleResource extends JsonResource
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
            'store_id' => $this->store_id,
            'name' => $this->name,
            'description' => $this->description,
            'expires_at' => $this->expires_at,
            'number_of_codes' => $this->numberOfCodes(),
            'total_value' => $this->getTotalValue(),
            'created_at' => $this->created_at,
        ];
    }
}
