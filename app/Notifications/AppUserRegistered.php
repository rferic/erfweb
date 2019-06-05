<?php

namespace App\Notifications;

use App\Models\Core\AppUser;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AppUserRegistered extends Notification
{
    use Queueable;

    public $user, $app;

    /**
     * Create a new notification instance.
     *
     * @param AppUser $item
     */
    public function __construct( AppUser $item )
    {
        $this->user = $item->user;
        $this->app = $item->app;
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
        if ( $this->item->app->type === 'private' ) {
            return (new MailMessage)
                ->subject( __('Request to use a private app'))
                ->greeting($this->user->name . ' ' . __('has requested to use the private application') . ' ' . $this->app->id)
                ->line('Review the request and accept it if you consider it appropriate.')
                ->line('Thank you for using our application!');
        } else {
            return (new MailMessage)
                ->subject( __('Request to use a protected app'))
                ->greeting($this->user->name . ' ' . __('has requested to use the protected application') . ' ' . $this->app->id)
                ->line('Thank you for using our application!');
        }
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
