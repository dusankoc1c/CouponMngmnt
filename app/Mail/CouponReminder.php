<?php

namespace App\Mail;

use App\Helpers\EmailTemplateHelper;
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
    public string $renderBody;
    public string $daniDoIsteka;
    public string $unsubscribeUrl;
    /**
     * Create a new message instance.
     */
    public function __construct(Coupon $coupon)
    {
        $this->coupon = $coupon;

        $this->daniDoIsteka = $this->calculateDaysLeft($coupon);

        $store = $coupon->bundle->store;

        if ($store->reminder_email_template != null) {
            $template = $store->reminder_email_template;
        } else {
            $template = EmailTemplateHelper::getDefaultReminderTemplate();
        }

        $this->renderBody = EmailTemplateHelper::render($template, [
            'name' => $coupon->receiver_name,
            'amount' => number_format((float) $coupon->discount_amount, 2),
            'store_name' => $store->name,
            'days_left' => $this->daniDoIsteka,
        ]);

        $this->unsubscribeUrl = URL::signedRoute('coupons.unsubscribe', ['coupon' => $coupon->id]);
    }
    public function calculateDaysLeft(Coupon $coupon): string
    {
        if ($coupon->expires_at == null) {
            return 'N/A';
        }

        $days = now()->diffInDays($coupon->expires_at, false);

        return (string) $days;
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
