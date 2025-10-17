<?php

namespace App\Notifications;

use App\Models\Purchase;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentReminder extends Notification implements ShouldQueue
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
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Link to the document upload page for this order
        $uploadUrl = route('documents.showUploadForm', $this->purchase);

        return (new MailMessage)
            ->subject("Reminder: Required Documents for PO #{$this->purchase->purchase_number}")
            ->greeting("Hello {$notifiable->name},")
            ->line("This is a friendly reminder to please upload the required documents for Purchase Order #{$this->purchase->purchase_number}.")
            ->line("The following documents are still marked as missing:")
            // We can add a list of missing docs here if needed
            ->action('Upload Documents Now', $uploadUrl)
            ->line('Thank you for your prompt attention to this matter.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'purchase_id' => $this->purchase->id,
            'purchase_number' => $this->purchase->purchase_number,
            'sender_id' => $this->adminUser->id,
            'sender_name' => $this->adminUser->name,
            'sender_avatar' => $this->adminUser->profile_image_url,
            'message' => "sent you a reminder for missing documents on PO #{$this->purchase->purchase_number}",
        ];
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
}
