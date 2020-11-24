<?php

namespace Drupal\azhealthclub_modify\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Provides a 'AZHeaderRight' block.
 *
 * @Block(
 *  id = "az_header_right_block",
 *  admin_label = @Translation("AZ Header Right Block"),
 * )
 */
class AZHeaderRightBlock extends BlockBase
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
    $variables = [];
    $build = [];
    $build['#variables'] = $variables;
    $build["#theme"] = 'az_header_right_block';
    return $build;
  }
}
