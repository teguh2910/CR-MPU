<?php

namespace App\Http\Controllers;

use App\Exports\ReportMonthlyHistoryExport;
use App\Models\ReportMonthlyTransaction;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportMonthlyTransactionExportController extends Controller
{
    public function __invoke(int | string $year, string $remarksKey): BinaryFileResponse
    {
        $year = (int) $year;
        $remarks = $this->decodeRemarksKey($remarksKey);

        abort_if($year <= 0 || $remarks === '', 404);

        $exists = ReportMonthlyTransaction::query()
            ->where('year', $year)
            ->where('remarks', $remarks)
            ->exists();

        abort_unless($exists, 404);

        $filename = sprintf(
            'monthly-report-history-%d-%s.xlsx',
            $year,
            str($remarks)->slug('_')->limit(80, '')
        );

        return Excel::download(new ReportMonthlyHistoryExport($year, $remarks), $filename);
    }

    protected function decodeRemarksKey(string $remarksKey): string
    {
        $normalized = strtr($remarksKey, '-_', '+/');
        $padding = strlen($normalized) % 4;

        if ($padding > 0) {
            $normalized .= str_repeat('=', 4 - $padding);
        }

        $decoded = base64_decode($normalized, true);

        return is_string($decoded) ? trim($decoded) : '';
    }
}
