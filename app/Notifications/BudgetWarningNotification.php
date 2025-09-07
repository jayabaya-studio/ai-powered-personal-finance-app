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

    public function __construct(string $budgetName)
    {
        $this->budgetName = $budgetName;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "Warning: You have used over 90% of your budget for '{$this->budgetName}'.",
            'link' => route('budgets.index'), // Optional: link to the budgets page
        ];
    }
}
