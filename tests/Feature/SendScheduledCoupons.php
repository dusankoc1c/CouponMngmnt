<?php

namespace Tests\Feature;

use App\Mail\MyEmail;
use App\Models\Bundle;
use App\Models\Coupon;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendScheduledCoupons extends TestCase
{
    use RefreshDatabase;

    public function test_istekao_kupon_se_ne_salje(): void
    {
        Mail::fake();

        $istekaoKupon = $this->kreirajKupon([
            'send_date' => now()->subHour(),
            'expires_at' => now()->subDay(),
            'email_sent_at' => null,
        ]);

        $this->artisan('app:send-scheduled-coupons');

        Mail::assertNotSent(MyEmail::class);

        $istekaoKupon->refresh();
        $this->assertNull($istekaoKupon->email_sent_at);
    }

    public function test_kupon_bez_datuma_isteka_se_salje(): void
    {
        Mail::fake();

        $kupon = $this->kreirajKupon([
            'send_date' => now()->subHour(),
            'expires_at' => null,
            'email_sent_at' => null,
        ]);

        $this->artisan('app:send-scheduled-coupons');

        Mail::assertSent(MyEmail::class);

        $kupon->refresh();
        $this->assertNotNull($kupon->email_sent_at);
    }

    public function test_kupon_koji_jos_nije_istekao_se_salje(): void
    {
        Mail::fake();

        $kupon = $this->kreirajKupon([
            'send_date' => now()->subHour(),
            'expires_at' => now()->addDay(),
            'email_sent_at' => null,
        ]);

        $this->artisan('app:send-scheduled-coupons');

        Mail::assertSent(MyEmail::class);

        $kupon->refresh();
        $this->assertNotNull($kupon->email_sent_at);
    }

    private function kreirajKupon(array $podaci): Coupon
    {
        $korisnik = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $korisnik->id]);
        $bundle = Bundle::factory()->create(['store_id' => $store->id]);

        return Coupon::factory()->create(array_merge([
            'bundle_id' => $bundle->id,
            'receiver_email' => 'test@example.com',
        ], $podaci));
    }
}
