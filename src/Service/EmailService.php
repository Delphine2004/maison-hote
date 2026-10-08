<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Booking;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;


final class EmailService
{
    public function __construct(
        private MailerInterface $mailer,
    ) {}

    private function sendTemplate(
        string $to,
        string $subject,
        string $template,
        array $context = [],
    ): void {
        $email = (new TemplatedEmail())
            ->from('no-reply@guesthouse.com')
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context);

        $this->mailer->send($email);
    }

    public function sendRegistrationConfirmation(
        User $user
    ): void {
        $this->sendTemplate(
            to: $user->getEmail(),
            subject: 'Bienvenue !',
            template: 'registration/email.html.twig',
            context: [
                'user' => $user,
            ],
        );
    }

    public function sendBookingConfirmation(
        User $user,
        Booking $booking
    ): void {
        $this->sendTemplate(
            to: $user->getEmail(),
            subject: 'Confirmation réservation',
            template: 'booking/email_confirmation.html.twig',
            context: [
                'user' => $user,
                'booking' => $booking
            ],
        );
    }

    public function sendBookingCancellation(
        User $user,
        Booking $booking
    ): void {
        $this->sendTemplate(
            to: $user->getEmail(),
            subject: 'Annulation réservation',
            template: 'booking/email_cancellation.html.twig',
            context: [
                'user' => $user,
                'booking' => $booking
            ],
        );
    }
}
