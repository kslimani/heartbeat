<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Support\Status\Report\Latest\Report;
use App\Support\Locale;

class StatusHasChanged extends Notification
{
    use Queueable, Customizable;

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
        $this->customize($notifiable);

        $changes = $this->report->userChanges($notifiable)
            ->map(function ($change) {
                $fmtChange = clone $change;
                $fmtChange->date = Locale::humanDatetime(
                    $fmtChange->date,
                    Locale::TYPE_DATETIME_SHORT
                );

                return $fmtChange;
            });

        $statuses = $this->report->userStatuses($notifiable);

        return (new MailMessage)
            ->subject(sprintf(
                '[%s] %s',
                config('app.name', 'Heartbeat'),
                __('app.services_statuses')
            ))
            ->markdown('notifications.status-has-changed', [
                'changes' => $changes,
                'statuses' => $statuses,
                'date' => Locale::humanDatetime(),
            ]);
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
