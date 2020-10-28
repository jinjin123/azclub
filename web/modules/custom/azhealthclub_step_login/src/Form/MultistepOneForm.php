<?php

/**
 * @file
 * Contains \Drupal\azhealthclub_step_login\Form\MultistepOneForm.
 */

namespace Drupal\azhealthclub_step_login\Form;

use Drupal\Core\Form\FormStateInterface;

class MultistepOneForm extends MultistepFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'multistep_form_one';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

    $form['tips'] = [
      '#markup' => '<div class="tips">正服用阿斯利康藥物的人士，只要您想做好健康管理，都歡迎你加入成為「康心摯友」的會員（會用全免）。
「康心摯友」會定期送上會員通訊、電郵、或短訊等，為您提供最新醫學情報及健康小貼士。</div>',
    ];
    $form['phone'] = [
      '#type' => 'tel',
      '#title' => '',
      //'#pattern' => '[\d]*',
      '#default_value' => $this->store->get('phone') ? $this->store->get('phone') : '',
      '#attributes' => ['placeholder' => '電話號碼*'],
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => '',
      '#default_value' => $this->store->get('email') ? $this->store->get('email') : '',
      '#attributes' => ['placeholder' => '電郵地址*'],
    ];

    $form['pass'] = [
      '#type' => 'password_confirm',
      '#size' => 25,
    ];

    $form['verify_code'] = [
      '#type' => 'textfield',
      '#title' => '發送驗證碼',
      '#default_value' => $this->store->get('verify_code') ? $this->store->get('verify_code') : '',
      '#attributes' => ['placeholder' => '輸入驗證碼'],
    ];

    $statement = '<div class="statement">
  <div>
  顧客可自行決定是否參與本會員優惠計劃。申請人須填妥本申請表格，
  並提供本表格所要求之資料，經確認登記成功後方可享受有關優惠。
  </div>
  <div>
  阿斯利康於本會員優惠計劃中收集個人資料將會用作（i）處理會員優惠計劃申請；（ii）醫療資料通訊；
  （iii）市場分析；及（iv）於本人同意之情形下，開展市場推廣活動（具體資訊見下文），上述一切用途均需符合本公司私隱政策聲明，
  該聲明可透過向本公司寫信索取，並註明阿斯利康私隱專員收，郵寄地址：香港中央郵政信箱8717號。
  </div>
</div>';
    $form['privacy_statement'] = [
      '#type' => 'details',
      '#title' => '個人資料私隱政策聲明',
      '#description' => $statement,
      '#open' => TRUE,
    ];

    $entityFieldManager = \Drupal::service('entity_field.manager');
    $memberFields = $entityFieldManager->getFieldDefinitions('profile', 'member');
    $field_settings = $memberFields['field_attention1']->getSettings();
    $allowed_values = $field_settings['allowed_values'];

    $form['attention1'] = [
      '#type' => 'checkboxes',
      '#title' => '重要事項',
      '#options' => $allowed_values,
      '#default_value' => $this->store->get('attention1') ? $this->store->get('attention1') : [],
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];

    $form['notice'] = [
      '#markup' => '（如以上方格沒有打鉤，阿斯利康未得到本人同意，申請將不被接納）',
    ];

    $form['actions']['submit']['#value'] = '下一步';
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->store->set('phone', $form_state->getValue('phone'));
    $this->store->set('email', $form_state->getValue('email'));
    $this->store->set('pass', $form_state->getValue('pass'));
    //$this->store->set('verify_code', $form_state->getValue('verify_code'));
    $this->store->set('attention1', $form_state->getValue('attention1'));

    $form_state->setRedirect('azhealthclub_step_login.multistep_two');
  }
}
