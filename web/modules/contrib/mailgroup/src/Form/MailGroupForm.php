<?php

namespace Drupal\mailgroup\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\mailgroup\ConnectionPluginManager;
use Drupal\mailgroup\MailGroupConfig;

/**
 * Form controller for mail group edit forms.
 */
class MailGroupForm extends ContentEntityForm {

  /**
   * The mail group connection plugin manager.
   *
   * @var \Drupal\mailgroup\ConnectionPluginManager
   */
  protected $connectionManager;

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
   * @param \Drupal\mailgroup\ConnectionPluginManager $connectionManager
   *   The mail group connection plugin manager.
   * @param \Drupal\mailgroup\MailGroupConfig $config
   *   The mail group configuration service.
   */
  public function __construct(EntityRepositoryInterface $entity_repository, EntityTypeBundleInfoInterface $entity_type_bundle_info = NULL, TimeInterface $time = NULL, ConnectionPluginManager $connectionManager, MailGroupConfig $config) {
    parent::__construct($entity_repository, $entity_type_bundle_info, $time);
    $this->connectionManager = $connectionManager;
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
      $container->get('plugin.manager.mailgroup_connection'),
      $container->get('mailgroup.config')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\mailgroup\Entity\MailGroupInterface $entity */
    $entity = $this->entity;
    $form = parent::buildForm($form, $form_state);
    $form['#tree'] = TRUE;

    $form['plugin']['widget']['#ajax'] = [
      'callback' => 'Drupal\mailgroup\Form\MailGroupForm::connectionFields',
      'event' => 'change',
      'wrapper' => 'edit-connection',
      'progress' => [
        'type' => 'throbber',
        'message' => $this->t('Loading fields...'),
      ],
    ];

    $form['connection'] = [
      '#type' => 'details',
      '#title' => $this->t('Connection'),
      '#open' => TRUE,
      '#attributes' => [
        'id' => ['edit-connection'],
      ],
    ];

    /** @var \Drupal\mailgroup\ConnectionInterface $plugin */
    $plugin = $entity->getConnectionPlugin();
    if ($plugin) {
      $fields = $plugin->getFields();
      $form['connection'] += $fields;
    }

    $form['settings'] = [
      '#type' => 'details',
      '#title' => $this->t('Settings'),
      '#open' => TRUE,
    ];

    $prepend_name = $this->config->get($entity->id(), 'prepend_name');
    $form['settings']['prepend_name'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Prepend group name to the subject'),
      '#default_value' => isset($prepend_name) ? $prepend_name : TRUE,
    ];

    $reply_to = $this->config->get($entity->id(), 'reply_to');
    $form['settings']['reply_to'] = [
      '#type' => 'select',
      '#title' => $this->t('Reply to'),
      '#description' => $this->t('Who should replies ot group messages be sent to.'),
      '#options' => [
        'all' => $this->t('All members'),
        'sender' => $this->t('Sender only'),
      ],
      '#default_value' => isset($reply_to) ? $reply_to : 'all',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\mailgroup\Entity\MailGroupInterface $entity */
    $entity = $this->entity;
    $status = parent::save($form, $form_state);

    $entity_id = $entity->id();
    $this->config->delete($entity_id);

    $settings = $form_state->getValue('settings');
    foreach ($settings as $key => $value) {
      if ($value) {
        $this->config->set($entity_id, $key, $value);
      }
    }

    $config = $form_state->getValue('connection');
    foreach ($config as $key => $value) {
      if ($value) {
        $this->config->set($entity_id, $key, $value);
      }
    }

    if ($status == SAVED_NEW) {
      $this->messenger()->addMessage($this->t('Created the %label group.', [
        '%label' => $entity->label(),
      ]));
    }
    else {
      $this->messenger()->addMessage($this->t('Saved the %label group.', [
        '%label' => $entity->label(),
      ]));
    }

    $form_state->setRedirect('entity.mailgroup.canonical', ['mailgroup' => $entity->id()]);
  }

  /**
   * AJAX callback to add connection plugin fields to the mail group form.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public static function connectionFields(array &$form, FormStateInterface $form_state) {
    $plugin_value = $form_state->getValue('plugin');
    $plugin_id = $plugin_value[0]['value'];
    $connection_manager = \Drupal::service('plugin.manager.mailgroup_connection');
    /** @var \Drupal\mailgroup\ConnectionInterface $plugin */
    $plugin = $connection_manager->createInstance($plugin_id);
    $fields = $plugin->getFields();

    $form['connection'] = [
      '#type' => 'details',
      '#title' => t('Connection'),
      '#open' => TRUE,
      '#attributes' => [
        'id' => ['edit-connection'],
      ],
    ];

    $form['connection'] += $fields;

    return $form['connection'];
  }

}
