<?php

namespace App\Traits;

use App\Models\ShippingLog;
use Illuminate\Support\Facades\Auth;

trait ShippingLogTrait 
{
    /**
     * Log shipping activity
     *
     * @param int $shippingId
     * @param string $status
     * @param string $notes
     * @return ShippingLog
     */
    public function logShippingActivity($shippingId, $status, $notes = '')
    {
        $log = ShippingLog::create([
            'shipping_id' => $shippingId,
            'user_id' => Auth::id() ?? 1,
            'status' => $status,
            'notes' => $notes,
        ]);
        
        return $log;
    }
}