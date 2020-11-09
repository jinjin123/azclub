<?php

namespace Drupal\mailgroup\Entity;

use Drupal\user\UserInterface;

/**
 * Provides an interface for defining mail group memberships.
 */
interface MailGroupMembershipInterface extends MailGroupEntityInterface {

  /**
   * Load a membership by email address.
   *
   * @param string $email
   *   The membership email address.
   * @param \Drupal\mailgroup\Entity\MailGroup $group
   *   (optional) A group to load the membership for.
   *
   * @return array|false
   *   An array of loaded memberships, or FALSE if a group with the specified
   *   email address isn't found.
   */
  public static function loadByEmail($email, MailGroup $group = NULL);

  /**
   * Gets the membership email address.
   *
   * @return string
   *   Email address of the membership.
   */
  public function getEmail();

  /**
   * Sets the membership email address.
   *
   * @param string $email
   *   The membership email address.
   *
   * @return \Drupal\mailgroup\Entity\MailGroupMembershipInterface
   *   The called membership entity.
   */
  public function setEmail($email);

  /**
   * Returns the user entity.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity.
   */
  public function getUser();

  /**
   * Sets the user entity.
   *
   * @param \Drupal\user\UserInterface $account
   *   The user entity.
   *
   * @return \Drupal\mailgroup\Entity\MailGroupMembershipInterface
   *   The called membership entity.
   */
  public function setUser(UserInterface $account);

  /**
   * Returns the user ID.
   *
   * @return int|null
   *   The user ID, or NULL if not set.
   */
  public function getUserId();

  /**
   * Sets the user ID.
   *
   * @param int $uid
   *   The user id.
   *
   * @return \Drupal\mailgroup\Entity\MailGroupMembershipInterface
   *   The called membership entity.
   */
  public function setUserId($uid);

  /**
   * Gets the member's first name.
   *
   * @return string
   *   First name of the member.
   */
  public function getFirstName();

  /**
   * Sets the member's first name.
   *
   * @param string $name
   *   The member's first name.
   *
   * @return \Drupal\mailgroup\Entity\MailGroupMembershipInterface
   *   The called membership entity.
   */
  public function setFirstName($name);

  /**
   * Gets the member's last name.
   *
   * @return string
   *   Last name of the member.
   */
  public function getLastName();

  /**
   * Sets the member's last name.
   *
   * @param string $name
   *   The member's last name.
   *
   * @return \Drupal\mailgroup\Entity\MailGroupMembershipInterface
   *   The called membership entity.
   */
  public function setLastName($name);

  /**
   * Returns the membership status.
   *
   * @return bool
   *   TRUE if the membership is active.
   */
  public function isActive();

  /**
   * Sets the status of a membership.
   *
   * @param bool $active
   *   TRUE to set this membership to active, FALSE to set it to inactive.
   *
   * @return \Drupal\mailgroup\Entity\MailGroupMembershipInterface
   *   The called membership entity.
   */
  public function setActive($active);

}
