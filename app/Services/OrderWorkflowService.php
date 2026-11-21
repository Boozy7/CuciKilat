<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Service;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderWorkflowService
{
    private const ORDER_CODE_ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    public function __construct(
        private readonly OrderPricingCalculator $calculator,
    ) {}

    /**
     * @param  array{customer_name: string, customer_phone: string, pickup_address: string}  $customerData
     */
    public function createPickupOrder(Service $service, array $customerData): Order
    {
        if (! $service->is_active) {
            throw ValidationException::withMessages([
                'service_id' => ['Layanan yang dipilih sedang tidak aktif.'],
            ]);
        }

        $order = Order::create([
            'order_code' => $this->generateUniqueOrderCode(),
            'service_id' => $service->id,
            'customer_name' => $customerData['customer_name'],
            'customer_phone' => $customerData['customer_phone'],
            'pickup_address' => $customerData['pickup_address'],
            'status' => OrderStatus::WaitingPickup,
            'actual_weight_grams' => null,
            'billed_weight_kg' => null,
            'item_count' => null,
            'applied_unit_price' => null,
            'applied_surcharge_percent' => null,
            'final_amount' => null,
            'weighed_at' => null,
            'estimated_completion_at' => null,
            'delivered_at' => null,
            'payment_status' => PaymentStatus::Unpaid,
            'paid_at' => null,
        ]);

        return $order->load('service');
    }

    public function weighAndBill(
        Order $order,
        ?int $actualWeightGrams = null,
        ?int $itemCount = null,
        ?CarbonInterface $weighedAt = null,
    ): Order {
        $this->ensureNotDelivered($order);

        if ($order->status !== OrderStatus::PickedUp) {
            throw ValidationException::withMessages([
                'status' => ['Penimbangan dan perhitungan tagihan hanya dapat dilakukan saat status order sudah dijemput (picked_up).'],
            ]);
        }

        if ($order->isWeighed()) {
            throw ValidationException::withMessages([
                'order' => ['Order ini sudah ditimbang dan memiliki tagihan final.'],
            ]);
        }

        /** @var Service $service */
        $service = $order->service()->firstOrFail();

        if ($service->isPerKg() && ($actualWeightGrams === null || $actualWeightGrams < 1)) {
            throw ValidationException::withMessages([
                'actual_weight_grams' => ['Berat aktual dalam gram wajib diisi minimal 1 gram untuk layanan kiloan.'],
            ]);
        }

        if ($service->isPerItem() && ($itemCount === null || $itemCount < 1)) {
            throw ValidationException::withMessages([
                'item_count' => ['Jumlah item wajib diisi minimal 1 untuk layanan satuan.'],
            ]);
        }

        $snapshot = $this->calculator->calculate(
            service: $service,
            actualWeightGrams: $actualWeightGrams,
            itemCount: $itemCount,
            weighedAt: $weighedAt,
        );

        DB::transaction(function () use ($order, $snapshot): void {
            $order->fill($snapshot);
            $order->save();
        });

        return $order->refresh()->load('service');
    }

    public function transitionStatus(Order $order, OrderStatus $targetStatus): Order
    {
        $this->ensureNotDelivered($order);

        if (! $order->status->canTransitionTo($targetStatus)) {
            $expected = $order->status->next()?->value ?? 'tidak ada';

            throw ValidationException::withMessages([
                'status' => [
                    "Perpindahan status dari {$order->status->value} ke {$targetStatus->value} tidak diperbolehkan. Status berikutnya yang valid: {$expected}.",
                ],
            ]);
        }

        if ($targetStatus === OrderStatus::Processing && ! $order->isWeighed()) {
            throw ValidationException::withMessages([
                'status' => ['Penimbangan dan tagihan wajib diselesaikan sebelum order masuk tahap proses (processing).'],
            ]);
        }

        $order->status = $targetStatus;

        if ($targetStatus === OrderStatus::Delivered) {
            $order->delivered_at = now();
        }

        $order->save();

        return $order->refresh()->load('service');
    }

    public function updatePaymentStatus(
        Order $order,
        PaymentStatus $targetPaymentStatus,
        ?CarbonInterface $paidAt = null,
    ): Order {
        $this->ensureNotDelivered($order);

        if ($targetPaymentStatus !== PaymentStatus::Unpaid && ! $order->isWeighed()) {
            throw ValidationException::withMessages([
                'payment_status' => ['Tagihan wajib dihitung terlebih dahulu sebelum mencatat pembayaran.'],
            ]);
        }

        $order->payment_status = $targetPaymentStatus;
        $order->paid_at = $targetPaymentStatus === PaymentStatus::Paid
            ? ($paidAt ?? $order->paid_at ?? now())
            : null;

        $order->save();

        return $order->refresh()->load('service');
    }

    private function ensureNotDelivered(Order $order): void
    {
        if ($order->isDelivered()) {
            throw ValidationException::withMessages([
                'order' => ['Order yang sudah diserahkan tidak dapat diubah.'],
            ]);
        }
    }

    private function generateUniqueOrderCode(): string
    {
        $datePrefix = now(DailyRevenueReportService::REPORT_TIMEZONE)->format('Ymd');
        $alphabetLength = strlen(self::ORDER_CODE_ALPHABET);

        do {
            $suffix = '';

            for ($i = 0; $i < 6; $i++) {
                $suffix .= self::ORDER_CODE_ALPHABET[random_int(0, $alphabetLength - 1)];
            }

            $code = "CK-{$datePrefix}-{$suffix}";
        } while (Order::where('order_code', $code)->exists());

        return $code;
    }
}
