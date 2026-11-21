<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Order;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;

class DailyRevenueReportService
{
    public const REPORT_TIMEZONE = 'Asia/Jakarta';

    /**
     * @return array{
     *     date: string,
     *     timezone: string,
     *     total_revenue: int,
     *     paid_orders_count: int,
     *     orders: Collection<int, Order>,
     * }
     */
    public function forDate(?string $date = null): array
    {
        $targetDate = $date ?? now(self::REPORT_TIMEZONE)->toDateString();

        $startOfDayWib = CarbonImmutable::createFromFormat('Y-m-d', $targetDate, self::REPORT_TIMEZONE)
            ->startOfDay();
        $endOfDayWib = $startOfDayWib->endOfDay();

        $orders = Order::query()
            ->with('service')
            ->where('payment_status', PaymentStatus::Paid)
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [$startOfDayWib->utc(), $endOfDayWib->utc()])
            ->orderBy('paid_at')
            ->get();

        return [
            'date' => $startOfDayWib->toDateString(),
            'timezone' => self::REPORT_TIMEZONE,
            'total_revenue' => (int) $orders->sum('final_amount'),
            'paid_orders_count' => $orders->count(),
            'orders' => $orders,
        ];
    }
}
