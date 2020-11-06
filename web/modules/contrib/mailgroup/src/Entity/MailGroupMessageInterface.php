<?php

namespace Drupal\mailgroup\Entity;

use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface for defining mail group messages.
 */
interface MailGroupMessageInterface extends MailGroupEntityInterface, EntityChangedInterface {

  /**
   * Gets the message subject.
   *
   * @return string
   *   Subject of the message.
   */
  public function getSubject();

  /**
   * Sets the message subject.
   *
   * @param string $subject
   *   The message subject.
   *
   * @return \Drupal\mailgroup\Entity\MailGroupmessageInterface
   *   The called message entity.
   */
  public function setSubject($subject);

  /**
   * Gets the message sender.
   *
   * @return string
   *   Sender of the message.
   */
  public function getSender();

  /**
   * Sets the message sender.
   *
   * @param string $sender
   *   The message sender.
   *
   * @return \Drupal\mailgroup\Entity\MailGroupmessageInterface
   *   The called message entity.
   */
  public function setSender($sender);

  /**
   * Gets the message body.
   *
   * @return string
   *   Body of the message.
   */
  public function getBody();

  /**
   * Sets the message Body.
   *
   * @param string $body
   *   The message body.
   *
   * @return \Drupal\mailgroup\Entity\MailGroupmessageInterface
   *   The called message entity.
   */
  public function setBody($body);

  /**
   * Gets the message creation timestamp.
   *
   * @return int
   *   Creation timestamp of the message.
   */
  public function getCreatedTime();

  /**
   * Sets the message creation timestamp.
   *
   * @param int $timestamp
   *   The message creation timestamp.
   *
   * @return \Drupal\mailgroup\Entity\MailGroupmessageInterface
   *   The called message entity.
   */
  public function setCreatedTime($timestamp);

}
