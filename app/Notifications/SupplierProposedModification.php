<?php

namespace App\Notifications;

use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupplierProposedModification extends Notification implements ShouldQueue
{
    use Queueable;

    public Purchase $purchase;
    public Supplier $supplier;

    /**
     * Create a new notification instance.
     */
    public function __construct(Purchase $purchase, Supplier $supplier)
    {
        $this->purchase = $purchase;
        $this->supplier = $supplier;
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
        // The $notifiable here is the Admin's User model
        $adminPurchaseUrl = route('purchases.show', $this->purchase);

        return (new MailMessage)
            ->subject("Action Required: Modification Proposal for PO #{$this->purchase->purchase_number}")
            ->greeting("Hello {$notifiable->name},")
            ->line("The supplier, {$this->supplier->user->name}, has submitted a modification proposal for Purchase Order #{$this->purchase->purchase_number}.")
            ->line("Please review the proposed changes and take action (validate or reject).")
            ->action('Review Proposal', $adminPurchaseUrl);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        // Determine sender information
        $senderName = $this->supplier->company_name ?? $this->supplier->user->name ?? 'Supplier';
        $senderAvatar = $this->supplier->user && $this->supplier->user->profile_picture
            ? 'public/storage/' . $this->supplier->user->profile_picture
            : 'public/storage/images/default_avatar.png';

        // Create personalized message
        $message = $senderName . ' proposed a modification for PO #' . $this->purchase->purchase_number;

        return [
            'purchase_id' => $this->purchase->id,
            'purchase_number' => $this->purchase->purchase_number,
            'supplier_id' => $this->supplier->id,
            'supplier_name' => $this->supplier->company_name,
            'message' => $message,
            'action_url' => route('purchases.show', $this->purchase),
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
        $senderName = $this->supplier->company_name ?? $this->supplier->user->name ?? 'Supplier';
        $senderAvatar = $this->supplier->user && $this->supplier->user->profile_picture
            ? $this->supplier->user->profile_picture
            : 'images/default_avatar.png';

        // Create personalized message
        $message = $senderName . ' proposed a modification for PO #' . $this->purchase->purchase_number;

        return [
            'id' => $this->id,
            'type' => 'App\Notifications\SupplierProposedModification',
            'purchase_id' => $this->purchase->id,
            'purchase_number' => $this->purchase->purchase_number,
            'supplier_id' => $this->supplier->id,
            'supplier_name' => $this->supplier->company_name,
            'message' => $message,
            'action_url' => route('purchases.show', $this->purchase),
            'sender_name' => $senderName,
            'sender_avatar' => $senderAvatar,
            'created_at' => now()->toISOString(),
        ];
    }
}
