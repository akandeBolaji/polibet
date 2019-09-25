<?php

namespace App\Notifications;

use App\Bet;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OutcomeSubmitted extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $bet;

    public function __construct(Bet $bet)
    {
        $this->bet = $bet;
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
        $url = url('/api/bet/'.$this->bet->random);

        return (new MailMessage)
               ->subject('Outcome Submitted')
               ->greeting('Hello!')
               ->line('An outcome was just submitted for a bet you participated in')
               ->line('You can click on the below link to either accept or dispute this outcome!')
               ->action('Go now!', $url)
               ->line('You or other participants have 24 hours to make a dispute or the outcome would become accepted!!');
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
