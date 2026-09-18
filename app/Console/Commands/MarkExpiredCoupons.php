<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:mark-expired-coupons')]
#[Description('Command description')]
class MarkExpiredCoupons extends Command
{
    protected $signature = 'app:mark-expired-coupons';
    protected $description = 'mark coupons that expired (date)';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $coupons = Coupon::whereNotNull('expires_at')
                ->where('expires_at', '<=', now())
                ->where('is_used', false)
                ->where('is_expired', false)->get();

        $this->info("Pronadjeno expired kupona : ",$coupons->count());

        foreach ($coupons as $coupon) {
            $coupon->is_expired = true;
            $coupon->save();
        }
    }
}
