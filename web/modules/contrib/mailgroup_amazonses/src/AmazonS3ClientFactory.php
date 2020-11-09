<?php

namespace Drupal\mailgroup_amazonses;

use Drupal\Core\Config\ConfigFactory;
use Aws\S3\S3Client;

/**
 * Factory class for AWS S3Client instances.
 */
class AmazonS3ClientFactory {

  /**
   * Creates an AWS SesClient instance.
   *
   * @param array $options
   *   The default client options.
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   *   The config factory.
   *
   * @return \Aws\S3\S3Client
   *   The client instance.
   */
  public static function createInstance(array $options, ConfigFactory $configFactory) {
    $settings = $configFactory->get('aws_secrets_manager.settings');

    $options['region'] = $settings->get('aws_region');

    $awsKey = $settings->get('aws_key');
    $awsSecret = $settings->get('aws_secret');

    if (!empty($awsKey) && !empty($awsSecret)) {
      $options['credentials'] = [
        'key' => $awsKey,
        'secret' => $awsSecret,
      ];
    }

    return new S3Client($options);
  }

}
