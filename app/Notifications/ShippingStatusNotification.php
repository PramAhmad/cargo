<?php

namespace App\Notifications;

use App\Models\Shipping;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ShippingStatusNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private Shipping $shipping, 
        private string $oldStatus, 
        private string $newStatus
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'shipping_id' => $this->shipping->id,
            'invoice' => $this->shipping->invoice,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => 'Shipping <a href="'.route('shippings.show', $this->shipping->id).'" class="text-blue-600 font-medium">'.$this->shipping->invoice.'</a> status changed from '.$this->oldStatus.' to '.$this->newStatus,
            'updated_by' => auth()->user() ? auth()->user()->name : 'System',
            'type' => 'shipping_status'
        ];
    }
}