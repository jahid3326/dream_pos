<?php

namespace App\Notifications;

use App\Models\Purchase;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ModificationRequestApproved extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    public Purchase $purchase;
    public User $adminUser;

    /**
     * Create a new notification instance.
     */
    public function __construct(Purchase $purchase, User $adminUser)
    {
        $this->purchase = $purchase;
        $this->adminUser = $adminUser;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $orderUrl = route('orders.details', $this->purchase);

        return (new MailMessage)
            ->subject("Update on Purchase Order: #{$this->purchase->purchase_number}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your modification proposal for Purchase Order #{$this->purchase->purchase_number} has been reviewed and approved by {$this->adminUser->name}.")
            ->line("The order will now proceed to production with the updated details.")
            ->action('View Updated Order', $orderUrl)
            ->line('Thank you.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'purchase_id' => $this->purchase->id,
            'purchase_number' => $this->purchase->purchase_number,
            'sender_id' => $this->adminUser->id,
            'sender_name' => $this->adminUser->name,
            'sender_avatar' => $this->adminUser->profile_picture,
            'message' => "approved your modification for PO #{$this->purchase->purchase_number}",
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        // This uses the same data as the database notification
        return new BroadcastMessage($this->toDatabase($notifiable));
    }
}
