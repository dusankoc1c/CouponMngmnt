<?php

namespace App\Helpers;

use App\Http\Requests\ExportAllCodesRequest;
use App\Models\Coupon;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExportHelper
{
    public static function buildCsvExport(Collection $coupons, bool $includeStoreColumn) : StreamedResponse
    {
        $filename = 'export-all-coupons-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($coupons, $includeStoreColumn) {
            $file = fopen('php://output', 'w');

            if ($includeStoreColumn) {
                fputcsv($file, ['Store Name', 'Bundle Name', 'Code', 'Receiver Name', 'Receiver Email', 'Amount', 'Send Date',  'Send Immediately', 'Status', 'Created At', 'Expires At']);
            } else {
                fputcsv($file, ['Bundle Name', 'Code', 'Receiver Name', 'Receiver Email', 'Amount', 'Send Date', 'Send Immediately', 'Status', 'Expires At']);
            }

            foreach ($coupons as $coupon) {
                if ($coupon->is_used) {
                    $status = 'Used';
                } else {
                    $status = 'Not Used';
                }

                if ($coupon->send_date != null) {
                    $sendDate = $coupon->send_date->format('Y-m-d');
                } else {
                    $sendDate = '';
                }
                if($coupon->send_immediately){
                    $sendImmediately = 'True';
                }else{
                    $sendImmediately = 'False';
                }

                if ($includeStoreColumn) {
                    fputcsv($file, [
                        $coupon->bundle->store->name,
                        $coupon->bundle->name,
                        $coupon->code,
                        $coupon->{Coupon::RECEIVER_NAME},
                        $coupon->receiver_email,
                        $coupon->discount_amount,
                        $sendDate,
                        $sendImmediately,
                        $status,
                        $coupon->created_at->format('Y-m-d'),
                        $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : '',
                    ]);
                } else {
                    fputcsv($file, [
                        $coupon->bundle->name,
                        $coupon->code,
                        $coupon->receiver_name,
                        $coupon->receiver_email,
                        $coupon->discount_amount,
                        $sendDate,
                        $sendImmediately,
                        $status,
                        $coupon->created_at->format('Y-m-d'),
                        $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : '',
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
