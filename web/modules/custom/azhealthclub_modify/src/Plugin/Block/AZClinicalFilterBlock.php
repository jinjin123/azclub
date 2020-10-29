<?php

namespace Drupal\azhealthclub_modify\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Provides a 'AZClinicalFilterBlock' block.
 *
 * @Block(
 *  id = "az_clinical_filter_block",
 *  admin_label = @Translation("AZClinicalFilterBlock"),
 * )
 */
class AZClinicalFilterBlock extends BlockBase
{

  /**
   * Drupal\Core\Entity\EntityManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * Symfony\Component\DependencyInjection\ContainerAwareInterface definition.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerAwareInterface
   */
  protected $entityQuery;


  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
  {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->entityManager = $container->get('entity.manager');
    $instance->entityQuery = $container->get('entity.query');
    return $instance;
  }

  public function build()
  {

    $database = \Drupal::database();

    $query = $database->select('paragraph__field_az_clinical_age', 'p');
    $age = $query->fields('p', ['field_az_clinical_age_value'])
      ->distinct()
      ->execute()
      ->fetchAll();

    $tumor = $database->select('taxonomy_term_field_data', 'p')
      ->condition('vid','tumor_tag')
      ->fields('p', ['name'])
      ->execute()
      ->fetchAll();

    $sexx = $database->select('paragraph_revision__field_az_se', 'p')
      ->fields('p', ['field_az_se_value'])
      ->distinct()
      ->execute()
      ->fetchAll();

    $variables = [
      'sexx' => $sexx,
      'tumor' => $tumor,
      'age' => $age
    ];
    $build = [];
    $build['#variables'] = $variables;
    $build["#theme"] = 'az_clinical_filter_block';
    return $build;
  }
}
