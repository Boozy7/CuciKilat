@extends('layouts.app')

@section('title', 'Kelola Layanan')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6 pb-4 border-b border-[#4A2018]/15">
    <div>
        <p class="font-mono text-xs uppercase tracking-widest text-[#7A4E45] mb-1">Master Tarif</p>
        <h1 class="font-display text-3xl text-[#4A2018]">Kelola Layanan</h1>
    </div>
    <button type="button"
            onclick="document.getElementById('modal-create').classList.remove('hidden')"
            class="cursor-pointer bg-[#4A2018] hover:bg-[#33140E] text-[#FAF8F4] px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors min-h-[44px]">
        + Tambah Layanan
    </button>
</div>

{{-- Daftar Layanan --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse($services as $service)
        <div class="bg-white rounded-lg border border-[#4A2018]/20 p-6 flex flex-col justify-between {{ !$service->is_active ? 'opacity-65' : '' }}">
            <div>
                <div class="flex items-start justify-between gap-2 mb-3">
                    <div>
                        <h3 class="font-display text-xl text-[#4A2018]">{{ $service->name }}</h3>
                        <p class="text-xs font-mono text-[#7A4E45] mt-0.5">{{ $service->code }}</p>
                    </div>
                    @if($service->is_active)
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#DCEAF4] text-[#1F4A68] border border-[#9EC5E0]">Aktif</span>
                    @else
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#F7E3DE] text-[#8F2D1E] border border-[#E3BDB4]">Nonaktif</span>
                    @endif
                </div>

                <p class="text-xs font-mono uppercase tracking-wider text-[#7A4E45] mb-3">{{ $service->pricing_type->label() }}</p>

                <p class="font-mono text-2xl font-semibold text-[#4A2018]">
                    Rp {{ number_format($service->base_price, 0, ',', '.') }}
                    <span class="text-xs font-normal text-[#7A4E45]">/{{ $service->isPerKg() ? 'kg' : 'item' }}</span>
                </p>

                @if($service->surcharge_percent > 0)
                    <p class="inline-flex mt-2 px-2 py-0.5 rounded bg-[#F7E8B5] text-[#6B4C0B] text-xs font-semibold">+{{ $service->surcharge_percent }}% surcharge</p>
                @endif
                <p class="text-xs text-[#7A4E45] mt-2">Estimasi {{ $service->estimated_days }} hari</p>
            </div>

            <div class="flex gap-2 mt-5 pt-4 border-t border-[#4A2018]/10">
                <button type="button"
                        onclick="openEditModal({{ $service->id }}, {{ \Illuminate\Support\Js::from($service->code) }}, {{ \Illuminate\Support\Js::from($service->name) }}, {{ \Illuminate\Support\Js::from($service->pricing_type->value) }}, {{ $service->base_price }}, {{ $service->surcharge_percent }}, {{ $service->estimated_days }}, {{ $service->is_active ? 'true' : 'false' }})"
                        class="flex-1 cursor-pointer bg-[#F7E8B5] hover:bg-[#EFE0A8] text-[#4A2018] py-2 rounded-lg text-xs font-semibold transition-colors min-h-[38px]">
                    Edit
                </button>
                <form method="POST" action="{{ route('staff.services.destroy', $service) }}"
                      onsubmit="return confirm('Yakin ingin menghapus atau menonaktifkan layanan ini?')" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full cursor-pointer bg-[#F7E3DE] hover:bg-[#EFD1C9] text-[#8F2D1E] py-2 rounded-lg text-xs font-semibold transition-colors min-h-[38px]">
                        Hapus / Nonaktif
                    </button>
                </form>
            </div>
        </div>
    @empty
        <p class="col-span-3 text-center text-[#7A4E45] py-10">Belum ada layanan.</p>
    @endforelse
</div>

{{-- Modal Tambah Layanan --}}
<div id="modal-create" class="hidden fixed inset-0 bg-[#33140E]/50 backdrop-blur-[1px] z-50 flex items-center justify-center p-4">
    <div class="bg-[#FAF8F4] border border-[#4A2018]/25 rounded-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4 pb-3 border-b border-[#4A2018]/15">
            <h2 class="font-display text-xl text-[#4A2018]">Tambah Layanan Baru</h2>
            <button type="button" onclick="document.getElementById('modal-create').classList.add('hidden')" class="cursor-pointer text-[#7A4E45] hover:text-[#4A2018] px-2 py-1 text-lg" aria-label="Tutup modal">&times;</button>
        </div>
        <form method="POST" action="{{ route('staff.services.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-[#4A2018] mb-1">Kode <span class="text-[#8F2D1E]">*</span></label>
                <input type="text" name="code" required maxlength="32" placeholder="Contoh: PREMIUM" class="w-full bg-white border border-[#4A2018]/25 rounded-lg px-3 py-2 text-sm font-mono uppercase text-[#4A2018]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-[#4A2018] mb-1">Nama Layanan <span class="text-[#8F2D1E]">*</span></label>
                <input type="text" name="name" required maxlength="100" placeholder="Contoh: Premium Wash" class="w-full bg-white border border-[#4A2018]/25 rounded-lg px-3 py-2 text-sm text-[#4A2018]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-[#4A2018] mb-1">Tipe Harga <span class="text-[#8F2D1E]">*</span></label>
                <select name="pricing_type" required class="w-full bg-white border border-[#4A2018]/25 rounded-lg px-3 py-2 text-sm text-[#4A2018]">
                    @foreach(\App\Enums\PricingType::cases() as $pt)
                        <option value="{{ $pt->value }}">{{ $pt->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-[#4A2018] mb-1">Harga Dasar (Rp) <span class="text-[#8F2D1E]">*</span></label>
                    <input type="number" name="base_price" required min="1" placeholder="7000" class="w-full bg-white border border-[#4A2018]/25 rounded-lg px-3 py-2 text-sm font-mono text-[#4A2018]">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#4A2018] mb-1">Surcharge (%)</label>
                    <input type="number" name="surcharge_percent" min="0" max="100" value="0" class="w-full bg-white border border-[#4A2018]/25 rounded-lg px-3 py-2 text-sm font-mono text-[#4A2018]">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-[#4A2018] mb-1">Estimasi Hari <span class="text-[#8F2D1E]">*</span></label>
                    <input type="number" name="estimated_days" required min="1" max="30" placeholder="3" class="w-full bg-white border border-[#4A2018]/25 rounded-lg px-3 py-2 text-sm font-mono text-[#4A2018]">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#4A2018] mb-1">Status</label>
                    <select name="is_active" class="w-full bg-white border border-[#4A2018]/25 rounded-lg px-3 py-2 text-sm text-[#4A2018]">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="w-full cursor-pointer bg-[#4A2018] hover:bg-[#33140E] text-[#FAF8F4] py-2.5 rounded-lg text-sm font-semibold transition-colors min-h-[44px]">
                Simpan Layanan
            </button>
        </form>
    </div>
</div>

{{-- Modal Edit Layanan --}}
<div id="modal-edit" class="hidden fixed inset-0 bg-[#33140E]/50 backdrop-blur-[1px] z-50 flex items-center justify-center p-4">
    <div class="bg-[#FAF8F4] border border-[#4A2018]/25 rounded-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4 pb-3 border-b border-[#4A2018]/15">
            <h2 class="font-display text-xl text-[#4A2018]">Edit Layanan</h2>
            <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')" class="cursor-pointer text-[#7A4E45] hover:text-[#4A2018] px-2 py-1 text-lg" aria-label="Tutup modal">&times;</button>
        </div>
        <form method="POST" id="form-edit" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-semibold text-[#4A2018] mb-1">Kode</label>
                <input type="text" name="code" id="edit-code" required maxlength="32" class="w-full bg-white border border-[#4A2018]/25 rounded-lg px-3 py-2 text-sm font-mono uppercase text-[#4A2018]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-[#4A2018] mb-1">Nama Layanan</label>
                <input type="text" name="name" id="edit-name" required maxlength="100" class="w-full bg-white border border-[#4A2018]/25 rounded-lg px-3 py-2 text-sm text-[#4A2018]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-[#4A2018] mb-1">Tipe Harga</label>
                <select name="pricing_type" id="edit-pricing_type" required class="w-full bg-white border border-[#4A2018]/25 rounded-lg px-3 py-2 text-sm text-[#4A2018]">
                    @foreach(\App\Enums\PricingType::cases() as $pt)
                        <option value="{{ $pt->value }}">{{ $pt->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-[#4A2018] mb-1">Harga Dasar (Rp)</label>
                    <input type="number" name="base_price" id="edit-base_price" required min="1" class="w-full bg-white border border-[#4A2018]/25 rounded-lg px-3 py-2 text-sm font-mono text-[#4A2018]">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#4A2018] mb-1">Surcharge (%)</label>
                    <input type="number" name="surcharge_percent" id="edit-surcharge_percent" min="0" max="100" class="w-full bg-white border border-[#4A2018]/25 rounded-lg px-3 py-2 text-sm font-mono text-[#4A2018]">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-[#4A2018] mb-1">Estimasi Hari</label>
                    <input type="number" name="estimated_days" id="edit-estimated_days" required min="1" max="30" class="w-full bg-white border border-[#4A2018]/25 rounded-lg px-3 py-2 text-sm font-mono text-[#4A2018]">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#4A2018] mb-1">Status</label>
                    <select name="is_active" id="edit-is_active" class="w-full bg-white border border-[#4A2018]/25 rounded-lg px-3 py-2 text-sm text-[#4A2018]">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="w-full cursor-pointer bg-[#4A2018] hover:bg-[#33140E] text-[#FAF8F4] py-2.5 rounded-lg text-sm font-semibold transition-colors min-h-[44px]">
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
