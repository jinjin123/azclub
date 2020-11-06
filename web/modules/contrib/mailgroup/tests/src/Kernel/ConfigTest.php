<?php

namespace Drupal\Tests\mailgroup\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Core\Database\Database;

/**
 * Tests for MailGroupConfig.
 *
 * @group mailgroup
 */
class ConfigTest extends KernelTestBase {

  /**
   * The config service.
   *
   * @var \Drupal\mailgroup\MailGroupConfig
   */
  protected $config;

  /**
   * The modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'mailgroup',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installSchema('mailgroup', 'mailgroup_config');
    $this->config = \Drupal::service('mailgroup.config');

    $connection = Database::getConnection();
    $connection->insert('mailgroup_config')
      ->fields(['id', 'name', 'value'])
      ->values([
        'id' => 1,
        'name' => 'prepend_name',
        'value' => 1,
      ])
      ->values([
        'id' => 1,
        'name' => 'reply_to',
        'value' => 'all',
      ])
      ->execute();
  }

  /**
   * Test getting a single value.
   */
  public function testGet() {
    $value = $this->config->get(1, 'prepend_name');
    $this->assertEqual($value, 1);
  }

  /**
   * Test getting all values.
   */
  public function testGetAll() {
    $config = $this->config->get(1);
    $this->assertArrayHasKey('reply_to', $config);
    $this->assertEqual($config['reply_to'], 'all');
  }

  /**
   * Test setting a value.
   */
  public function testSet() {
    $this->config->set(1, 'test', 'Test');
    $value = $this->config->get(1, 'test');
    $this->assertEqual($value, 'Test');
  }

  /**
   * Test updating a value.
   */
  public function testUpdate() {
    $this->config->set(1, 'reply_to', 'sender');
    $value = $this->config->get(1, 'reply_to');
    $this->assertEqual($value, 'sender');
  }

}
