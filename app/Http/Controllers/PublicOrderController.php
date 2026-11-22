<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\PublicOrderTrackingResource;
use App\Http\Resources\ServiceResource;
use App\Models\Order;
use App\Models\Service;
use App\Services\OrderWorkflowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\View\View;

class PublicOrderController extends Controller
{
    public function home(): View
    {
        $services = Service::query()->active()->orderBy('id')->get();

        return view('customer.home', compact('services'));
    }

    public function create(): View
    {
        $services = Service::query()->active()->orderBy('id')->get();

        return view('customer.order-create', compact('services'));
    }

    public function trackForm(Request $request): View
    {
        $orderCode = $request->string('order_code')->trim()->upper()->value();
        $order = null;

        if ($orderCode !== '') {
            $order = Order::query()->with('service')->where('order_code', $orderCode)->first();
        }

        return view('customer.track', compact('order', 'orderCode'));
    }

    public function orderSuccess(string $orderCode): View
    {
        $order = Order::query()->with('service')->where('order_code', strtoupper(trim($orderCode)))->firstOrFail();

        return view('customer.order-success', compact('order'));
    }

    public function services(): AnonymousResourceCollection|View
    {
        $services = Service::query()
            ->active()
            ->orderBy('id')
            ->get();

        if (request()->expectsJson()) {
            return ServiceResource::collection($services);
        }

        return view('customer.home', compact('services'));
    }

    public function store(StoreOrderRequest $request, OrderWorkflowService $workflow): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        /** @var Service $service */
        $service = Service::query()->findOrFail($validated['service_id']);

        $order = $workflow->createPickupOrder($service, [
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'pickup_address' => $validated['pickup_address'],
        ]);

        if ($request->expectsJson()) {
            return (new PublicOrderTrackingResource($order))
                ->additional([
                    'message' => 'Permintaan jemput berhasil dibuat. Simpan kode order Anda untuk melacak cucian.',
                ])
                ->response()
                ->setStatusCode(201);
        }

        return redirect()
            ->route('orders.success', $order->order_code)
            ->with('success', 'Permintaan jemput berhasil dibuat! Simpan kode order Anda.');
    }

    public function track(string $orderCode): PublicOrderTrackingResource|JsonResponse|View
    {
        $normalizedCode = strtoupper(trim($orderCode));

        $order = Order::query()
            ->with('service')
            ->where('order_code', $normalizedCode)
            ->first();

        if ($order === null) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Kode order tidak ditemukan.',
                ], 404);
            }

            return view('customer.track', [
                'order' => null,
                'orderCode' => $normalizedCode,
            ]);
        }

        if (request()->expectsJson()) {
            return new PublicOrderTrackingResource($order);
        }

        return view('customer.track', [
            'order' => $order,
            'orderCode' => $normalizedCode,
        ]);
    }
}
