<?php

/**
 * @file
 * Contains \Drupal\azhealthclub_step_login\Form\MultistepThreeForm.
 */

namespace Drupal\azhealthclub_step_login\Form;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

class MultistepThreeForm extends MultistepFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'multistep_form_three';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

    $form['field_attention']['member_attention2'] = [
      '#type' => 'checkbox',
      '#title' => '本人沒有服用「阿斯利康」以下藥物，但是有興趣收取「康心摯友會」健康資訊（請√閣下有興趣之範疇）',
      '#default_value' => $this->store->get('member_attention2') ? $this->store->get('member_attention2') : '',
    ];
    $taxonomy_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('ta_type');
    $ta_type = [];
    foreach ($taxonomy_terms as $term) {
      $ta_type[$term->tid] = $term->name;
    }
    $form['ta_type'] = [
      '#type' => 'checkboxes',
      '#title' => '',
      '#options'=> $ta_type,
      '#default_value' => $this->store->get('ta_type') ? $this->store->get('ta_type') : [],
    ];

    $form['medicine_using'] = [
      '#type' => 'radios',
      '#title' => '本人正服用「阿斯利康」以下藥物（請√閣下現在正服用「阿斯利康」藥物）',
      '#options' => [
        '1' => 'medicine1',
        '2' => 'medicine2',
      ],
      '#default_value' => $this->store->get('medicine_using') ? $this->store->get('medicine_using') : [],
    ];
    $form['field_where1']= [
      '#type' => 'checkboxes',
      '#title' => '您在哪裡處方以上藥物',
      '#options' => [
        '1' => '私家醫院或診所',
        '2' => '公立醫院或門診',
      ],
      '#default_value' => $this->store->get('field_where1') ? $this->store->get('field_where1') : [],
    ];
    $form['field_where2']= [
      '#type' => 'checkboxes',
      '#title' => '您在哪裡處方以上藥物',
      '#options' => [
        '1' => '私家醫院或診所',
        '2' => '公立醫院或門診',
        '3' => '萬寧 Mannings',
        '4' => '屈臣氏 Watsons',
      ],
      '#default_value' => $this->store->get('field_where2') ? $this->store->get('field_where2') : [],
    ];

    $form['actions']['previous'] = [
      '#type' => 'link',
      '#title' => '上一步',
      '#attributes' => [
        'class' => ['btn'],
      ],
      '#weight' => 0,
      '#url' => Url::fromRoute('azhealthclub_step_login.multistep_two'),
    ];

    $form['actions']['submit']['#value'] = '下一步';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->store->set('member_attention2', $form_state->getValue('member_attention2'));
    $this->store->set('ta_type', $form_state->getValue('ta_type'));
    $this->store->set('medicine_using', $form_state->getValue('medicine_using'));
    $this->store->set('birthday', $form_state->getValue('birthday'));
    $this->store->set('member_attention1', $form_state->getValue('member_attention1'));
    $this->store->set('field_where1', $form_state->getValue('field_where1'));
    $this->store->set('field_where2', $form_state->getValue('field_where2'));

    // Save the data
    parent::saveData();
    $form_state->setRedirect('<front>');
  }
}
