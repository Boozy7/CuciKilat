<?php

namespace App\Enums;

enum OrderStatus: string
{
    case WaitingPickup = 'waiting_pickup';
    case PickedUp = 'picked_up';
    case Processing = 'processing';
    case ReadyForDelivery = 'ready_for_delivery';
    case Delivered = 'delivered';

    public function label(): string
    {
        return match ($this) {
            self::WaitingPickup => 'Menunggu dijemput',
            self::PickedUp => 'Sudah dijemput',
            self::Processing => 'Sedang diproses',
            self::ReadyForDelivery => 'Siap diantar',
            self::Delivered => 'Sudah diserahkan',
        };
    }

    public function next(): ?self
    {
        return match ($this) {
            self::WaitingPickup => self::PickedUp,
            self::PickedUp => self::Processing,
            self::Processing => self::ReadyForDelivery,
            self::ReadyForDelivery => self::Delivered,
            self::Delivered => null,
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return $this->next() === $target;
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::WaitingPickup => 'bg-[#F7E8B5] text-[#6B4C0B] border border-[#E5CE82]',
            self::PickedUp => 'bg-[#DCEAF4] text-[#1F4A68] border border-[#B5D3E8]',
            self::Processing => 'bg-[#C8DFF0] text-[#183C56] border border-[#96BEDA]',
            self::ReadyForDelivery => 'bg-[#EFE0A8] text-[#4A2018] border border-[#D4BC6A]',
            self::Delivered => 'bg-[#4A2018] text-[#FAF8F4] border border-[#4A2018]',
        };
    }
}
