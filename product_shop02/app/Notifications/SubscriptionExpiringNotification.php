<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpiringNotification extends Notification
{
    use Queueable;

    public function __construct(public Subscription $subscription) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('mail.subscription_expiring_subject'))
            ->greeting(__('mail.greeting', ['name' => $notifiable->name]))
            ->line(__('mail.subscription_expiring_line', [
                'plan' => $this->subscription->plan_name,
                'date' => $this->subscription->ends_at?->format('Y-m-d'),
            ]))
            ->action(__('billing.renew'), route('billing.index'))
            ->line(__('mail.thanks'));
    }
}
