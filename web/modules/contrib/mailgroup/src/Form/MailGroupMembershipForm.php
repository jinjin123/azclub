<?php

namespace Drupal\mailgroup\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for group membership edit forms.
 */
class MailGroupMembershipForm extends ContentEntityForm {

  /**
   * The entity being used by this form.
   *
   * @var \Drupal\mailgroup\Entity\MailGroupMembershipInterface
   */
  protected $entity;

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    /** @var \Drupal\user\UserInterface $account */
    $account = $this->entity->getUser();
    $email = $account->getEmail();
    $this->entity->setEmail($email);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = &$this->entity;
    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the group membership.'));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the group membership.'));
    }

    $form_state->setRedirect('entity.mailgroup_membership.collection', ['mailgroup' => $entity->getGroupId()]);
  }

}
