<?php

namespace Drupal\mailgroup\Entity;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Provides a base interface for defining mail group entities.
 */
interface MailGroupEntityInterface extends ContentEntityInterface {

  /**
   * Gets the group entity.
   *
   * @return \Drupal\mailgroup\Entity\MailGroupInterface
   *   Group of the membership.
   */
  public function getGroup();

  /**
   * Sets the group entity.
   *
   * @param \Drupal\mailgroup\Entity\MailGroupInterface $group
   *   The group entity.
   *
   * @return \Drupal\mailgroup\Entity\MailGroupMembershipInterface
   *   The called membership entity.
   */
  public function setGroup(MailGroupInterface $group);

  /**
   * Gets the group ID.
   *
   * @return int|null
   *   The group ID, or NULL if not set.
   */
  public function getGroupId();

  /**
   * Sets the group ID.
   *
   * @param int $gid
   *   The group of the membership.
   *
   * @return \Drupal\mailgroup\Entity\MailGroupMembershipInterface
   *   The called membership entity.
   */
  public function setGroupId($gid);

}
