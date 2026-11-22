<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\DailyRevenueReportRequest;
use App\Http\Resources\StaffOrderResource;
use App\Services\DailyRevenueReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function dailyRevenue(
        DailyRevenueReportRequest $request,
        DailyRevenueReportService $reportService,
    ): JsonResponse|View {
        $report = $reportService->forDate($request->validated('date'));

        if ($request->expectsJson()) {
            return response()->json([
                'date' => $report['date'],
                'timezone' => $report['timezone'],
                'total_revenue' => $report['total_revenue'],
                'paid_orders_count' => $report['paid_orders_count'],
                'orders' => StaffOrderResource::collection($report['orders'])->resolve($request),
            ]);
        }

        return view('staff.reports.daily-revenue', [
            'date' => $report['date'],
            'timezone' => $report['timezone'],
            'totalRevenue' => $report['total_revenue'],
            'paidOrdersCount' => $report['paid_orders_count'],
            'orders' => $report['orders'],
        ]);
    }
}
