<?php

namespace App\Services;

use App\Helpers\CouponHelper;
use App\Mail\CouponReminder;
use App\Mail\MyEmail;
use App\Models\Bundle;
use App\Models\Coupon;
use App\Repositories\Contracts\CouponRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Mail;

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
            'send_immediately' => $couponData['send_immediately'] ?? false,
        ];
    }

    public function couponMatchRow(Coupon $coupon, array $rowData): bool
    {
        if($coupon->receiver_name != $rowData['receiver_name']){
            return false;
        }
        if($coupon->receiver_email != $rowData['receiver_email']){
            return false;
        }
        if($coupon->discount_amount != $rowData['discount_amount']){
            return false;
        }
        $couponSndDate = $coupon->send_date ? $coupon->send_date->format('Y-m-d') : null;
        $rowSndDate = $rowData['send_date'] ? date('Y-m-d', strtotime($rowData['send_date'])) : null;

        if($couponSndDate != $rowSndDate){
            return false;
        }

        $couponExp = $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : null;
        $rowExp = $rowData['expires_at'] ? date('Y-m-d', strtotime($rowData['expires_at'])) : null;

        if($couponExp != $rowExp){
            return false;
        }

        if($coupon->is_used !== $rowData['is_used']){
            return false;
        }

        return true;
    }

    public function importCsv(Bundle $bundle, UploadedFile $file): array
    {
        $handle = fopen($file->getRealPath(), 'r');

        $rowIndex = 0;

        $totalNewAmount = 0;
        $columnOfset = 0;

        $rowsToCreate = [];
        $rowsToRestore = [];
        $conflictedRows = [];

        $rowsToMove = [];

        $skippedCount = 0;

        while(($row = fgetcsv($handle)) !== false){
            $rowIndex++;

            \Log::info('Red ' . $rowIndex . ': ' . json_encode($row));

            if($rowIndex === 1){
                if(isset($row[0]) && trim($row[0]) == 'Store Name'){
                    $columnOfset = 1;
                }
                continue;
            }

            $code = trim($row[1 + $columnOfset]);
            $receiverName = trim($row[2 + $columnOfset]);
            $receiverEmail = trim($row[3 + $columnOfset]);
            $amount = trim($row[4 + $columnOfset]);
            $sendDateRaw = trim($row[5 + $columnOfset]);
            $statusRaw = trim($row[6 + $columnOfset]);
            $createdAtRaw = trim($row[7 + $columnOfset]);

            if (!is_numeric($amount)) {
                $skippedCount++;
                continue;
            }

            if(isset($row[8 + $columnOfset])){
                $expiresAtRaw = trim($row[8 + $columnOfset]);
            }else{
                $expiresAtRaw = '';
            }

//            $codeExists = $this->couponRepository->findByCode($code);
//
//            if($codeExists != null ){
//                $skippedCount++;
//                continue;
//            }

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

            $rowData = [
                'code' => $code,
                'receiver_name' => $receiverName,
                'receiver_email' => $receiverEmail,
                'discount_amount' => $amount,
                'send_date' => $sendDate,
                'expires_at' => $expiresAt,
                'is_used' => $isUsed,
                'created_at_override' => $createdAtRaw,
            ];

            $existingCoupon = $this->couponRepository->findByCodeIncludingTrashed($code);
            if($existingCoupon == null){
                $rowsToCreate[] = $rowData;
                $totalNewAmount += (float)$amount;
                continue;
            }

            $fieldsMatch = $this->couponMatchRow($existingCoupon, $rowData);
            if(!$fieldsMatch){
                $conflictedRows[] = $code;
                continue;
            }

            if($existingCoupon->trashed()){
                $rowsToRestore[] = $existingCoupon;
                $totalNewAmount += (float)$amount;
            }else{
                $skippedCount++;
            }

            $oldBundle = Bundle::withTrashed()->find($existingCoupon->bundle_id);
            if($oldBundle != null && $oldBundle->trashed()){
                $rowsToMove[] = $existingCoupon;
                $totalNewAmount += (float)$amount;
                continue;
            }else{
                $skippedCount++;
            }
        }

        fclose($handle);

        if(!empty($conflictedRows)){
            throw ValidationException::withMessages([
                'csv_file' => "Kodovi vec postoje sa drugacijim info" . implode(", ", $conflictedRows),
            ]);
        }

        $this->storeService->assertCanAddValue($bundle->store, (float)$totalNewAmount);

        $importedCount = 0;

        foreach($rowsToCreate as $row){
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

        $restoredCount = 0;

        foreach($rowsToRestore as $row){
            $this->couponRepository->restore($row);
            $restoredCount++;
        }

        foreach($rowsToMove as $row){
            $this->couponRepository->update($row, ['bundle_id' => $bundle->id]);
            $restoredCount++;
        }

        return [
            'importedCount' => $importedCount,
            'restoredCount' => $restoredCount,
            'skippedCount' => $skippedCount,
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


    public function assertResend(Coupon $coupon) : void
    {
        if($coupon->receiver_email == null){
            throw ValidationException::withMessages([
                'receiver_email' => 'Nema mail primaoca',
            ]);
        }

        if($coupon->is_used == true){
            throw ValidationException::withMessages([
                'is_used' => "Kupon je iskoriscen"
            ]);
        }

        if($coupon->is_expired == true){
            throw ValidationException::withMessages([
                'is_expired' => "Kupon je zastareo"
            ]);
        }
    }
    public function resendInitialMail(Coupon $coupon) : void
    {
        $this->assertResend($coupon);
        Mail::to($coupon->receiver_email)->send(new MyEmail($coupon));
        $coupon->email_sent_at = now();
        $coupon->save();
    }
    public function resendReminderMail(Coupon $coupon) : void
    {
        $this->assertResend($coupon);
        Mail::to($coupon->receiver_email)->send(new CouponReminder($coupon));
        $coupon->last_sent_at = now();
        $coupon->save();
    }
    public function resendNeededMail(Coupon $coupon) : bool
    {
        if($coupon->receiver_email == null){
            return false;
        }
        if($coupon->is_used){
            return false;
        }
        if($coupon->is_expired){
            return false;
        }
        if ($coupon->email_sent_at == null) {
            Mail::to($coupon->receiver_email)->send(new MyEmail($coupon));
            $coupon->email_sent_at = now();
            $coupon->save();
        } else {
            Mail::to($coupon->receiver_email)->send(new CouponReminder($coupon));
            $coupon->last_sent_at = now();
            $coupon->save();
        }

        return true;
    }
}
