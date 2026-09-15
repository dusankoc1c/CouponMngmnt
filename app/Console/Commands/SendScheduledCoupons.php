<?php

namespace App\Console\Commands;

use App\Mail\MyEmail;
use App\Models\Coupon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

#[Signature('app:send-scheduled-coupons')]
#[Description('Command description')]
class SendScheduledCoupons extends Command
{
    /**
     * Execute the console command.
     */
    protected $signature = 'app:send-scheduled-coupon';

    public function handle()
    {
        $coupons =  Coupon::whereNotNull('receiver_email')
            ->whereNotNull('send_date')
            ->whereNull('email_sent_at')
            ->where('send_date', '<=', now())
            ->get();

        $this->info('za slanje : ' . $coupons->count());

        foreach ($coupons as $coupon) {
            Mail::to($coupon->receiver_email)->send(new MyEmail($coupon));

            $coupon->email_sent_at = now();
            $coupon->save();

            $this->info('poslato : ' . $coupon->code);
        }
    }
}
