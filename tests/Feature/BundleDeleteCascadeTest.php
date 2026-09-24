<?php

namespace Tests\Feature;

use App\Models\Bundle;
use App\Models\Coupon;
use App\Models\Store;
use App\Models\User;
use App\Services\BundleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BundleDeleteCascadeTest extends TestCase
{
    use RefreshDatabase;

    public function test_brisanje_bundle_a_soft_brise_i_njegove_kupone(): void
    {
        $korisnik = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $korisnik->id]);
        $bundle = Bundle::factory()->create(['store_id' => $store->id]);

        $kupon1 = Coupon::factory()->create(['bundle_id' => $bundle->id]);
        $kupon2 = Coupon::factory()->create(['bundle_id' => $bundle->id]);

        app(BundleService::class)->deleteBundle($bundle);

        $this->assertNull(Coupon::find($kupon1->id));
        $this->assertNull(Coupon::find($kupon2->id));

        $this->assertNotNull(Coupon::withTrashed()->find($kupon1->id));
        $this->assertNotNull($kupon1->fresh()?->deleted_at ?? Coupon::withTrashed()->find($kupon1->id)->deleted_at);
    }

    public function test_soft_deleted_kupon_se_ne_salje_preko_schedulera(): void
    {
        $korisnik = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $korisnik->id]);
        $bundle = Bundle::factory()->create(['store_id' => $store->id]);

        $kupon = Coupon::factory()->create([
            'bundle_id' => $bundle->id,
            'receiver_email' => 'test@example.com',
            'send_date' => now()->subHour(),
            'expires_at' => now()->addDay(),
            'email_sent_at' => null,
        ]);

        app(BundleService::class)->deleteBundle($bundle);

        \Illuminate\Support\Facades\Mail::fake();

        $this->artisan('app:send-scheduled-coupons');

        \Illuminate\Support\Facades\Mail::assertNothingSent();
    }
}
