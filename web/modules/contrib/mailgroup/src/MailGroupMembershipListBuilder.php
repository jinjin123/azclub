<?php

namespace Drupal\mailgroup;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of group memberships.
 */
class MailGroupMembershipListBuilder extends MailGroupEntityListBuilder {

  /**
   * The group to list members for.
   *
   * @var \Drupal\mailgroup\Entity\MailGroup
   */
  protected $group;

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['user'] = $this->t('User');
    $header['email'] = $this->t('Email Address');
    $header['first_name'] = $this->t('First Name');
    $header['last_name'] = $this->t('Last Name');
    $header['status'] = $this->t('Status');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\mailgroup\Entity\MailGroupMembershipInterface $entity */
    /** @var \Drupal\user\UserInterface $account */
    $account = $entity->getUser();

    $row['user'] = Link::createFromRoute(
      $account->label(),
      'entity.user.canonical',
      ['user' => $account->id()]
    );
    $row['email'] = $entity->getEmail();
    $row['first_name'] = $entity->getFirstName();
    $row['last_name'] = $entity->getLastName();
    $row['status'] = $entity->isActive() ? $this->t('Active') : $this->t('Inactive');

    return $row + parent::buildRow($entity);
  }

}
