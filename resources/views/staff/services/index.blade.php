@extends('layouts.app')

@section('title', 'Kelola Layanan')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <h1 class="text-2xl font-bold">🏷️ Kelola Layanan</h1>
    <button onclick="document.getElementById('modal-create').classList.remove('hidden')"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
        + Tambah Layanan
    </button>
</div>

{{-- Daftar Layanan --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($services as $service)
        <div class="bg-white rounded-xl shadow border p-5 {{ !$service->is_active ? 'opacity-60' : '' }}">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <h3 class="font-bold text-lg">{{ $service->name }}</h3>
                    <p class="text-xs font-mono text-gray-400">{{ $service->code }}</p>
                </div>
                @if($service->is_active)
                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-700">Aktif</span>
                @else
                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-600">Nonaktif</span>
                @endif
            </div>
            <p class="text-sm text-gray-500 mb-2">{{ $service->pricing_type->label() }}</p>
            <p class="text-xl font-bold">
                Rp {{ number_format($service->base_price, 0, ',', '.') }}
                <span class="text-sm font-normal text-gray-400">/{{ $service->isPerKg() ? 'kg' : 'item' }}</span>
            </p>
            @if($service->surcharge_percent > 0)
                <p class="text-xs text-orange-500 mt-1">+{{ $service->surcharge_percent }}% surcharge</p>
            @endif
            <p class="text-xs text-gray-400 mt-1">Estimasi {{ $service->estimated_days }} hari</p>

            <div class="flex gap-2 mt-4">
                <button onclick="openEditModal({{ $service->id }}, '{{ $service->code }}', '{{ addslashes($service->name) }}', '{{ $service->pricing_type->value }}', {{ $service->base_price }}, {{ $service->surcharge_percent }}, {{ $service->estimated_days }}, {{ $service->is_active ? 'true' : 'false' }})"
                        class="flex-1 bg-yellow-50 hover:bg-yellow-100 text-yellow-700 py-1.5 rounded-lg text-xs font-semibold transition text-center">
                    ✏️ Edit
                </button>
                <form method="POST" action="{{ route('staff.services.destroy', $service) }}"
                      onsubmit="return confirm('Yakin ingin menghapus/nonaktifkan layanan ini?')" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-50 hover:bg-red-100 text-red-600 py-1.5 rounded-lg text-xs font-semibold transition">
                        🗑️ Hapus
                    </button>
                </form>
            </div>
        </div>
    @empty
        <p class="col-span-3 text-center text-gray-400">Belum ada layanan.</p>
    @endforelse
</div>

{{-- Modal Tambah Layanan --}}
<div id="modal-create" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold">Tambah Layanan Baru</h2>
            <button onclick="document.getElementById('modal-create').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
        </div>
        <form method="POST" action="{{ route('staff.services.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Kode <span class="text-red-500">*</span></label>
                <input type="text" name="code" required maxlength="32" placeholder="Contoh: PREMIUM" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm uppercase">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Layanan <span class="text-red-500">*</span></label>
                <input type="text" name="name" required maxlength="100" placeholder="Contoh: Premium Wash" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tipe Harga <span class="text-red-500">*</span></label>
                <select name="pricing_type" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    @foreach(\App\Enums\PricingType::cases() as $pt)
                        <option value="{{ $pt->value }}">{{ $pt->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Harga Dasar (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="base_price" required min="1" placeholder="7000" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Surcharge (%)</label>
                    <input type="number" name="surcharge_percent" min="0" max="100" value="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Estimasi Hari <span class="text-red-500">*</span></label>
                    <input type="number" name="estimated_days" required min="1" max="30" placeholder="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                    <select name="is_active" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-lg font-semibold transition">
                Simpan Layanan
            </button>
        </form>
    </div>
</div>

{{-- Modal Edit Layanan --}}
<div id="modal-edit" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold">Edit Layanan</h2>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
        </div>
        <form method="POST" id="form-edit" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Kode</label>
                <input type="text" name="code" id="edit-code" required maxlength="32" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm uppercase">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Layanan</label>
                <input type="text" name="name" id="edit-name" required maxlength="100" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tipe Harga</label>
                <select name="pricing_type" id="edit-pricing_type" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    @foreach(\App\Enums\PricingType::cases() as $pt)
                        <option value="{{ $pt->value }}">{{ $pt->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Harga Dasar (Rp)</label>
                    <input type="number" name="base_price" id="edit-base_price" required min="1" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Surcharge (%)</label>
                    <input type="number" name="surcharge_percent" id="edit-surcharge_percent" min="0" max="100" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Estimasi Hari</label>
                    <input type="number" name="estimated_days" id="edit-estimated_days" required min="1" max="30" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                    <select name="is_active" id="edit-is_active" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white py-2.5 rounded-lg font-semibold transition">
                Update Layanan
            </button>
        </form>
    </div>
</div>

<script>
function openEditModal(id, code, name, pricingType, basePrice, surchargePercent, estimatedDays, isActive) {
    document.getElementById('form-edit').action = '/staff/services/' + id;
    document.getElementById('edit-code').value = code;
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-pricing_type').value = pricingType;
    document.getElementById('edit-base_price').value = basePrice;
    document.getElementById('edit-surcharge_percent').value = surchargePercent;
    document.getElementById('edit-estimated_days').value = estimatedDays;
    document.getElementById('edit-is_active').value = isActive ? '1' : '0';
    document.getElementById('modal-edit').classList.remove('hidden');
}
</script>
@endsection
