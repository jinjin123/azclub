<?php

namespace Drupal\mailgroup\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for mail group message edit forms.
 */
class MailGroupMessageForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\mailgroup\Entity\MailGroupMessage $entity */
    $entity = $this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the message.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the message.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.mailgroup_message.canonical', [
      'mailgroup' => $entity->getGroupId(),
      'mailgroup_message' => $entity->id(),
    ]);
  }

}
