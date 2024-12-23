<?php

namespace EventSubscriber;

use Event\LogEvent;
use Repository\LogRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LogSubscriber implements EventSubscriberInterface
{
  private LogRepository $rep;
  private LogEvent $logEvent;

  public function __construct(LogEvent $logEvent)
  {
    $this->rep = new LogRepository();
    $this->logEvent = $logEvent;
  }

  public function onLoginAction()
  {
    $log = $this->logEvent->getLog();
    $this->rep->add($log->getType(), $log->getMessage());
  }

  public static function getSubscribedEvents(): array
  {
    return [
      LogEvent::LOGIN   => 'onLoginAction',
    ];
  }
}