<?php

namespace Drupal\mailgroup;

use Drupal\Core\Entity\Routing\AdminHtmlRouteProvider;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Provides routes for mail groups.
 */
class MailGroupRouteProvider extends AdminHtmlRouteProvider {

  /**
   * {@inheritdoc}
   */
  public function getAddPageRoute(EntityTypeInterface $entity_type) {
    $route = parent::getAddPageRoute($entity_type);
    $route->setDefault('_controller', 'Drupal\mailgroup\Controller\MailGroupController::addPage');

    return $route;
  }

}
