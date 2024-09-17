<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class SlackNotificacao extends Notification
{
    use Queueable;

    protected $mensagem;

    public function __construct($mensagem)
    {
        $this->mensagem = $mensagem;
    }

    public function via($notificavel)
    {
        return ['slack'];
    }

    public function toSlack($notificavel)
    {
        return (new SlackMessage)
            ->content($this->mensagem);
    }
}
