<?php

namespace Drupal\azhealthclub_modify\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
* Configure settings for this site.
*/
class SettingsForm extends ConfigFormBase {
  /**
  * {@inheritdoc}
  */
  public function getFormId() {
    return 'azhealthclub_modify_settings_form';
  }

  /**
  * {@inheritdoc}
  */
  protected function getEditableConfigNames() {
    return [
      'azhealthclub_modify.settings',
    ];
  }

  /**
  * {@inheritdoc}
  */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('azhealthclub_modify.settings');
    $contentTypes = \Drupal::entityTypeManager()->getStorage('node_type')->loadMultiple();
    $contentTypesList = [];
    foreach ($contentTypes as $contentType) {
      $contentTypesList[$contentType->id()] = $contentType->label();
    }

    $form['ta_content_types'] = array(
      '#type' => 'checkboxes',
      '#options' => $contentTypesList,
      '#title' => $this->t('Choose all therapeutic area content types'),
      '#description' => '',
      '#default_value' => $config->get('ta_content_types') ? : [],
    );

    return parent::buildForm($form, $form_state);
  }

  /**
  * {@inheritdoc}
  */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration
    $this->configFactory->getEditable('azhealthclub_modify.settings')
    // Set the submitted configuration setting
    ->set('ta_content_types', array_filter($form_state->getValue('ta_content_types')))
    ->save();

    parent::submitForm($form, $form_state);
  }
}
