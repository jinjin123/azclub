<?php

namespace Drupal\mailgroup;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Logger\LoggerChannelTrait;

/**
 * Mail group connection plugin base class.
 */
abstract class ConnectionBase extends PluginBase implements ConnectionInterface {

  use StringTranslationTrait;
  use LoggerChannelTrait;

  /**
   * The Mail group logger.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->logger = $this->getLogger('mailgroup');
  }

  /**
   * Get the current configuration for the plugin.
   *
   * @return array
   *   An array of configuration items.
   */
  public function getConfig() {
    return $this->configuration;
  }

}
