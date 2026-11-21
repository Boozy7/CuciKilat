<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ServiceController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $services = Service::query()
            ->orderBy('id')
            ->get();

        return ServiceResource::collection($services);
    }

    public function store(StoreServiceRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $service = Service::create([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'pricing_type' => $validated['pricing_type'],
            'base_price' => $validated['base_price'],
            'surcharge_percent' => $validated['surcharge_percent'] ?? 0,
            'estimated_days' => $validated['estimated_days'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return (new ServiceResource($service))
            ->additional(['message' => 'Layanan berhasil ditambahkan.'])
            ->response()
            ->setStatusCode(201);
    }

    public function show(Service $service): ServiceResource
    {
        return new ServiceResource($service);
    }

    public function update(UpdateServiceRequest $request, Service $service): ServiceResource
    {
        $service->fill($request->validated());
        $service->save();

        return (new ServiceResource($service->refresh()))
            ->additional(['message' => 'Layanan berhasil diperbarui.']);
    }

    public function destroy(Service $service): JsonResponse
    {
        if ($service->orders()->exists()) {
            $service->update(['is_active' => false]);

            return response()->json([
                'action' => 'deactivated',
                'message' => 'Layanan sudah dipakai pada riwayat order sehingga dinonaktifkan (is_active = false) untuk menjaga riwayat transaksi.',
                'data' => new ServiceResource($service->refresh()),
            ]);
        }

        $service->delete();

        return response()->json([
            'action' => 'deleted',
            'message' => 'Layanan berhasil dihapus.',
        ]);
    }
}
