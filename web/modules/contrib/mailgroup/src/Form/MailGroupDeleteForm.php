<?php

namespace Drupal\mailgroup\Form;

use Drupal\Core\Entity\ContentEntityDeleteForm;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\mailgroup\MailGroupConfig;

/**
 * Builds the form to delete a mail group.
 */
class MailGroupDeleteForm extends ContentEntityDeleteForm {

  /**
   * The mail group configuration service.
   *
   * @var \Drupal\mailgroup\MailGroupConfig
   */
  protected $config;

  /**
   * Construct a mail group form.
   *
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository service.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle service.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   * @param \Drupal\mailgroup\MailGroupConfig $config
   *   The config for the Mail group.
   */
  public function __construct(EntityRepositoryInterface $entity_repository, EntityTypeBundleInfoInterface $entity_type_bundle_info = NULL, TimeInterface $time = NULL, MailGroupConfig $config) {
    parent::__construct($entity_repository, $entity_type_bundle_info, $time);
    $this->config = $config;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.repository'),
      $container->get('entity_type.bundle.info'),
      $container->get('datetime.time'),
      $container->get('mailgroup.config')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    /** @var \Drupal\mailgroup\Entity\MailGroupInterface $entity */
    $entity = $this->entity;
    $this->config->delete($entity->id());
  }

}
