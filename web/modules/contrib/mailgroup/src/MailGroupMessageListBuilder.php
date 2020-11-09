<?php

namespace Drupal\mailgroup;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of mail group messages.
 */
class MailGroupMessageListBuilder extends MailGroupEntityListBuilder {

  /**
   * The group to list messages for.
   *
   * @var \Drupal\mailgroup\Entity\MailGroup
   */
  protected $group;

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Message ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var $entity \Drupal\mailgroup\Entity\MailGroupMessage */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.mailgroup_message.edit_form', [
        'mailgroup' => $entity->getGroupId(),
        'mailgroup_message' => $entity->id(),
      ]
    );

    return $row + parent::buildRow($entity);
  }

}
