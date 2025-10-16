<?php

namespace App\Notifications;

use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupplierConfirmedOrder extends Notification implements ShouldQueue
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
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // The $notifiable here will be the Super Admin's User model
        $purchaseUrl = route('purchases.show', $this->purchase);

        return (new MailMessage)
            ->subject("Supplier Confirmation for PO #{$this->purchase->purchase_number}")
            ->greeting("Hello {$notifiable->name},")
            ->line("The supplier, **{$this->supplier->user->name}**, has confirmed their part of Purchase Order #{$this->purchase->purchase_number}.")
            ->line("The order is now moving into the production phase for this supplier.")
            ->action('View Purchase Order', $purchaseUrl);
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
            'sender_name' => $this->supplier->user->name,
            'sender_avatar' => $this->supplier->user->profile_image_url,
            'message' => "has confirmed their part of PO #{$this->purchase->purchase_number}",
        ];
    }
}
