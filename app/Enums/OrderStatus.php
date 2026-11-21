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
}
