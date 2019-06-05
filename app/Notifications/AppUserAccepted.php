<?php

namespace App\Notifications;

use App\Http\Helpers\LocalizationHelper;
use App\Models\Core\AppUser;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\App;

class AppUserAccepted extends Notification
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
        $title = '';
        $lang = LocalizationHelper::getSupportedItem('code', App::getLocale());

        foreach ( $this->app->locales AS $locale ) {
            if ( $title === '' || $locale->lang === $lang['iso'] ) {
                $title = $locale->title;
            }
        }

        return (new MailMessage)
            ->subject( __('Your request has been accepted'))
            ->greeting(__('Hello ') . $this->user->name . '!')
            ->line(__('Your request to use the application "' . $title . '" has been accepted.'))
            ->line(__('If you are not agree, you can contact us to solve the problem.'));
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
