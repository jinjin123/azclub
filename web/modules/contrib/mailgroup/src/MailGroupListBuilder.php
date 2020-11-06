<?php

namespace Drupal\mailgroup;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of mail groups.
 */
class MailGroupListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['name'] = $this->t('Name');
    $header['email'] = $this->t('Email address');
    $header['Status'] = $this->t('Status');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\mailgroup\Entity\MailGroupInterface $entity */

    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.mailgroup.canonical',
      ['mailgroup' => $entity->id()]
    );
    $row['email'] = $entity->getEmail();
    $row['status'] = $entity->isActive() ? $this->t('Active') : $this->t('Inactive');

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function getOperations(EntityInterface $entity) {
    $operations = $this->getDefaultOperations($entity);

    $operations['mailgroup_members'] = [
      'title' => $this->t('Members'),
      'weight' => 20,
      'url' => Url::fromRoute(
        'entity.mailgroup_membership.collection',
        ['mailgroup' => $entity->id()]
      ),
    ];

    $operations['mailgroup_messages'] = [
      'title' => $this->t('Messages'),
      'weight' => 20,
      'url' => Url::fromRoute(
        'entity.mailgroup_message.collection',
        ['mailgroup' => $entity->id()]
      ),
    ];

    $operations += $this
      ->moduleHandler()
      ->invokeAll('entity_operation', [$entity]);
    $this->moduleHandler->alter('entity_operation', $operations, $entity);
    uasort($operations, '\\Drupal\\Component\\Utility\\SortArray::sortByWeightElement');

    return $operations;
  }

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

}
