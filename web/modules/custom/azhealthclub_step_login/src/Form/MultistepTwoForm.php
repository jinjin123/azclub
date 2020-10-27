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

    $form['zh_name'] = [
      '#type' => 'textfield',
      '#title' => '中文名字*',
      '#default_value' => $this->store->get('zh_name') ? $this->store->get('zh_name') : '',
    ];

    $form['en_name'] = [
      '#type' => 'textfield',
      '#title' => '英文名字*',
      '#default_value' => $this->store->get('en_name') ? $this->store->get('en_name') : '',
    ];

    $form['identification_last4num'] = [
      '#type' => 'textfield',
      '#title' => '香港身份證號碼（頭4位數字）*',
      '#default_value' => $this->store->get('identification_last4num') ? $this->store->get('identification_last4num') : '',
    ];

    $form['birthday'] = [
      '#type' => 'datetime',
      '#title' => '出生日萬期',
      '#default_value' => $this->store->get('birthday') ? $this->store->get('birthday') : new DrupalDateTime('2000-01-01 00:00:00'),
      '#date_date_element' => 'date',
      '#date_time_element' => 'none',
      '#date_year_range' => '2010:+3',
      '#date_timezone' => 'Asia/Kolkata',
    ];

    $form['gender'] = [
      '#type' => 'select',
      '#title' => '性別*',
      '#options' => [
        'male' => '男',
        'female' => '女',
      ],
      '#default_value' => $this->store->get('gender') ? $this->store->get('gender') : '',
    ];

    $form['field_attention']['member_attention1'] = [
      '#type' => 'checkbox',
      '#title' => '本人願意接收有關阿斯利康健康講座/活動、藥物產品，會員優惠發健康醫學等資訊，並同意可為該目的使用本人個人資料。',
      '#default_value' => $this->store->get('member_attention1') ? $this->store->get('member_attention1') : '',
    ];

    $form['communication_mode'] = [
      '#markup' => '如閣下同意我們使用您的個人資料，透過以下方式通知您關於本公司會員優惠/活動、藥物產品及健康資訊，請於以下空格內打鉤「」：
（如沒有填寫，我們沒法通知閣下有關會員優惠/活動發資訊）',
    ];
    $form['communication_mode']['phone']= [
      '#type' => 'checkbox',
      '#title' => '電話',
      '#default_value' => $this->store->get('communication_mode.phone') ? $this->store->get('communication_mode.phone') : '',
    ];
    $form['communication_mode']['phone_text']= [
      '#type' => 'checkbox',
      '#title' => '電話短訊',
      '#default_value' => $this->store->get('communication_mode.phone_text') ? $this->store->get('communication_mode.phone_text') : '',
    ];
    $form['communication_mode']['email']= [
      '#type' => 'checkbox',
      '#title' => '電子郵件',
      '#default_value' => $this->store->get('communication_mode.email') ? $this->store->get('communication_mode.email') : '',
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
    $this->store->set('member_attention1', $form_state->getValue('member_attention1'));
    $this->store->set('communication_mode.phone', $form_state->getValue('phone'));
    $this->store->set('communication_mode.phone_text', $form_state->getValue('phone_text'));
    $this->store->set('communication_mode.email', $form_state->getValue('email'));

    // Save the data
    parent::saveData();
    $form_state->setRedirect('some_route');
  }
}
