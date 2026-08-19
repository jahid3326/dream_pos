<?php

namespace App\Notifications;

use App\Models\Purchase;
use App\Models\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class PurchaseOrderCreated extends Notification implements ShouldQueue
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
        return ['mail', 'database', 'broadcast'];
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
        // Determine sender information
        $senderName = $this->sender ? $this->sender->name : 'System';
        $senderAvatar = $this->sender && $this->sender->profile_picture
            ? 'storage/' . $this->sender->profile_picture
            : 'storage/images/default_avatar.png';

        // Create personalized message
        $message = $this->sender
            ? $senderName . ' created a new Purchase Order: #' . $this->purchase->purchase_number
            : 'New purchase order #' . $this->purchase->purchase_number . ' has been created.';

        return [
            'purchase_id' => $this->purchase->id,
            'purchase_number' => $this->purchase->purchase_number,
            'supplier_total_amount' => $this->supplierTotalAmount,
            'message' => $message,
            'action_url' => route('orders.show', $this->purchase),
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
            ? $senderName . ' created a new Purchase Order: #' . $this->purchase->purchase_number
            : 'New purchase order #' . $this->purchase->purchase_number . ' has been created.';

        return [
            'id' => $this->id,
            'type' => 'App\Notifications\PurchaseOrderCreated',
            'purchase_id' => $this->purchase->id,
            'purchase_number' => $this->purchase->purchase_number,
            'supplier_total_amount' => $this->supplierTotalAmount,
            'message' => $message,
            'action_url' => route('orders.show', $this->purchase),
            'sender_name' => $senderName,
            'sender_avatar' => $senderAvatar,
            'created_at' => now()->toISOString(),
        ];
    }
}
