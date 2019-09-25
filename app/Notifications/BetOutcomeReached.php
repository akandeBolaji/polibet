<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class BetOutcomeReached extends Notification
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
        $bet_id = $this->bet->random;

        return (new MailMessage)
               ->subject("Outcome Required for $bet_id")
               ->greeting('Hello!')
               ->line("An outcome is required for your bet $bet_id")
               ->line('You can click on the below link to give the outcome')
               ->action('Go now!', $url)
               ->line('You have 24 hours to do so!!');
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
