<?php

namespace Drupal\mailgroup_amazonses\Plugin\Mailgroup\Connection;

use Drupal\mailgroup\ConnectionBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

/**
 * Amazon SES connection plugin.
 *
 * @Plugin(
 *   id = "amazonses",
 *   label = @Translation("Amazon SES")
 * )
 */
class AmazonSes extends ConnectionBase implements ContainerFactoryPluginInterface {

  /**
   * The S3 client.
   *
   * @var \Aws\S3\S3Client
   */
  protected $s3;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, S3Client $s3_client) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->s3 = $s3_client;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('mailgroup_amazonses.s3_client')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFields() {
    $config = $this->configuration;

    $fields['bucket'] = [
      '#type' => 'textfield',
      '#title' => $this->t('S3 Bucket'),
      '#description' => $this->t('The S3 bucket email are stored in'),
      '#default_value' => isset($config['bucket']) ? $config['bucket'] : NULL,
    ];

    $fields['delete'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Delete after retrieval'),
      '#description' => $this->t('Delete messages from S3 after retrieval.'),
      '#default_value' => isset($config['delete']) ? $config['delete'] : TRUE,
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function testConnection() {
    $config = $this->configuration;

    try {
      $this->s3->listObjects([
        'Bucket' => $config['bucket'],
        'MaxKeys' => 1,
      ]);

      return TRUE;
    }

    catch (S3Exception $e) {
      return FALSE;
    }
  }

}
