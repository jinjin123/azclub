<?php

namespace Drupal\mailgroup\Plugin\Mailgroup\Connection;

use Drupal\mailgroup\ConnectionBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Fetch\Server;

/**
 * Basic server connection plugin.
 *
 * @Plugin(
 *   id = "basic",
 *   label = @Translation("Basic")
 * )
 */
class Basic extends ConnectionBase implements ContainerFactoryPluginInterface {

  /**
   * The mail manager.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MailManagerInterface $mail_manager, LanguageManagerInterface $language_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->mailManager = $mail_manager;
    $this->languageManager = $language_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.mail'),
      $container->get('language_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFields() {
    $config = $this->configuration;

    $fields['protocol'] = [
      '#type' => 'select',
      '#title' => $this->t('Protocol'),
      '#options' => [
        'imap' => 'IMAP',
        'pop3' => 'POP3',
      ],
      '#default_value' => isset($config['protocol']) ? $config['protocol'] : NULL,
    ];

    $fields['username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Username'),
      '#default_value' => isset($config['username']) ? $config['username'] : NULL,
    ];

    $fields['password'] = [
      '#type' => 'password',
      '#title' => $this->t('Password'),
      '#default_value' => isset($config['password']) ? $config['password'] : NULL,
    ];

    $fields['server'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Server'),
      '#default_value' => isset($config['server']) ? $config['server'] : NULL,
    ];

    $fields['port'] = [
      '#type' => 'number',
      '#title' => $this->t('Port'),
      '#default_value' => isset($config['port']) ? $config['port'] : NULL,
    ];

    $fields['secure'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use SSL/TLS'),
      '#default_value' => isset($config['secure']) ? $config['secure'] : NULL,
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function testConnection() {
    $config = $this->configuration;

    try {
      $server = new Server($config['server'], $config['port']);
      $server->setAuthentication($config['username'], $config['password']);
      $server->getImapStream();
    }
    catch (\RuntimeException $e) {
      return FALSE;
    }

    return TRUE;
  }

}
