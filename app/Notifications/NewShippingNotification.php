<?php

namespace App\Notifications;

use App\Models\Shipping;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewShippingNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(private Shipping $shipping)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'shipping_id' => $this->shipping->id,
            'invoice' => $this->shipping->invoice,
            'customer' => $this->shipping->customer ? $this->shipping->customer->name : 'Unknown',
            'amount' => $this->shipping->grand_total,
            'message' => 'New shipping transaction <a href="'.route('shippings.show', $this->shipping->id).'" class="text-blue-600 font-medium">'.$this->shipping->invoice.'</a> has been created.',
            'created_by' => auth()->user() ? auth()->user()->name : 'System',
            'type' => 'shipping'
        ];
    }
}