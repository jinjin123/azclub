<?php

namespace Drupal\mailgroup\Controller;

use Drupal\Core\Entity\Controller\EntityController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Link;

/**
 * Controller for mail groups.
 */
class MailGroupController extends EntityController {

  /**
   * {@inheritdoc}
   */
  public function addPage($entity_type_id) {
    $build = parent::addPage($entity_type_id);

    if ($build instanceof RedirectResponse) {
      return $build;
    }

    $entity_type = $this->entityTypeManager->getDefinition($entity_type_id);
    $bundle_entity_type_id = $entity_type->getBundleEntityType();
    $bundle_entity_type = $this->entityTypeManager->getDefinition($bundle_entity_type_id);

    $link_text = $this->t('Add a new @entity_type.', ['@entity_type' => $bundle_entity_type->getSingularLabel()]);
    $link_route_name = 'entity.mailgroup_type.add_form';
    $build['#add_bundle_message'] = $this->t('There are no @entity_type yet. @add_link', [
      '@entity_type' => $bundle_entity_type->getPluralLabel(),
      '@add_link' => Link::createFromRoute($link_text, $link_route_name)->toString(),
    ]);

    return $build;
  }

}
