<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Shipment;
use App\Models\User;

class NewShipmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $shipment;
    protected $sender;

    /**
     * Create a new notification instance.
     */
    public function __construct(Shipment $shipment, User $sender = null)
    {
        $this->shipment = $shipment;
        $this->sender = $sender;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Shipment Created - ' . $this->shipment->shipment_number)
            ->view('emails.new-shipment', [
                'shipment' => $this->shipment,
                'user' => $notifiable
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        // Determine sender information
        $senderName = $this->sender ? $this->sender->name : 'System';
        $senderAvatar = $this->sender && $this->sender->profile_picture
            ? 'public/storage/' . $this->sender->profile_picture
            : 'public/storage/images/default_avatar.png';

        // Create personalized message
        $message = $this->sender
            ? $senderName . ' converted purchase to shipment ' . $this->shipment->shipment_number
            : 'New shipment ' . $this->shipment->shipment_number . ' has been created.';

        return [
            'shipment_id' => $this->shipment->id,
            'shipment_number' => $this->shipment->shipment_number,
            'customer_name' => $this->shipment->customer->user->name,
            'purchase_number' => $this->shipment->purchase->purchase_number,
            'message' => $message,
            'action_url' => route('shipments.show', $this->shipment),
            'sender_name' => $senderName,
            'sender_avatar' => $senderAvatar,
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast(object $notifiable): array
    {
        // Determine sender information
        $senderName = $this->sender ? $this->sender->name : 'System';
        $senderAvatar = $this->sender && $this->sender->profile_picture
            ? $this->sender->profile_picture
            : 'images/default_avatar.png';

        // Create personalized message
        $message = $this->sender
            ? $senderName . ' converted purchase to shipment ' . $this->shipment->shipment_number
            : 'New shipment ' . $this->shipment->shipment_number . ' has been created.';

        return [
            'id' => $this->id,
            'type' => 'App\Notifications\NewShipmentNotification',
            'shipment_id' => $this->shipment->id,
            'shipment_number' => $this->shipment->shipment_number,
            'customer_name' => $this->shipment->customer->user->name,
            'purchase_number' => $this->shipment->purchase->purchase_number,
            'message' => $message,
            'action_url' => route('shipments.show', $this->shipment),
            'sender_name' => $senderName,
            'sender_avatar' => $senderAvatar,
            'created_at' => now()->toISOString(),
        ];
    }
}
