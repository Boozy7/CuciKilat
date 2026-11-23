<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Unpaid = 'unpaid';
    case Pending = 'pending';
    case Paid = 'paid';

    public function label(): string
    {
        return match ($this) {
            self::Unpaid => 'Belum dibayar',
            self::Pending => 'Menunggu konfirmasi',
            self::Paid => 'Sudah dibayar',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Unpaid => 'bg-[#F7E3DE] text-[#8F2D1E] border border-[#E3BDB4]',
            self::Pending => 'bg-[#F7E8B5] text-[#6B4C0B] border border-[#E5CE82]',
            self::Paid => 'bg-[#DCEAF4] text-[#1F4A68] border border-[#9EC5E0]',
        };
    }
}
