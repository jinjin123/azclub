<?php

namespace Drupal\mailgroup\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form handler for mail group types.
 */
class MailGroupTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\mailgroup\Entity\MailGroupTypeInterface $type */
    $type = $this->entity;
    $form = parent::form($form, $form_state);

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#maxlength' => 255,
      '#default_value' => $type->label(),
      '#description' => $this->t('Name of the group type.'),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\mailgroup\Entity\MailGroupType::load',
      ],
      '#disabled' => !$type->isNew(),
    ];

    $form['description'] = [
      '#title' => $this->t('Description'),
      '#type' => 'textarea',
      '#default_value' => $type->getDescription(),
      '#description' => $this->t('Description of the group type.'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\mailgroup\Entity\MailGroupTypeInterface $type */
    $type = $this->entity;
    $status = $type->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Added the %label group type.', [
          '%label' => $type->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label group type.', [
          '%label' => $type->label(),
        ]));
    }

    $form_state->setRedirectUrl($type->toUrl('collection'));
  }

}
