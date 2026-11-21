<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\PublicOrderTrackingResource;
use App\Http\Resources\ServiceResource;
use App\Models\Order;
use App\Models\Service;
use App\Services\OrderWorkflowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PublicOrderController extends Controller
{
    public function services(): AnonymousResourceCollection
    {
        $services = Service::query()
            ->active()
            ->orderBy('id')
            ->get();

        return ServiceResource::collection($services);
    }

    public function store(StoreOrderRequest $request, OrderWorkflowService $workflow): JsonResponse
    {
        $validated = $request->validated();

        /** @var Service $service */
        $service = Service::query()->findOrFail($validated['service_id']);

        $order = $workflow->createPickupOrder($service, [
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'pickup_address' => $validated['pickup_address'],
        ]);

        return (new PublicOrderTrackingResource($order))
            ->additional([
                'message' => 'Permintaan jemput berhasil dibuat. Simpan kode order Anda untuk melacak cucian.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function track(string $orderCode): PublicOrderTrackingResource|JsonResponse
    {
        $normalizedCode = strtoupper(trim($orderCode));

        $order = Order::query()
            ->with('service')
            ->where('order_code', $normalizedCode)
            ->first();

        if ($order === null) {
            return response()->json([
                'message' => 'Kode order tidak ditemukan.',
            ], 404);
        }

        return new PublicOrderTrackingResource($order);
    }
}
