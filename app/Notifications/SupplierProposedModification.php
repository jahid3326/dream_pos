<?php

namespace App\Notifications;

use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupplierProposedModification extends Notification implements ShouldQueue, ShouldBroadcast
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
        return ['database', 'mail', 'broadcast'];
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
        return [
            //
        ];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'purchase_id' => $this->purchase->id,
            'purchase_number' => $this->purchase->purchase_number,
            'sender_id' => $this->supplier->user->id,
            'sender_name' => $this->supplier->company_name,
            'sender_avatar' => $this->supplier->user->profile_image_url,
            'message' => "proposed a modification for PO #{$this->purchase->purchase_number}",
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }
}
