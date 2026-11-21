<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\DailyRevenueReportRequest;
use App\Http\Resources\StaffOrderResource;
use App\Services\DailyRevenueReportService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function dailyRevenue(
        DailyRevenueReportRequest $request,
        DailyRevenueReportService $reportService,
    ): JsonResponse {
        $report = $reportService->forDate($request->validated('date'));

        return response()->json([
            'date' => $report['date'],
            'timezone' => $report['timezone'],
            'total_revenue' => $report['total_revenue'],
            'paid_orders_count' => $report['paid_orders_count'],
            'orders' => StaffOrderResource::collection($report['orders'])->resolve($request),
        ]);
    }
}
