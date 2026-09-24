<?php

namespace Tests\Unit;

use App\Models\Bundle;
use App\Models\Coupon;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponIsExpired extends TestCase
{
    use RefreshDatabase;

    public function test_kupon_sa_proslim_datumom_je_istekao(): void
    {
        $kupon = $this->kreirajKupon(['expires_at' => now()->subDay()]);

        $this->assertTrue($kupon->is_expired);
    }

    public function test_kupon_sa_buducim_datumom_nije_istekao(): void
    {
        $kupon = $this->kreirajKupon(['expires_at' => now()->addDay()]);

        $this->assertFalse($kupon->is_expired);
    }

    public function test_kupon_bez_datuma_isteka_nije_istekao(): void
    {
        $kupon = $this->kreirajKupon(['expires_at' => null]);

        $this->assertFalse($kupon->is_expired);
    }

    private function kreirajKupon(array $podaci): Coupon
    {
        $korisnik = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $korisnik->id]);
        $bundle = Bundle::factory()->create(['store_id' => $store->id]);

        return Coupon::factory()->create(array_merge([
            'bundle_id' => $bundle->id,
        ], $podaci));
    }
}
