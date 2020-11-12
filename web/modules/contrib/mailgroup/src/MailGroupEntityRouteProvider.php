<?php

namespace Drupal\mailgroup;

use Drupal\Core\Entity\Routing\AdminHtmlRouteProvider;
use Drupal\Core\Entity\EntityTypeInterface;
use Symfony\Component\Routing\Route;

/**
 * Provides routes for mail group entities.
 */
class MailGroupEntityRouteProvider extends AdminHtmlRouteProvider {

  /**
   * {@inheritdoc}
   */
  public function getCollectionRoute(EntityTypeInterface $entity_type) {
    $route = parent::getCollectionRoute($entity_type);
    $route->setDefault('_entity_list', NULL)
      ->setDefault('_controller', '\Drupal\mailgroup\Controller\MailGroupEntityController::listing')
      ->setDefault('entity_type', $entity_type->id())
      ->setOption('parameters', [
        'mailgroup' => ['type' => 'entity:mailgroup'],
      ])
      ->setOption('_admin_route', TRUE);

    return $route;
  }

  /**
   * {@inheritdoc}
   */
  public function getAddFormRoute(EntityTypeInterface $entity_type) {
    $route = parent::getAddFormRoute($entity_type);
    return $this->modifyFormRoute($route, $entity_type);
  }

  /**
   * {@inheritdoc}
   */
  public function getEditFormRoute(EntityTypeInterface $entity_type) {
    $route = parent::getEditFormRoute($entity_type);
    return $this->modifyFormRoute($route, $entity_type);
  }

  /**
   * {@inheritdoc}
   */
  protected function getDeleteFormRoute(EntityTypeInterface $entity_type) {
    $route = parent::getDeleteFormRoute($entity_type);
    return $this->modifyFormRoute($route, $entity_type);
  }

  /**
   * Modify a group entity route.
   *
   * @param \Symfony\Component\Routing\Route $route
   *   The route to modify.
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route
   *   The route being modified.
   */
  protected function modifyFormRoute(Route $route, EntityTypeInterface $entity_type) {
    $parameters = $route->getOption('parameters');
    $parameters['mailgroup'] = ['type' => 'entity:mailgroup'];

    $route->setDefault('_controller', '\Drupal\mailgroup\Controller\MailGroupEntityFormController::getContent')
      ->setDefault('entity_type', $entity_type->id())
      ->setOption('parameters', $parameters);

    return $route;
  }

}
