<?php

namespace Drupal\mailgroup\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the mail group membership entity.
 *
 * @ContentEntityType(
 *   id = "mailgroup_membership",
 *   label = @Translation("Membership"),
 *   label_collection = @Translation("Memberships"),
 *   label_singular = @Translation("membership"),
 *   label_plural = @Translation("memberships"),
 *   label_count = @PluralTranslation(
 *     singular = "@count membership",
 *     plural = "@count memberships"
 *   ),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\mailgroup\MailGroupMembershipListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\mailgroup\Form\MailGroupMembershipForm",
 *       "add" = "Drupal\mailgroup\Form\MailGroupMembershipForm",
 *       "edit" = "Drupal\mailgroup\Form\MailGroupMembershipForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "access" = "Drupal\mailgroup\MailGroupEntityAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\mailgroup\MailGroupEntityRouteProvider",
 *     },
 *   },
 *   base_table = "mailgroup_membership",
 *   admin_permission = "administer mailgroups",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "uid" = "uid",
 *     "gid" = "gid",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "add-form" = "/mailgroup/{mailgroup}/members/add",
 *     "edit-form" = "/mailgroup/{mailgroup}/members/{mailgroup_membership}/edit",
 *     "delete-form" = "/mailgroup/{mailgroup}/members/{mailgroup_membership}/delete",
 *     "collection" = "/mailgroup/{mailgroup}/members",
 *   },
 * )
 */
class MailGroupMembership extends MailGroupEntityBase implements MailGroupMembershipInterface {

  /**
   * {@inheritdoc}
   */
  public static function loadByEmail($email, MailGroup $group = NULL) {
    /** @var \Drupal\mailgroup\Entity\MailGroupInterface $group */

    $properties = ['email' => $email];

    if ($group) {
      $properties['gid'] = $group->id();
    }

    try {
      $entity_storage = \Drupal::entityTypeManager()->getStorage('mailgroup_membership');
      $memberships = $entity_storage->loadByProperties($properties);

      if ($group) {
        return $memberships ? reset($memberships) : FALSE;
      }
      else {
        return $memberships ? $memberships : FALSE;
      }
    }

    catch (\Exception $e) {
      return FALSE;
    }

  }

  /**
   * {@inheritdoc}
   */
  public function getGroup() {
    return $this->get('gid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setGroup(MailGroupInterface $group) {
    $this->set('gid', $group->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getGroupId() {
    return $this->get('gid')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setGroupId($gid) {
    $this->set('gid', $gid);
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
  public function getUser() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setUser(UserInterface $account) {
    $this->set('uid', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getUserId() {
    return $this->get('uid')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setUserId($uid) {
    $this->set('uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getFirstName() {
    return $this->get('first_name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setFirstName($name) {
    $this->set('first_name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getLastName() {
    return $this->get('last_name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setLastName($name) {
    $this->set('last_name', $name);
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
  public function toUrl($rel = 'canonical', array $options = []) {
    $url = parent::toUrl($rel, $options);
    $url->setRouteParameter('mailgroup', $this->getGroupId());

    return $url;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);
    $fields['gid']->setDescription(t('The group to add the member to.'));

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('User'))
      ->setDescription(t('The user ID of the group member.'))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['first_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('First Name'))
      ->setDescription(t('The first name of the member.'))
      ->setSettings([
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['last_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Last Name'))
      ->setDescription(t('The last name of the member.'))
      ->setSettings([
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['email'] = BaseFieldDefinition::create('email')
      ->setLabel(t('Email'))
      ->setDescription(t('The email address of the member.'))
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
        'type' => 'hidden',
      ])
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
