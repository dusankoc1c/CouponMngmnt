<?php

namespace App\Console\Commands;

use App\Mail\CouponReminder;
use App\Models\Coupon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

#[Signature('app:send-coupon-reminder')]
#[Description('Command description')]
class SendCouponReminder extends Command
{
    protected $signature = 'app:send-coupon-reminder';
    protected $description = 'Sending coupon reminders (2 mins testing)';
    /**
     * Execute the console command.
     */

    public function handle()
    {
        $coupons = Coupon::whereNotNull('receiver_email')
            ->where('is_used', false)
            ->where('subscribed', true)
            ->whereNotNull('email_sent_at')
            ->get();

        $this->info('pokusaj slanja : ' . $coupons->count());

        $sentCount = 0;

        foreach ($coupons as $coupon) {
            $store = $coupon->bundle->store;

            if($store->reminder_days != null){
                $reminderDays = $store->reminder_days;
            }else{
                $reminderDays = 20;
            }

            $lastSent = $coupon->last_sent_at ?? $coupon->email_sent_at;

            $daysPassed = $lastSent->diffInDays(now());

            $this->info($coupon->code . ' - proslo dana ' . $daysPassed .  '- od potrebnih : ' . $reminderDays);

            if ($daysPassed < $reminderDays) {
                $this->info($coupon->code . ' - nije ispunio uslov za vreme');
                continue;
            }

            Mail::to($coupon->receiver_email)->send(new CouponReminder($coupon));

            $coupon->last_sent_at = now();
            $coupon->save();

            $sentCount++;
            $this->info('poslat  - ' . $coupon->code);
        }

        $this->info('ukupno - ' . $sentCount);
    }

}
