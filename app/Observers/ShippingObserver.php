<?php

namespace App\Observers;

use App\Models\Shipping;
use App\Models\User;
use App\Notifications\NewShippingNotification;
use Illuminate\Support\Facades\Notification;

class ShippingObserver
{
    /**
     * Handle the Shipping "created" event.
     */
    public function created(Shipping $shipping): void
    {
        // Kirim notifikasi ke admin dan marketing
        $recipients = User::with('roles')
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', ['superadmin', 'admin', 'marketing']);
            })
            ->get();

        // Tambahkan marketing terkait jika ada
        if ($shipping->marketing_id) {
            $marketing = User::find($shipping->marketing_id);
            if ($marketing && !$recipients->contains('id', $marketing->id)) {
                $recipients->push($marketing);
            }
        }

        // Kirim notifikasi
        Notification::send($recipients, new NewShippingNotification($shipping));
    }

    /**
     * Handle the Shipping "updated" event.
     */
    public function updated(Shipping $shipping): void
    {
        // Optional: tambahkan notifikasi untuk update status atau perubahan penting
    }

    /**
     * Handle the Shipping "deleted" event.
     */
    public function deleted(Shipping $shipping): void
    {
        //
    }

    /**
     * Handle the Shipping "restored" event.
     */
    public function restored(Shipping $shipping): void
    {
        //
    }

    /**
     * Handle the Shipping "force deleted" event.
     */
    public function forceDeleted(Shipping $shipping): void
    {
        //
    }
}