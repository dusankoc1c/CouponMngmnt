<?php

namespace App\Mail;

use App\Helpers\EmailTemplateHelper;
use App\Models\Coupon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public Coupon $coupon;
    public string $renderBody;
    /**
     * Create a new message instance.
     */
    public function __construct(Coupon $coupon)
    {
        $this->coupon = $coupon;

        $store = $coupon->bundle->store;

        if($store->initial_email_template != null){
            $template = $store->initial_email_template;
        }else{
            $template = EmailTemplateHelper::getDefaultInitialTemplate();
        }

        $this->renderBody = EmailTemplateHelper::render($template, [
            'name' => $coupon->receiver_name,
            'amount' => number_format((float) $coupon->discount_amount, 2),
            'store_name' => $store->name,
        ]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Coupon Has Arrived',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.coupon',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
