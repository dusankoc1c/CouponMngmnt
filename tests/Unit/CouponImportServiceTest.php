<?php

namespace Tests\Unit;

use App\Services\CouponImportService;
use Tests\TestCase;

class CouponImportServiceTest extends TestCase
{
    private CouponImportService $couponImportService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->couponImportService = new CouponImportService();
    }

    public function test_ispravan_red_prolazi_validaciju(): void
    {
        $row = ['', 'KOD123', 'Marko Markovic', 'marko@example.com', '50', '2026-10-01', 'not_used', '', '2026-12-01'];

        $rezultat = $this->couponImportService->sanitizeAndValidateInput($row, 0);

        $this->assertTrue($rezultat['valid']);
        $this->assertNull($rezultat['reason']);
        $this->assertEquals('KOD123', $rezultat['data']['code']);
        $this->assertEquals(50.0, $rezultat['data']['discount_amount']);
        $this->assertEquals('marko@example.com', $rezultat['data']['receiver_email']);
    }

    public function test_negativan_iznos_ne_prolazi(): void
    {
        $row = ['', 'KOD123', 'Marko Markovic', 'marko@example.com', '-50', '2026-10-01', 'not_used', '', '2026-12-01'];

        $rezultat = $this->couponImportService->sanitizeAndValidateInput($row, 0);

        $this->assertFalse($rezultat['valid']);
        $this->assertEquals('Iznos ne sme biti negativan', $rezultat['reason']);
        $this->assertNull($rezultat['data']);
    }

    public function test_nulti_iznos_prolazi(): void
    {
        $row = ['', 'KOD123', 'Marko Markovic', 'marko@example.com', '0', '2026-10-01', 'not_used', '', '2026-12-01'];

        $rezultat = $this->couponImportService->sanitizeAndValidateInput($row, 0);

        $this->assertTrue($rezultat['valid']);
        $this->assertEquals(0.0, $rezultat['data']['discount_amount']);
    }

    public function test_iznos_koji_nije_broj_ne_prolazi(): void
    {
        $row = ['', 'KOD123', 'Marko Markovic', 'marko@example.com', 'abc', '2026-10-01', 'not_used', '', '2026-12-01'];

        $rezultat = $this->couponImportService->sanitizeAndValidateInput($row, 0);

        $this->assertFalse($rezultat['valid']);
        $this->assertEquals('Iznos nije broj', $rezultat['reason']);
    }

    public function test_prazan_kod_ne_prolazi(): void
    {
        $row = ['', '', 'Marko Markovic', 'marko@example.com', '50', '2026-10-01', 'not_used', '', '2026-12-01'];

        $rezultat = $this->couponImportService->sanitizeAndValidateInput($row, 0);

        $this->assertFalse($rezultat['valid']);
        $this->assertEquals('Kod kupona je obavezan', $rezultat['reason']);
    }

    public function test_neispravan_email_ne_prolazi(): void
    {
        $row = ['', 'KOD123', 'Marko Markovic', 'nije-email', '50', '2026-10-01', 'not_used', '', '2026-12-01'];

        $rezultat = $this->couponImportService->sanitizeAndValidateInput($row, 0);

        $this->assertFalse($rezultat['valid']);
        $this->assertEquals('Email primaoca nije validan', $rezultat['reason']);
    }

    public function test_prazan_email_je_dozvoljen(): void
    {
        $row = ['', 'KOD123', 'Marko Markovic', '', '50', '2026-10-01', 'not_used', '', '2026-12-01'];

        $rezultat = $this->couponImportService->sanitizeAndValidateInput($row, 0);

        $this->assertTrue($rezultat['valid']);
        $this->assertNull($rezultat['data']['receiver_email']);
    }

    public function test_status_used_postavlja_is_used_na_true(): void
    {
        $row = ['', 'KOD123', 'Marko Markovic', 'marko@example.com', '50', '2026-10-01', 'USED', '', '2026-12-01'];

        $rezultat = $this->couponImportService->sanitizeAndValidateInput($row, 0);

        $this->assertTrue($rezultat['data']['is_used']);
    }

    public function test_prazan_datum_isteka_postaje_null(): void
    {
        $row = ['', 'KOD123', 'Marko Markovic', 'marko@example.com', '50', '2026-10-01', 'not_used', '', ''];

        $rezultat = $this->couponImportService->sanitizeAndValidateInput($row, 0);

        $this->assertTrue($rezultat['valid']);
        $this->assertNull($rezultat['data']['expires_at_raw']);
    }

    public function test_column_ofset_pomera_indekse(): void
    {
        $row = ['Moja Prodavnica', '', 'KOD123', 'Marko Markovic', 'marko@example.com', '50', '2026-10-01', 'not_used', '', '2026-12-01'];

        $rezultat = $this->couponImportService->sanitizeAndValidateInput($row, 1);

        $this->assertTrue($rezultat['valid']);
        $this->assertEquals('KOD123', $rezultat['data']['code']);
    }
}
