<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return
            [
                'id'=>$this->id,
                'name'=>$this->name,
                'description'=>$this->description,
                'value_limit' => $this->value_limit,
                'reminder_days' => $this->reminder_days,
                'created_at' => $this->created_at,
        ];
    }
}
