<?php

/**
 * @file
 * Contains \Drupal\azhealthclub_step_login\Form\MultistepThreeForm.
 */

namespace Drupal\azhealthclub_step_login\Form;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;

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

    $entityFieldManager = \Drupal::service('entity_field.manager');
    $memberFields = $entityFieldManager->getFieldDefinitions('profile', 'member');

    $form['no_medicine_using'] = [
      '#type' => 'checkbox',
      '#title' => '本人沒有服用「阿斯利康」以下藥物，但是有興趣收取「康心摯友會」健康資訊（請√閣下有興趣之範疇）',
      '#default_value' => $this->store->get('no_medicine_using') ? $this->store->get('no_medicine_using') : '',
    ];
    $taxonomy_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('ta_type');
    $ta_type = [];
    foreach ($taxonomy_terms as $term) {
      if ($term->depth == 0)  {
        $ta_type[$term->tid] = $term->name;
      }
    }
    $form['ta_type'] = [
      '#type' => 'checkboxes',
      '#title' => '',
      '#options'=> $ta_type,
      '#default_value' => $this->store->get('ta_type') ? $this->store->get('ta_type') : [],
      '#states' => [
        'visible' => [
          ':input[name="no_medicine_using"]' => ['checked' => TRUE],
        ],
      ],
    ];

    // todo add picture
    $ids = \Drupal::entityQuery('node')
      ->condition('type', 'az_product_information')
      ->condition('status', 1)
      ->condition('field_promote_to_register_form', TRUE)
      ->execute();
    if (!empty($ids)) {
      $database = \Drupal::database();
      $pd_img = $database->select('node__field_az_product_img', 'n')
        ->fields("n",["field_az_product_img_target_id","entity_id"])
        ->condition("n.bundle","az_product_information","=")
        ->condition("n.entity_id",$ids,"IN")
        ->execute()
        ->fetchAll();
    }
    else {
      $pd_img = [];
    }

    $pd_v = [];
    $pd_t = [];
    foreach($pd_img as $key =>$v){
      $img_url = file_url_transform_relative(file_create_url(File::load($v->field_az_product_img_target_id)->getFileUri()));
      $imv = '<img src='.$img_url.' '.'img_id='.$v->entity_id.'/>';
      array_push($pd_t,$v->entity_id);
      array_push($pd_v,$pd_v[$v->entity_id]=$imv);
    }
    foreach($pd_v as $kk => $vv){
      if(!in_array($kk,$pd_t)){
        unset($pd_v[$kk]) ;
      }
    }

    $form['medicine_using'] = [
      '#type' => 'radios',
      '#title' => '本人正服用「阿斯利康」以下藥物（請√閣下現在正服用「阿斯利康」藥物）',
      '#options' => $pd_v,
      '#default_value' => $this->store->get('medicine_using') ? $this->store->get('medicine_using') : [],
      '#states' => [
        'visible' => [
          ':input[name="no_medicine_using"]' => ['checked' => FALSE],
        ],
      ],
    ];

    $field_settings = $memberFields['field_where1']->getSettings();
    $allowed_values = $field_settings['allowed_values'];
    $form['where1']= [
      '#type' => 'checkboxes',
      '#title' => '您在哪裡處方以上藥物',
      '#options' =>$allowed_values,
      '#default_value' => $this->store->get('where1') ? $this->store->get('where1') : [],
    ];

    $field_settings = $memberFields['field_where2']->getSettings();
    $allowed_values = $field_settings['allowed_values'];
    $form['where2']= [
      '#type' => 'checkboxes',
      '#title' => '您在哪裡處方以上藥物',
      '#options' => $allowed_values,
      '#default_value' => $this->store->get('where2') ? $this->store->get('where2') : [],
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
    $this->store->set('no_medicine_using', $form_state->getValue('no_medicine_using'));
    $this->store->set('ta_type', $form_state->getValue('ta_type'));
    $this->store->set('medicine_using', $form_state->getValue('medicine_using'));
    $this->store->set('where1', $form_state->getValue('where1'));
    $this->store->set('where2', $form_state->getValue('where2'));

    // Save the data
    parent::saveData();

    $currentUser = \Drupal::currentUser();
    if ($currentUser->isAnonymous()) {
      $form_state->setRedirect('azhealthclub_modify.welcome');
    }
    else {
      $form_state->setRedirect('azhealthclub_modify.dashboard');
    }
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    $values = $form_state->getValues();

    if ($values['no_medicine_using']) {
      $form_state->setValue('medicine_using', NULL);
    }
  }

}
