<?php

namespace Drupal\mailgroup\Entity;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\mailgroup\Event\MailGroupMessageEvent;

/**
 * Defines the mail group message entity.
 *
 * @ingroup mailgroup
 *
 * @ContentEntityType(
 *   id = "mailgroup_message",
 *   label = @Translation("Message"),
 *   label_collection = @Translation("Messages"),
 *   label_singular = @Translation("message"),
 *   label_plural = @Translation("messages"),
 *   label_count = @PluralTranslation(
 *     singular = "@count message",
 *     plural = "@count messages"
 *   ),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\mailgroup\MailGroupMessageListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\mailgroup\Form\MailGroupMessageForm",
 *       "add" = "Drupal\mailgroup\Form\MailGroupMessageForm",
 *       "edit" = "Drupal\mailgroup\Form\MailGroupMessageForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "access" = "Drupal\mailgroup\MailGroupEntityAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\mailgroup\MailGroupEntityRouteProvider",
 *     },
 *   },
 *   base_table = "mailgroup_message",
 *   admin_permission = "administer message entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "subject",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/mailgroup/{mailgroup}/message/{mailgroup_message}",
 *     "add-form" = "/mailgroup/{mailgroup}/message/add",
 *     "edit-form" = "/mailgroup/{mailgroup}/message/{mailgroup_message}/edit",
 *     "delete-form" = "/mailgroup/{mailgroup}/message/{mailgroup_message}/delete",
 *     "collection" = "/mailgroup/{mailgroup}/messages",
 *   }
 * )
 */
class MailGroupMessage extends MailGroupEntityBase implements MailGroupMessageInterface {

  use StringTranslationTrait;
  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public function getSubject() {
    $subject = $this->get('subject')->value;

    $group = $this->getGroup();
    $config = $group->getConfig();

    if ($config['prepend_name']) {
      $name = $group->getName();
      $subject = "[$name] $subject";
    }

    return $subject;
  }

  /**
   * {@inheritdoc}
   */
  public function setSubject($subject) {
    $this->set('subject', $subject);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getSender() {
    return $this->get('sender')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setSender($subject) {
    $this->set('sender', $subject);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getBody() {
    return $this->get('body')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setBody($body) {
    $this->set('body', $body);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);
    $fields['gid']->setDescription(t('The group to add the message to.'));

    $fields['subject'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Subject'))
      ->setDescription(t('The subject of the message.'))
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
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['sender'] = BaseFieldDefinition::create('email')
      ->setLabel(t('Sender'))
      ->setDescription(t('The sender of the message.'))
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
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['body'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Body'))
      ->setDescription(t('The body of the message.'))
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
        'type' => 'string_long',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    $group = $this->getGroup();
    $sender = $this->getSender();

    if (!$group->isActive()) {
      $message = $this->t('Message to @group not sent. The group is not active.', [
        '@group' => $group->getName(),
      ]);
      throw new \Exception($message);
    }

    if (!$group->isMember($sender)) {
      $message = $this->t('Message to @group rejected. @email is not a member.', [
        '@group' => $group->getName(),
        '@email' => $sender,
      ]);
      throw new \Exception($message);
    }

    parent::preSave($storage);
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);

    $event = new MailGroupMessageEvent($this);
    $event_dispatcher = \Drupal::service('event_dispatcher');
    $event_dispatcher->dispatch(MailGroupMessageEvent::CREATE, $event);
  }

}
