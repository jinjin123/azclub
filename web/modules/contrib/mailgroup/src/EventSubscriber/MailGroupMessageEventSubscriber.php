<?php

namespace Drupal\mailgroup\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\mailgroup\MailHandler;
use Drupal\mailgroup\Event\MailGroupMessageEvent;

/**
 * Subscribe to mail group message events.
 */
class MailGroupMessageEventSubscriber implements EventSubscriberInterface {

  /**
   * The mail handler service.
   *
   * @var \Drupal\mailgroup\MailHandler
   */
  protected $handler;

  /**
   * Constructs the service.
   *
   * @param \Drupal\mailgroup\MailHandler $mail_handler
   *   The mail handler service.
   */
  public function __construct(MailHandler $mail_handler) {
    $this->handler = $mail_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      MailGroupMessageEvent::CREATE => 'sendMessage',
    ];
  }

  /**
   * Send a message to group members.
   *
   * @param \Drupal\mailgroup\Event\MailGroupMessageEvent $event
   *   The message event.
   */
  public function sendMessage(MailGroupMessageEvent $event) {
    /** @var \Drupal\mailgroup\Entity\MailGroupMessage $message */
    $message = $event->getMessage();

    $this->handler->sendMessage($message);
  }

}
