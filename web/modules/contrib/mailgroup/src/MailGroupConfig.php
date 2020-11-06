<?php

namespace Drupal\mailgroup;

use Drupal\Core\Database\Connection;

/**
 * Defines the mail group config service.
 */
class MailGroupConfig implements MailGroupConfigInterface {

  /**
   * The database connection to use.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Constructs a new mail group config service.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection to use.
   */
  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public function get($id, $name = NULL) {
    /** @var \Drupal\Core\Database\Query\SelectInterface $query */
    $query = $this->connection->select('mailgroup_config', 'mc')
      ->fields('mc')
      ->condition('id', $id);

    if (isset($name)) {
      $query->condition('name', $name);
    }

    $result = $query->execute();

    if (isset($name)) {
      $result = $result->fetch();
      return $result->value;
    }
    else {
      $result = $result->fetchAll();
      $config = [];

      foreach ($result as $row) {
        $config[$row->name] = $row->value;
      }

      return $config;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function set($id, $name, $value) {
    $this->connection->merge('mailgroup_config')
      ->keys([
        'id' => $id,
        'name' => $name,
      ])
      ->fields([
        'value' => $value,
      ])
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function delete($id, $name = NULL) {
    $query = $this->connection->delete('mailgroup_config')
      ->condition('id', (array) $id, 'IN');

    if (isset($name)) {
      $query->condition('name', $name);
    }

    $query->execute();
  }

}
