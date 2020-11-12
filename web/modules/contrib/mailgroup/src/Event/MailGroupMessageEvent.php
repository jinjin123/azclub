<?php

namespace Drupal\mailgroup\Event;

use Symfony\Component\EventDispatcher\Event;
use Drupal\mailgroup\Entity\MailGroupMessage;

/**
 * Defines mail group message events.
 */
class MailGroupMessageEvent extends Event {

  const CREATE = 'mailgroup_message.create';

  /**
   * The message.
   *
   * @var \Drupal\mailgroup\Entity\MailGroupMessage
   */
  protected $message;

  /**
   * Constructs the object.
   *
   * @param \Drupal\mailgroup\Entity\MailGroupMessage $message
   *   The account of the user logged in.
   */
  public function __construct(MailGroupMessage $message) {
    $this->message = $message;
  }

  /**
   * Gets the message that triggered the event.
   *
   * @return \Drupal\mailgroup\Entity\MailGroupMessage
   *   The message that triggered the event.
   */
  public function getMessage() {
    return $this->message;
  }

}
