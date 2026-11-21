<?php

namespace App\Http\Controllers\Staff;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderPaymentRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Requests\WeighOrderRequest;
use App\Http\Resources\StaffOrderResource;
use App\Models\Order;
use App\Services\OrderWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Order::query()
            ->with('service')
            ->latest();

        if ($status = $request->string('status')->trim()->value()) {
            $query->where('status', $status);
        }

        if ($paymentStatus = $request->string('payment_status')->trim()->value()) {
            $query->where('payment_status', $paymentStatus);
        }

        if ($search = $request->string('search')->trim()->value()) {
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('order_code', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        return StaffOrderResource::collection($query->get());
    }

    public function show(Order $order): StaffOrderResource
    {
        return new StaffOrderResource($order->load('service'));
    }

    public function updateStatus(
        UpdateOrderStatusRequest $request,
        Order $order,
        OrderWorkflowService $workflow,
    ): StaffOrderResource {
        $targetStatus = OrderStatus::from($request->validated('status'));
        $updated = $workflow->transitionStatus($order, $targetStatus);

        return (new StaffOrderResource($updated))
            ->additional(['message' => 'Status order berhasil diperbarui.']);
    }

    public function weigh(
        WeighOrderRequest $request,
        Order $order,
        OrderWorkflowService $workflow,
    ): StaffOrderResource {
        $validated = $request->validated();

        $updated = $workflow->weighAndBill(
            order: $order,
            actualWeightGrams: isset($validated['actual_weight_grams']) ? (int) $validated['actual_weight_grams'] : null,
            itemCount: isset($validated['item_count']) ? (int) $validated['item_count'] : null,
        );

        return (new StaffOrderResource($updated))
            ->additional(['message' => 'Penimbangan dan perhitungan tagihan berhasil disimpan.']);
    }

    public function updatePayment(
        UpdateOrderPaymentRequest $request,
        Order $order,
        OrderWorkflowService $workflow,
    ): StaffOrderResource {
        $targetPaymentStatus = PaymentStatus::from($request->validated('payment_status'));
        $updated = $workflow->updatePaymentStatus($order, $targetPaymentStatus);

        return (new StaffOrderResource($updated))
            ->additional(['message' => 'Status pembayaran berhasil diperbarui.']);
    }
}
