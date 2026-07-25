<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPaidNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('mail.order_paid_subject', ['number' => $this->order->number]))
            ->greeting(__('mail.greeting', ['name' => $this->order->customer_name]))
            ->line(__('mail.order_paid_line', [
                'number' => $this->order->number,
                'total' => $this->order->formattedTotal(),
            ]))
            ->action(__('mail.view_order'), route('orders.show', $this->order))
            ->line(__('mail.thanks'));
    }
}
