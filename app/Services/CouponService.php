<?php

namespace App\Services;

use App\Helpers\CouponHelper;
use App\Models\Bundle;
use App\Models\Coupon;
use App\Repositories\Contracts\CouponRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class CouponService
{
    public function __construct(
        private CouponRepositoryInterface $couponRepository,
        private StoreService $storeService){}

    public function createCoupon(Bundle $bundle, array $data): Coupon
    {
        $this->storeService->assertCanAddValue($bundle->store, (float)$data['discount_amount']);

        $coupon = $this->couponRepository->create($this->buildCouponAttributes($bundle, $data));

        $coupon->sendInititalMail();

        return $coupon;
    }

    public function createCouponInBundle(Bundle $bundle, array $couponData): bool
    {
        $coupon = $this->couponRepository->create($this->buildCouponAttributes($bundle, $couponData));

        return $coupon->sendInititalMail();
    }

    public function createTierCoupon(Bundle $bundle, float $amount, ?string $expiresAtInput): Coupon
    {
        $expiresAt = $this->resolveExpiresAt($bundle, $expiresAtInput);

        return $this->couponRepository->create([
            'bundle_id' => $bundle->id,
            'code' => CouponHelper::generateCode($bundle->name),
            'discount_amount' => $amount,
            'receiver_name'=>null,
            'receiver_email'=>null,
            'send_date'=>null,
            'expires_at'=>$expiresAt,
        ]);
    }

    public function updateCoupon(Coupon $coupon, array $data): Coupon
    {
        $oldAmount = (float) $coupon->discount_amount;
        $newAmount = (float) $data['discount_amount'];

        if($newAmount - $oldAmount > 0){
            $this->storeService->assertCanAddValue($coupon->bundle->store, $newAmount - $oldAmount);
        }

        $coupon = $this->couponRepository->update($coupon, $data);

        $coupon->sendInititalMail();

        return $coupon;
    }

    public function deleteCoupon(Coupon $coupon): void
    {
        $this->couponRepository->delete($coupon);
    }

    public function toggleUsedStatus(Coupon $coupon): Coupon
    {
        $newStatus = !$coupon->is_used;

        if ($newStatus) {
            $usedAt = now();
        } else {
            $usedAt = null;
        }

        $coupon = $this->couponRepository->update($coupon, [
            'is_used' => $newStatus,
            'used_at' => $usedAt,
        ]);

        return $coupon;
    }

    public function unsubscribeCoupon(Coupon $coupon): void
    {
        $this->couponRepository->update($coupon, [
            'subscribed' => false,
        ]);
    }

    private function buildCouponAttributes(Bundle $bundle, array $couponData): array
    {
        $expiresAtInput = $couponData['expires_at'] ?? null;
        $expiresAt = $this->resolveExpiresAt($bundle, $expiresAtInput);
        return [
            'bundle_id' => $bundle->id,
            'code' => CouponHelper::generateCode($bundle->name),
            'discount_amount' => $couponData['discount_amount'],
            'receiver_name' => $couponData['receiver_name'],
            'receiver_email' => $couponData['receiver_email'],
            'send_date' => $couponData['send_date'] ?? null,
            'expires_at' => $expiresAt,
        ];
    }

    public function importCsv(Bundle $bundle, UploadedFile $file): array
    {
        $handle = fopen($file->getRealPath(), 'r');

        $rowIndex = 0;
        $rowsToImport = [];
        $skippedCount = 0;
        $totalNewAmount = 0;

        while(($row = fgetcsv($handle)) !== false){
            $rowIndex++;

            \Log::info('Red ' . $rowIndex . ': ' . json_encode($row));

            if($rowIndex === 1){
                continue;
            }

            $code = trim($row[1]);
            $receiverName = trim($row[2]);
            $receiverEmail = trim($row[3]);
            $amount = trim($row[4]);
            $sendDateRaw = trim($row[5]);
            $statusRaw = trim($row[6]);
            $createdAtRaw = trim($row[7]);

            if(isset($row[8])){
                $expiresAtRaw = trim($row[8]);
            }else{
                $expiresAtRaw = '';
            }

            $codeExists = $this->couponRepository->findByCode($code);

            if($codeExists != null ){
                $skipedCount++;
                continue;
            }

            $expiresAt = $this->resolveExpiresAt($bundle, $expiresAtRaw == '' ? null : $expiresAtRaw);

            if($sendDateRaw == ''){
                $sendDate = null;
            }else{
                $sendDate = $sendDateRaw;
            }

            if($receiverEmail == ''){
                $receiverEmail = null;
            }

            if($receiverName == ''){
                $receiverName = null;
;
            }

            if(strtolower($statusRaw) == 'used'){
                $isUsed = true;
            }else{
                $isUsed = false;
            }

            $rowsToImport[] = [
                'code' => $code,
                'receiver_name' => $receiverName,
                'receiver_email' => $receiverEmail,
                'discount_amount' => $amount,
                'send_date' => $sendDate,
                'expires_at' => $expiresAt,
                'is_used' => $isUsed,
                'created_at_override' => $createdAtRaw,
            ];

            $totalNewAmount += (float) $amount;
        }

        fclose($handle);

        $this->storeService->assertCanAddValue($bundle->store, (float)$totalNewAmount);

        $importedCount = 0;

        foreach($rowsToImport as $row){
            $coupon = $this->couponRepository->create([
                'bundle_id' => $bundle->id,
                'code' => $row['code'],
                'discount_amount' => $row['discount_amount'],
                'receiver_name' => $row['receiver_name'],
                'receiver_email' => $row['receiver_email'],
                'send_date' => $row['send_date'],
                'expires_at' => $row['expires_at'],
                'is_used' => $row['is_used'],
            ]);

            if($row['created_at_override'] != ''){
                $coupon->created_at  = $row['created_at_override'];
                $coupon->save();
            }

            $importedCount++;
        }

        return [
            'imported' => $importedCount,
            'skipped' => $skippedCount,
        ];
    }

    public function resolveExpiresAt(Bundle $bundle, $expiresAtRaw) : string
    {
        if($expiresAtRaw != null && $expiresAtRaw != '')
        {
            return $expiresAtRaw;
        }

        if($bundle->expires_at != null)
        {
            return $bundle->expires_at;
        }

        throw ValidationException::withMessages([
            'expires_at' => 'Datum isteka je obavezan',
        ]);
    }
}
