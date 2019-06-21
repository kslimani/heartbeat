<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Support\Status\Report\Latest\Report;

class StatusHasChanged extends Notification
{
    use Queueable;

    public $report;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Support\Status\Report\Latest\Report  $report
     * @return void
     */
    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // TODO: blade template with formatted message, button link to user status page, etc...
        // return (new MailMessage)->view('emails.status-has-changed', [
        //     'changes' => $this->report->userChanges($notifiable),
        // ]);

        $message = (new MailMessage)
            ->subject('[Heartbeat] service status has changed');

        $this->report
            ->userChanges($notifiable)
            ->each(function($change) use ($message) {
                $message->line(sprintf(
                    'Device "%s" service "%s" status has changed from "%s" to "%s"',
                    $change->device->name,
                    $change->service->name,
                    $change->from->name,
                    $change->to->name
                ));
            });

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
