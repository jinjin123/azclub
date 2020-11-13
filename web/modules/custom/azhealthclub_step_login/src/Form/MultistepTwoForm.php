<?php

/**
 * @file
 * Contains \Drupal\azhealthclub_step_login\Form\MultistepTwoForm.
 */

namespace Drupal\azhealthclub_step_login\Form;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

class MultistepTwoForm extends MultistepFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'multistep_form_two';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

    $entityFieldManager = \Drupal::service('entity_field.manager');
    $memberFields = $entityFieldManager->getFieldDefinitions('profile', 'member');

    $form['zh_name'] = [
      '#type' => 'textfield',
      '#title' => '',
      '#default_value' => $this->store->get('zh_name') ? $this->store->get('zh_name') : '',
      '#attributes' => ['placeholder' => '*中文名字'],
      '#prefix' => '<div class="az-form-element-container"><div class="col-md-6 col-xs-12">',
      '#suffix' => '</div>',
      '#size' => 30,
    ];

    $form['en_name'] = [
      '#type' => 'textfield',
      '#title' => '',
      '#default_value' => $this->store->get('en_name') ? $this->store->get('en_name') : '',
      '#attributes' => ['placeholder' => '*英文名字'],
      '#prefix' => '<div class="col-md-6 col-xs-12">',
      '#suffix' => '</div></div>',
      '#size' => 30,
    ];

    $form['identification_last4num'] = [
      '#type' => 'textfield',
      '#title' => '',
      '#default_value' => $this->store->get('identification_last4num') ? $this->store->get('identification_last4num') : '',
      '#attributes' => ['placeholder' => '*香港身份證號碼（頭4位數字）'],
      '#prefix' => '<div class="az-form-element-container"><div class="col-md-6 col-xs-12">',
      '#suffix' => '</div>',
      '#size' => 30,
    ];

    $bundle_fields = \Drupal::getContainer()->get('entity_field.manager')->getFieldDefinitions('profile', 'member');
    $field_definition = $bundle_fields['field_birthday'];
    $yearonly_from = $field_definition->getSetting('yearonly_from');
    $yearonly_to = $field_definition->getSetting('yearonly_to');
    if ($yearonly_to == 'now')  {
      $yearonly_to = date('Y', time());
    }
    $years = [];
    for ($year = intval($yearonly_from); $year <= intval($yearonly_to); $year++) {
      $years[$year]= $year;
    }
    $form['birthday'] = [
      '#type' => 'select',
      //'#title' => '*出生年份',
      '#options' => [0=>'*出生年份'] + $years,
      '#default_value' => $this->store->get('birthday') ? $this->store->get('birthday') : 0,
      '#prefix' => '<div class="col-md-3 col-xs-12">',
      '#suffix' => '</div>',
    ];

    $field_settings = $memberFields['field_gender']->getSettings();
    $allowed_values = $field_settings['allowed_values'];
    $form['gender'] = [
      '#type' => 'select',
      //'#title' => '*性別',
      '#options' => ['0' => '*性別' ] + $allowed_values,
      '#default_value' => $this->store->get('gender') ? $this->store->get('gender') : '',
      '#prefix' => '<div class="col-md-3 col-xs-12">',
      '#suffix' => '</div></div>',
    ];

    $field_settings = $memberFields['field_attention2']->getSettings();
    $allowed_values = $field_settings['allowed_values'];
    $form['attention2'] = [
      '#type' => 'checkboxes',
      '#options' => $allowed_values,
      '#default_value' => $this->store->get('attention2') ? $this->store->get('attention2') : [],
    ];

    $field_settings = $memberFields['field_communication_mode']->getSettings();
    $allowed_values = $field_settings['allowed_values'];
    $form['communication_mode'] = [
      '#type' => 'checkboxes',
      '#title' => '如閣下同意我們使用您的個人資料，透過以下方式通知您關於本公司會員優惠/活動、藥物產品及健康資訊，請於以下空格內打鉤「」：
      （如沒有填寫，我們沒法通知閣下有關會員優惠/活動發資訊）',
      '#options' => $allowed_values,
      '#default_value' => $this->store->get('communication_mode') ? $this->store->get('communication_mode') : [],
    ];

    $form['actions']['previous'] = [
      '#type' => 'link',
      '#title' => '上一步',
      '#attributes' => [
        'class' => ['btn'],
      ],
      '#weight' => 0,
      '#url' => Url::fromRoute('azhealthclub_step_login.multistep_one'),
    ];

    $form['actions']['submit']['#value'] = '下一步';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->store->set('zh_name', $form_state->getValue('zh_name'));
    $this->store->set('en_name', $form_state->getValue('en_name'));
    $this->store->set('identification_last4num', $form_state->getValue('identification_last4num'));
    $this->store->set('birthday', $form_state->getValue('birthday'));
    $this->store->set('gender', $form_state->getValue('gender'));
    $this->store->set('attention2', $form_state->getValue('attention2'));
    $this->store->set('communication_mode', $form_state->getValue('communication_mode'));

    $form_state->setRedirect('azhealthclub_step_login.multistep_three');
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $values = $form_state->getValues();

    if (empty($values['zh_name'])) {
      $form_state->setErrorByName('zh_name', '請輸入中文名字');
      $form['zh_name']['#attributes']['class'][] = 'az-error';
    }
    if (empty($values['en_name'])) {
      $form_state->setErrorByName('en_name', '請輸入英文名字');
      $form['en_name']['#attributes']['class'][] = 'az-error';
    }
    if (empty($values['identification_last4num'])) {
      $form_state->setErrorByName('identification_last4num', '請填寫香港身份證號碼（頭4位數字）');
      $form['identification_last4num']['#attributes']['class'][] = 'az-error';
    }
    if (empty($values['birthday'])) {
      $form_state->setErrorByName('birthday', '請選擇出生年份');
      $form['birthday']['#attributes']['class'][] = 'az-error';
    }
    if (empty($values['gender'])) {
      $form_state->setErrorByName('gender', '請選擇性別');
      $form['gender']['#attributes']['class'][] = 'az-error';
    }

  }


}
