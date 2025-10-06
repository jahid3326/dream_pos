<?php

namespace App\Notifications;

use App\Models\Purchase;
use App\Models\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class PurchaseOrderCreated extends Notification implements ShouldBroadcast
{
    use Queueable;

    public Purchase $purchase;
    public User $sender;
    public Collection $items; // Items for this specific supplier
    public float $supplierTotalAmount; // Total amount for this specific supplier

    /**
     * Create a new notification instance.
     */
    public function __construct(Purchase $purchase, User $sender, Collection $items, float $supplierTotalAmount)
    {
        $this->purchase = $purchase;
        $this->sender = $sender;
        $this->items = $items;
        $this->supplierTotalAmount = $supplierTotalAmount;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $purchaseUrl = route('orders.show', $this->purchase);

        return (new MailMessage)
            ->subject("New Purchase Order: #{$this->purchase->purchase_number}")
            ->greeting("Hello {$notifiable->name},")
            ->line("A new purchase order has been created for you by {$this->sender->name}.")
            ->line("Purchase Order Number: **{$this->purchase->purchase_number}**")
            // --- THIS IS THE UPDATED LINE ---
            ->line("Your Total Amount: **$" . number_format($this->supplierTotalAmount, 2) . "**")
            ->action('View Your Order Details', $purchaseUrl)
            ->line('Thank you for your cooperation!');
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

    /**
     * Get the array representation of the notification for the database.
     * This is the data that gets stored in the `data` column.
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'purchase_id' => $this->purchase->id,
            'purchase_number' => $this->purchase->purchase_number,
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name,
            'sender_avatar' => $this->sender->profile_picture,
            'message' => "created a new Purchase Order: #{$this->purchase->purchase_number}",
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     * This is the data packet sent to the frontend via Pusher.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'purchase_id' => $this->purchase->id,
            'purchase_number' => $this->purchase->purchase_number,
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name,
            'sender_avatar' => $this->sender->profile_picture,
            'message' => "created a new Purchase Order: #{$this->purchase->purchase_number}",
        ]);
    }
}
