<?php

namespace App\Models;

use App\Mail\MyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Str;

class Coupon extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'bundle_id', 'code', 'discount_amount',
        'receiver_name', 'receiver_email',
        'send_date', 'send_immediately', 'expires_at',
        'is_used', 'used_at', 'email_sent_at',
        'last_sent_at', 'subscribed', 'is_expired',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'send_immediately' => 'boolean',
        'used_at' => 'datetime',
        'send_date' => 'datetime',
        'expires_at' => 'datetime',
        'email_sent_at' => 'datetime',
        'last_sent_at' => 'datetime',
        'subscribed' => 'boolean',
        'is_expired' => 'boolean',
        'discount_amount' => 'decimal:2',
    ];
    public function bundle(): belongsTo
    {
        return $this->belongsTo(Bundle::class);
    }


    // slanje inicijalnog mejla ako nije zakazan T/F izvuceno iz kontrollera
    public function sendInititalMail(): bool
    {
        if ($this->email_sent_at != null) {
            return false;
        }
        if ($this->receiver_email == null) {
            return false;
        }
        if ($this->send_date != null) {
            return false;
        }
        if(!$this->send_immediately){
            return false;
        }

        try {
            Mail::to($this->receiver_email)->send(new MyEmail($this));

            $this->email_sent_at = now();
            $this->save();

            Log::info('Poslat mejl : ' . $this->code);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage() . $this->code);
        }

        return true;
    }
}
