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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection|View
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

        $orders = $query->get();

        if ($request->expectsJson()) {
            return StaffOrderResource::collection($orders);
        }

        return view('staff.orders.index', compact('orders'));
    }

    public function show(Order $order): StaffOrderResource|View
    {
        $order->load('service');

        if (request()->expectsJson()) {
            return new StaffOrderResource($order);
        }

        return view('staff.orders.show', compact('order'));
    }

    public function updateStatus(
        UpdateOrderStatusRequest $request,
        Order $order,
        OrderWorkflowService $workflow,
    ): StaffOrderResource|RedirectResponse {
        $targetStatus = OrderStatus::from($request->validated('status'));
        $updated = $workflow->transitionStatus($order, $targetStatus);

        if ($request->expectsJson()) {
            return (new StaffOrderResource($updated))
                ->additional(['message' => 'Status order berhasil diperbarui.']);
        }

        return redirect()
            ->route('staff.orders.show', $order)
            ->with('success', 'Status order berhasil diperbarui ke: ' . $targetStatus->label());
    }

    public function weigh(
        WeighOrderRequest $request,
        Order $order,
        OrderWorkflowService $workflow,
    ): StaffOrderResource|RedirectResponse {
        $validated = $request->validated();

        $updated = $workflow->weighAndBill(
            order: $order,
            actualWeightGrams: isset($validated['actual_weight_grams']) ? (int) $validated['actual_weight_grams'] : null,
            itemCount: isset($validated['item_count']) ? (int) $validated['item_count'] : null,
        );

        if ($request->expectsJson()) {
            return (new StaffOrderResource($updated))
                ->additional(['message' => 'Penimbangan dan perhitungan tagihan berhasil disimpan.']);
        }

        return redirect()
            ->route('staff.orders.show', $order)
            ->with('success', 'Penimbangan dan perhitungan tagihan berhasil disimpan.');
    }

    public function updatePayment(
        UpdateOrderPaymentRequest $request,
        Order $order,
        OrderWorkflowService $workflow,
    ): StaffOrderResource|RedirectResponse {
        $targetPaymentStatus = PaymentStatus::from($request->validated('payment_status'));
        $updated = $workflow->updatePaymentStatus($order, $targetPaymentStatus);

        if ($request->expectsJson()) {
            return (new StaffOrderResource($updated))
                ->additional(['message' => 'Status pembayaran berhasil diperbarui.']);
        }

        return redirect()
            ->route('staff.orders.show', $order)
            ->with('success', 'Status pembayaran berhasil diperbarui ke: ' . $targetPaymentStatus->label());
    }
}
