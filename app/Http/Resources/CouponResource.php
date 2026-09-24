<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
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
            'bundle_id'=>$this->bundle_id,
            'code'=>$this->code,
            'discount_amount' => $this->discount_amount,
            'receiver_name' => $this->receiver_name,
            'receiver_email' => $this->receiver_email,
            'send_date' => $this->send_date,
            'send_immediately' => $this->send_immediately,
            'expires_at' => $this->expires_at,
            'is_used' => $this->is_used,
            'is_expired' => $this->is_expired,
            'subscribed' => $this->subscribed,
            'email_sent_at' => $this->email_sent_at,
            'last_sent_at' => $this->last_sent_at,
            'created_at' => $this->created_at,
        ];
    }
}
