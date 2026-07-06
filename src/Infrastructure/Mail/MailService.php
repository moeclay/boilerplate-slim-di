<?php
declare(strict_types=1);

namespace App\Infrastructure\Mail;

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

final class MailService
{
    public function __construct(
        private Mailer $mailer,
        private array $settings
    ) {
    }

    public function sendVerificationEmail(string $to, string $name, string $link): void
    {
        $email = (new Email())
            ->from(sprintf('%s <%s>', $this->settings['from_name'], $this->settings['from_address']))
            ->to($to)
            ->subject('Verify your email')
            ->html(sprintf(
                '<p>Hi %s,</p><p>Click this link to verify your account:</p><p><a href="%s">%s</a></p>',
                htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($link, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($link, ENT_QUOTES, 'UTF-8')
            ));

        $this->mailer->send($email);
    }
}