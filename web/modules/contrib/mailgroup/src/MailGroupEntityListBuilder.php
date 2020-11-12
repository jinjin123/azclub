<?php

namespace Drupal\mailgroup;

use Drupal\Core\Entity\EntityListBuilder;
use Drupal\mailgroup\Entity\MailGroupInterface;

/**
 * Defines a class to build a listing of group entities.
 */
class MailGroupEntityListBuilder extends EntityListBuilder {

  /**
   * The group to list entities for.
   *
   * @var \Drupal\mailgroup\Entity\MailGroup
   */
  protected $group;

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = parent::render();
    $build['table']['#empty'] = $this->t('There are no @label yet.', [
      '@label' => $this->entityType->getPluralLabel(),
    ]);

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEntityIds() {
    $query = $this->getStorage()->getQuery()
      ->condition('gid', $this->group->id())
      ->sort($this->entityType->getKey('id'));

    if ($this->limit) {
      $query->pager($this->limit);
    }

    return $query->execute();
  }

  /**
   * Set the group to list entities for.
   *
   * @param \Drupal\mailgroup\Entity\MailGroupInterface $mailgroup
   *   The group to list entities for.
   *
   * @return \Drupal\mailgroup\MailGroupEntityListBuilder
   *   The list builder.
   */
  public function setGroup(MailGroupInterface $mailgroup) {
    /** @var \Drupal\mailgroup\Entity\MailGroupInterface $mailgroup */
    $this->group = $mailgroup;
    return $this;
  }

}
