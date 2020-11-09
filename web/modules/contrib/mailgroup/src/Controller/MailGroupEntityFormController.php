<?php

namespace Drupal\mailgroup\Controller;

use Drupal\Core\Entity\HtmlEntityFormController;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\mailgroup\Entity\MailGroupInterface;

/**
 * Controller for mail group entity forms.
 */
class MailGroupEntityFormController extends HtmlEntityFormController implements ContainerInjectionInterface {

  /**
   * The group to list entities for.
   *
   * @var \Drupal\mailgroup\Entity\MailGroupInterface
   */
  protected $group;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_kernel.controller.argument_resolver'),
      $container->get('form_builder'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getContent(Request $request, RouteMatchInterface $route_match, MailGroupInterface $mailgroup) {
    $this->group = $mailgroup;

    return parent::getContentResult($request, $route_match);
  }

  /**
   * {@inheritdoc}
   */
  protected function getFormObject(RouteMatchInterface $route_match, $form_arg) {
    /** @var \Drupal\Core\Entity\ContentEntityFormInterface $form_object */
    $form_object = parent::getFormObject($route_match, $form_arg);

    if ($this->group) {
      /** @var \Drupal\mailgroup\Entity\MailGroupEntityInterface $entity */
      $entity = $form_object->getEntity();
      $entity->setGroup($this->group);
      $form_object->setEntity($entity);
    }

    return $form_object;
  }

}
