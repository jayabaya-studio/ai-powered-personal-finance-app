<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BudgetWarningNotification extends Notification
{
    use Queueable;

    protected $budgetName;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $budgetName)
    {
        $this->budgetName = $budgetName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // We only want to store this in the database for now
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => "Warning: You have used over 90% of your budget for '{$this->budgetName}'.",
            'link' => route('budgets.index'), // Optional: link to the budgets page
        ];
    }
}
