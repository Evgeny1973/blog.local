<?php

namespace App\Service;

use App\Entity\User;
use Twig\Environment;

class Mailer
{

    public const FROM_ADDRESS = 'oleg_34@list.ru';

    /**
     * @var \Swift_Mailer
     */
    protected $mailer;
    /**
     * @var Environment
     */
    protected $twig;

    public function __construct(\Swift_Mailer $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendConfirmationMessage(User $user)
    {
        $messageBody = $this->twig->render('security/confirmation.html.twig', ['user' => $user]);

        $message = new \Swift_Message();
        $message->setSubject('Вы успешно прошли регистрацию.')
            ->setFrom(self::FROM_ADDRESS)
            ->setTo($user->getEmail())
            ->setBody($messageBody, 'text/html');

        $this->mailer->send($message);
    }
}
