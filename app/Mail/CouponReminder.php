<?php

namespace App\Mail;

use App\Models\Coupon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class CouponReminder extends Mailable
{
    use Queueable, SerializesModels;

    public Coupon $coupon;
    public int $daniDoIsteka;
    public string $unsubscribeLink;
    /**
     * Create a new message instance.
     */
    public function __construct(Coupon $coupon)
    {
        $this->coupon = $coupon;
        if ($coupon->expires_at != null) {
            $this->daniDoIsteka = now()->diffInDays($coupon->expires_at, false);
        }else{
            $this->daniDoIsteka = -1;
        }
        $this->unsubscribeLink = URL::signedRoute('coupons.unsubscribe', ['coupon' => $coupon->id]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Coupon Reminder',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.coupon_reminder',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
