<?php

namespace Drupal\block_visibility_groups\Plugin\Condition;

use Drupal\block_visibility_groups\GroupEvaluator;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Entity\DependencyTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Executable\ExecutableManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a 'Condition Group' condition.
 *
 * @Condition(
 *   id = "condition_group",
 *   label = @Translation("Condition Group"),
 * )
 */
class ConditionGroup extends ConditionPluginBase implements ContainerFactoryPluginInterface {

  use DependencyTrait;

  /**
   * The condition plugin manager.
   *
   * @var \Drupal\Core\Executable\ExecutableManagerInterface
   */
  protected $manager;

  /**
   * The entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $entityStorage;

  /**
   * The current Request object.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * @var \Drupal\block_visibility_groups\GroupEvaluator
   */
  protected $groupEvaluator;

  /**
   * Evaluates the condition and returns TRUE or FALSE accordingly.
   *
   * @return bool
   *   TRUE if the condition has been met, FALSE otherwise.
   */
  public function evaluate() {
    $block_visibility_group_id = $this->configuration['block_visibility_group'];
    if (empty($block_visibility_group_id)) {
      return TRUE;
    }
    /** @var \Drupal\block_visibility_groups\Entity\BlockVisibilityGroup $block_visibility_group */
    if ($block_visibility_group = $this->entityStorage->load($block_visibility_group_id)) {
      return $this->groupEvaluator->evaluateGroup($block_visibility_group);
    }
    else {
      // Group doesn't exist.
      // @todo How to handle?
      return FALSE;
    }

  }

  /**
   * Provides a human readable summary of the condition's configuration.
   */
  public function summary() {
    // TODO: Implement summary() method.
  }

  /**
   *
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $block_visibility_groups = $this->entityStorage->loadMultiple();
    $options = ['' => $this->t('No Block Visibility Group')];
    foreach ($block_visibility_groups as $type) {
      $options[$type->id()] = $type->label();
    }

    $form['block_visibility_group'] = [
      '#title' => $this->t('Block Visibility Groups'),
      '#type' => 'select',
      '#options' => $options,
      // '#default_value' => $default,.
    ];
    $default = isset($this->configuration['block_visibility_group']) ? $this->configuration['block_visibility_group'] : '';

    if (!$default) {
      $default = $this->request->query->get('block_visibility_group');
      if ($default) {
        $form['block_visibility_group']['#disabled'] = TRUE;
        $form_state->setTemporaryValue('block_visibility_group_query', $default);
      }
    }
    $form['block_visibility_group']['#default_value'] = $default;
    // TODO: Change the autogenerated stub.
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['negate']['#access'] = FALSE;
    return $form;
  }

  /**
   *
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $user_values = $form_state->getValues();
    foreach ($user_values as $key => $value) {
      if ($key != 'negate') {
        $this->configuration[$key] = $value;
      }
    }
    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * Creates an instance of the plugin.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container to pull out services used in the plugin.
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   *
   * @return static
   *   Returns an instance of this plugin.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $container->get('block_visibility_groups.group_evaluator');
    return new static(
      $container->get('entity_type.manager')->getStorage('block_visibility_group'),
      $container->get('plugin.manager.condition'),
      $container->get('request_stack')->getCurrentRequest(),
      $container->get('block_visibility_groups.group_evaluator'),
      $configuration,
      $plugin_id,
      $plugin_definition);
  }

  /**
   *
   */
  public function __construct(EntityStorageInterface $entity_storage, ExecutableManagerInterface $manager, Request $request, GroupEvaluator $group_evaluator, array $configuration, $plugin_id, $plugin_definition) {
    // TODO: Change the autogenerated stub.
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->manager = $manager;
    $this->entityStorage = $entity_storage;
    $this->request = $request;
    $this->groupEvaluator = $group_evaluator;
  }

  /**
   * {inheritdoc}.
   */
  public function calculateDependencies() {
    // Get dependencies from parent.
    $dependencies = parent::calculateDependencies();
    if (!empty($this->configuration['block_visibility_group'])) {
      $group = $this->entityStorage->load($this->configuration['block_visibility_group']);
      $this->addDependency('config', $group->getConfigDependencyName());
    }
    return $this->dependencies;
  }

  /**
   * {inheritdoc}.
   */
  public function getCacheTags() {
    $tags = parent::getCacheTags();
    if (!empty($this->configuration['block_visibility_group'])) {
      if ($group = $this->entityStorage->load($this->configuration['block_visibility_group'])) {
        $tags = Cache::mergeTags($tags, $group->getCacheTags());
      }
    }
    return $tags;
  }

}
