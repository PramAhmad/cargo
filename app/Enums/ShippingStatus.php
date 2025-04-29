<?php

namespace App\Enums;

enum ShippingStatus: string 
{
    case waiting = 'waiting';
    case rejected = 'rejected';
    case sendAgentWarehouse = 'send_agent_warehouse';
    case sendIndonesia = 'send_indonesia';
    case arrivedIndonesia = 'arrived_indonesia';
    case sendSidoarjo = 'send_sidoarjo';
    case arrivedSidoarjo = 'arrived_sidoarjo';
    case sendAddress = 'send_address';
    case arrivedAddress = 'arrived_address';
    case done = 'done';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::waiting => 'Menunggu Proses',
            self::rejected => 'Ditolak',
            self::sendAgentWarehouse => 'Dikirim ke Gudang Agent',
            self::sendIndonesia => 'Dikirim ke Indonesia',
            self::arrivedIndonesia => 'Sampai di Indonesia',
            self::sendSidoarjo => 'Dikirim ke Sidoarjo',
            self::arrivedSidoarjo => 'Sampai di Sidoarjo',
            self::sendAddress => 'Dikirim ke Alamat Pengguna',
            self::arrivedAddress => 'Diterima di Alamat Pengguna',
            self::done => 'Selesai',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::waiting => 'gray',
            self::rejected => 'danger',
            self::sendAgentWarehouse => 'dark',
            self::sendIndonesia => 'warning',
            self::arrivedIndonesia => 'secondary',
            self::sendSidoarjo => 'info',
            self::arrivedSidoarjo => 'success',
            self::sendAddress => 'info',
            self::arrivedAddress => 'success',
            self::done => 'success',
        };
    }
}