<?php

namespace Drupal\mailgroup\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\mailgroup\Entity\MailGroupInterface;

/**
 * Base controller for mail group entities.
 */
class MailGroupEntityController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function listing($entity_type, MailGroupInterface $mailgroup) {
    /** @var \Drupal\mailgroup\MailGroupEntityListBuilder $list_builder */
    $list_builder = $this->entityTypeManager()->getListBuilder($entity_type);
    return $list_builder->setGroup($mailgroup)->render();
  }

}
