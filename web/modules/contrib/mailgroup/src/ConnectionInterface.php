<?php

namespace Drupal\mailgroup;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * An interface for mail group connection plugins.
 */
interface ConnectionInterface extends PluginInspectionInterface {

  /**
   * Get form fields for configuring the connection.
   *
   * @return array
   *   An array of fields to add to the mail group form.
   */
  public function getFields();

  /**
   * Test the connection to the email server.
   *
   * @return bool
   *   A boolean indicating if the connection was successful.
   */
  public function testConnection();

}
