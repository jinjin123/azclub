<?php

namespace Drupal\mailgroup\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining mail groups.
 */
interface MailGroupInterface extends ContentEntityInterface, EntityOwnerInterface {

  /**
   * Load a group by email address.
   *
   * @param string $email
   *   The group email address.
   *
   * @return \Drupal\mailgroup\Entity\MailGroup|false
   *   The loaded group, or FALSE if a group with the specified email address
   *   isn't found.
   */
  public static function loadByEmail($email);

  /**
   * Gets the group name.
   *
   * @return string
   *   The group name.
   */
  public function getName();

  /**
   * Sets the group name.
   *
   * @param string $name
   *   The group name.
   *
   * @return \Drupal\mailgroup\Entity\MailGroupInterface
   *   The called group entity.
   */
  public function setName($name);

  /**
   * Gets the group email address.
   *
   * @return string
   *   The group email address.
   */
  public function getEmail();

  /**
   * Sets the group email address.
   *
   * @param string $email
   *   The group email address.
   *
   * @return \Drupal\mailgroup\Entity\MailGroupInterface
   *   The called group entity.
   */
  public function setEmail($email);

  /**
   * Gets the group email connection plugin.
   *
   * @return string
   *   The group email connection plugin.
   */
  public function getConnectionPlugin();

  /**
   * Sets the group email connection plugin.
   *
   * @param string $plugin
   *   The group email connection plugin.
   *
   * @return \Drupal\mailgroup\Entity\MailGroupInterface
   *   The called group entity.
   */
  public function setConnectionPlugin($plugin);

  /**
   * Returns the group status.
   *
   * @return bool
   *   TRUE if the group is active.
   */
  public function isActive();

  /**
   * Sets the group status.
   *
   * @param bool $active
   *   TRUE to set the group to active, FALSE to set it to inactive.
   *
   * @return \Drupal\mailgroup\Entity\MailGroupInterface
   *   The called group entity.
   */
  public function setActive($active);

  /**
   * Test the connection to the group email.
   */
  public function testConnection();

  /**
   * Gets the configuration of a group.
   *
   * @param string $name
   *   (optional) The key name of the configuration.
   */
  public function getConfig($name = '');

  /**
   * Gets the messages belonging to a group.
   *
   * @return array
   *   An array of loaded message entities.
   */
  public function getMessages();

  /**
   * Gets the members of the group.
   *
   * @return array
   *   An array of loaded membership entities.
   */
  public function getMembers();

  /**
   * Gets the email addresses of group members.
   *
   * @return array
   *   An array of member email addresses.
   */
  public function getMemberEmails();

  /**
   * Checks if a user is a member of the group.
   *
   * @param string $email
   *   The email address of the user to check.
   *
   * @return bool
   *   TRUE if the user is a member.
   */
  public function isMember($email);

}
