<?php

namespace Drupal\mailgroup;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;

/**
 * Provides a listing of mail group types.
 */
class MailGroupTypeListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Group type');
    $header['id'] = $this->t('Machine name');
    $header['description'] = $this->t('Description');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\mailgroup\Entity\MailGroupTypeInterface $entity */

    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['description'] = $entity->getDescription();

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = parent::render();
    $build['table']['#empty'] = $this->t('There are no group types yet. <a href="@link">Add a group type</a>.', [
      '@link' => Url::fromRoute('entity.mailgroup_type.add_form')->toString(),
    ]);

    return $build;
  }

}
