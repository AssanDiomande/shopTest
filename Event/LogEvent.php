<?php

namespace Event;

use Model\Log;
use Symfony\Contracts\EventDispatcher\Event;

class LogEvent extends Event
{
  public const LOGIN = 'log.login';

  public Log $log;

  public function __construct(Log $log)
  {
    $this->log = $log;
  }

  public function getLog()
  {
    return $this->log;
  }
}