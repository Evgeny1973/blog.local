<?php

namespace App\EventSubscriber;

use App\Event\RegisteredUserEvent;
use App\Service\Mailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber implements EventSubscriberInterface
{
    /**
     * @var Mailer
     */
    protected $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [RegisteredUserEvent::NAME => 'onUserRegister'];
    }

    /**
     * @param RegisteredUserEvent $registeredUserEvent
     */
    public function onUserRegister(RegisteredUserEvent $registeredUserEvent)
    {
        $this->mailer->sendConfirmationMessage($registeredUserEvent->getRegisteredUser());
    }
}
