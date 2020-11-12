<?php

namespace Drupal\mailgroup\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Mail group entity.
 *
 * @ingroup mailgroup
 *
 * @ContentEntityType(
 *   id = "mailgroup",
 *   label = @Translation("Mail group"),
 *   label_collection = @Translation("Mail groups"),
 *   label_singular = @Translation("group"),
 *   label_plural = @Translation("groups"),
 *   label_count = @PluralTranslation(
 *     singular = "@count group",
 *     plural = "@count groups"
 *   ),
 *   bundle_label = @Translation("mail group type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\mailgroup\MailGroupListBuilder",
 *     "views_data" = "Drupal\mailgroup\MailGroupViewsData",
 *     "form" = {
 *       "default" = "Drupal\mailgroup\Form\MailGroupForm",
 *       "add" = "Drupal\mailgroup\Form\MailGroupForm",
 *       "edit" = "Drupal\mailgroup\Form\MailGroupForm",
 *       "delete" = "Drupal\mailgroup\Form\MailGroupDeleteForm",
 *     },
 *     "access" = "Drupal\mailgroup\MailGroupAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\mailgroup\MailGroupRouteProvider",
 *     },
 *   },
 *   base_table = "mailgroup",
 *   admin_permission = "administer mail groups",
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "type",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "uid",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/mailgroup/{mailgroup}",
 *     "add-page" = "/admin/mailgroup/add",
 *     "add-form" = "/admin/mailgroup/add/{mailgroup_type}",
 *     "edit-form" = "/mailgroup/{mailgroup}/edit",
 *     "delete-form" = "/mailgroup/{mailgroup}/delete",
 *     "collection" = "/admin/mailgroup",
 *   },
 *   bundle_entity_type = "mailgroup_type",
 *   field_ui_base_route = "entity.mailgroup_type.edit_form"
 * )
 */
class MailGroup extends ContentEntityBase implements MailGroupInterface {
  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'uid' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function loadByEmail($email) {
    try {
      $entity_storage = \Drupal::entityTypeManager()->getStorage('mailgroup');
      $group = $entity_storage->loadByProperties(['email' => $email]);
      return reset($group);
    }

    catch (\Exception $e) {
      return FALSE;
    }

  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('uid', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('uid')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getEmail() {
    return $this->get('email')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setEmail($email) {
    $this->set('email', $email);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getConnectionPlugin() {
    $plugin_id = $this->get('plugin')->value;
    if ($plugin_id) {
      $configManager = \Drupal::service('mailgroup.config');
      $config = $configManager->get($this->id());
      $connectionManager = \Drupal::service('plugin.manager.mailgroup_connection');
      $plugin = $connectionManager->createInstance($plugin_id, $config);
    }
    else {
      $plugin = NULL;
    }

    return $plugin;
  }

  /**
   * {@inheritdoc}
   */
  public function setConnectionPlugin($plugin) {
    $this->set('plugin', $plugin);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isActive() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setActive($active) {
    $this->set('status', $active ? TRUE : FALSE);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function testConnection() {
    /** @var \Drupal\mailgroup\ConnectionInterface $plugin */
    $plugin = $this->getConnectionPlugin();
    $status = $plugin->testConnection();

    return $status;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig($name = '') {
    $config = \Drupal::service('mailgroup.config');

    if ($name) {
      return $config->get($this->id(), $name);
    }

    return $config->get($this->id());
  }

  /**
   * {@inheritdoc}
   */
  public function getMessages() {
    $query = \Drupal::entityQuery('mailgroup_message');
    $query->condition('gid', $this->id());
    $message_ids = $query->execute();

    $messages = MailGroupMembership::loadMultiple($message_ids);

    return $messages;
  }

  /**
   * {@inheritdoc}
   */
  public function getMembers() {
    $query = \Drupal::entityQuery('mailgroup_membership');
    $query->condition('gid', $this->id());
    $query->condition('status', 1);
    $membership_ids = $query->execute();

    $members = MailGroupMembership::loadMultiple($membership_ids);

    return $members;
  }

  /**
   * {@inheritdoc}
   */
  public function getMemberEmails() {
    $emails = [];

    $query = \Drupal::entityQuery('mailgroup_membership');
    $query->condition('gid', $this->id());
    $query->condition('status', 1);
    $membership_ids = $query->execute();

    $members = MailGroupMembership::loadMultiple($membership_ids);
    foreach ($members as $member) {
      /** @var \Drupal\mailgroup\Entity\MailGroupMembershipInterface $member */
      $emails[] = $member->getUser()->getEmail();
    }

    return $emails;
  }

  /**
   * {@inheritdoc}
   */
  public function isMember($email) {
    /** @var \Drupal\mailgroup\Entity\MailGroupMembershipInterface $membership */
    $membership = MailGroupMembership::loadByEmail($email, $this);
    return $membership ? TRUE : FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the group.'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -10,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -10,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Group creator'))
      ->setDescription(t('The username of the group creator.'))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDefaultValueCallback('Drupal\group\Entity\Group::getCurrentUserId')
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['email'] = BaseFieldDefinition::create('email')
      ->setLabel(t('Email'))
      ->setDescription(t('The email address of the group.'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $connectionManager = \Drupal::service('plugin.manager.mailgroup_connection');
    $plugins = $connectionManager->getDefinitions();
    $plugin_options = [];
    foreach ($plugins as $plugin) {
      $plugin_options[$plugin['id']] = $plugin['label'];
    }

    $fields['plugin'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Connection plugin'))
      ->setDescription(t('The connection plugin to use to receive mail.'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
        'allowed_values' => $plugin_options,
      ])
      ->setDefaultValue('')
      ->setRequired(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'list_default',
        'weight' => -5,
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Active'))
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => TRUE,
        ],
        'weight' => 20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
