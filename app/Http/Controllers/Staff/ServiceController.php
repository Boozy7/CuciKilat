<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): AnonymousResourceCollection|View
    {
        $services = Service::query()
            ->orderBy('id')
            ->get();

        if (request()->expectsJson()) {
            return ServiceResource::collection($services);
        }

        return view('staff.services.index', compact('services'));
    }

    public function store(StoreServiceRequest $request): JsonResponse|RedirectResponse
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

        if ($request->expectsJson()) {
            return (new ServiceResource($service))
                ->additional(['message' => 'Layanan berhasil ditambahkan.'])
                ->response()
                ->setStatusCode(201);
        }

        return redirect()
            ->route('staff.services.index')
            ->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function show(Service $service): ServiceResource|View
    {
        if (request()->expectsJson()) {
            return new ServiceResource($service);
        }

        return view('staff.services.show', compact('service'));
    }

    public function update(UpdateServiceRequest $request, Service $service): ServiceResource|RedirectResponse
    {
        $service->fill($request->validated());
        $service->save();

        if ($request->expectsJson()) {
            return (new ServiceResource($service->refresh()))
                ->additional(['message' => 'Layanan berhasil diperbarui.']);
        }

        return redirect()
            ->route('staff.services.index')
            ->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy(Service $service): JsonResponse|RedirectResponse
    {
        if ($service->orders()->exists()) {
            $service->update(['is_active' => false]);

            if (request()->expectsJson()) {
                return response()->json([
                    'action' => 'deactivated',
                    'message' => 'Layanan sudah dipakai pada riwayat order sehingga dinonaktifkan (is_active = false) untuk menjaga riwayat transaksi.',
                    'data' => new ServiceResource($service->refresh()),
                ]);
            }

            return redirect()
                ->route('staff.services.index')
                ->with('success', 'Layanan sudah memiliki riwayat order sehingga dinonaktifkan.');
        }

        $service->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'action' => 'deleted',
                'message' => 'Layanan berhasil dihapus.',
            ]);
        }

        return redirect()
            ->route('staff.services.index')
            ->with('success', 'Layanan berhasil dihapus.');
    }
}
