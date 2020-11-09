<?php

namespace Drupal\mailgroup;

/**
 * Defines the mail group config service interface.
 */
interface MailGroupConfigInterface {

  /**
   * Returns configuration data.
   *
   * @param int $id
   *   The group ID of the configuration.
   * @param string $name
   *   (optional) The key name of the configuration.
   *
   * @return mixed|array
   *   The requested configuration, depending on the arguments passed:
   *   - For $id and $name, the stored value is returned, or NULL if
   *     no value was found.
   *   - For $id only, an associative array is returned that contains all
   *     configuration for the group.
   */
  public function get($id, $name = NULL);

  /**
   * Stores configuration data.
   *
   * @param int $id
   *   The group ID of the configuration.
   * @param string $name
   *   The key name of the configuration.
   * @param mixed $value
   *   The configuration value to store.
   */
  public function set($id, $name, $value);

  /**
   * Deletes configuration data.
   *
   * @param int|array $id
   *   The group ID of the configuration. Can also be an array of IDs
   *   to delete the configuration of multiple group.
   * @param string $name
   *   (optional) The key name of the configuration. If omitted, all
   *   configuration for a group is deleted.
   */
  public function delete($id, $name = NULL);

}
