<?php

namespace Drupal\mailgroup\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the mail group entity base class.
 */
class MailGroupEntityBase extends ContentEntityBase implements MailGroupEntityInterface {

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

    $fields['gid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Group'))
      ->setDescription(t('The group to add the member to.'))
      ->setSetting('target_type', 'mailgroup')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -10,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setRequired(TRUE);

    return $fields;
  }

}
