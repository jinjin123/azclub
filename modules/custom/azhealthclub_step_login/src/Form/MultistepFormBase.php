<?php

/**
* @file
* Contains \Drupal\azhealthclub_step_login\Form\MultistepFormBase.
*/

namespace Drupal\azhealthclub_step_login\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\SessionManagerInterface;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\user\Entity\User;
use Drupal\profile\Entity\Profile;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class MultistepFormBase extends FormBase {

  /**
  * @var \Drupal\Core\TempStore\PrivateTempStoreFactory
  */
  protected $tempStoreFactory;

  /**
  * @var \Drupal\Core\Session\SessionManagerInterface
  */
  private $sessionManager;

  /**
  * @var \Drupal\Core\Session\AccountInterface
  */
  private $currentUser;

  /**
  * @var \Drupal\Core\TempStore\PrivateTempStore
  */
  protected $store;

  /**
  * Constructs a \Drupal\azhealthclub_step_login\Form\MultistepFormBase.
  *
  * @param \Drupal\Core\TempStore\PrivateTempStoreFactory $temp_store_factory
  * @param \Drupal\Core\Session\SessionManagerInterface $session_manager
  * @param \Drupal\Core\Session\AccountInterface $current_user
  */
  public function __construct(PrivateTempStoreFactory $temp_store_factory, SessionManagerInterface $session_manager, AccountInterface $current_user) {
    $this->tempStoreFactory = $temp_store_factory;
    $this->sessionManager = $session_manager;
    $this->currentUser = $current_user;

    $this->store = $this->tempStoreFactory->get('multistep_data');
  }

  /**
  * {@inheritdoc}
  */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('tempstore.private'),
      $container->get('session_manager'),
      $container->get('current_user')
    );
  }

  /**
  * {@inheritdoc}.
  */
  public function buildForm(array $form, FormStateInterface $form_state) {

    if (!\Drupal::currentUser()->isAnonymous()) {
      $response = new RedirectResponse('/user');
      $response->send();
    }

    // Start a manual session for anonymous users.
    if (\Drupal::currentUser()->isAnonymous() && !isset($_SESSION['multistep_form_holds_session'])) {
      $_SESSION['multistep_form_holds_session'] = true;
      $this->sessionManager->start();
    }

    $form = [];
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
    '#type' => 'submit',
    '#value' => $this->t('Submit'),
    '#button_type' => 'primary',
    '#weight' => 10,
    ];

    return $form;
  }

  /**
  * Saves the data from the multistep form.
  */
  protected function saveData() {
    // Create and account and a member profile or save the origin
    $phone = $this->store->get('phone');
    $email = $this->store->get('email');
    $pass = $this->store->get('pass');
    $attention1 = $this->store->get('attention1');

    $zh_name = $this->store->get('zh_name');
    $en_name = $this->store->get('en_name');
    $identification_last4num = $this->store->get('identification_last4num');
    $birthday = $this->store->get('birthday');
    $gender = $this->store->get('gender');
    $attention2 = $this->store->get('attention2');
    $communication_mode = $this->store->get('communication_mode');

    $no_medicine_using = $this->store->get('no_medicine_using');
    $ta_type = $this->store->get('ta_type');
    $medicine_using = $this->store->get('medicine_using');
    $where1 = $this->store->get('where1');
    $where2 = $this->store->get('where2');

    // get filter value
    $attention1_filter = array_values(array_filter($attention1));
    $attention2_filter = array_values(array_filter($attention2));
    $communication_mode_filter = array_values(array_filter($communication_mode));
    $ta_type_filter = array_values(array_filter($ta_type));
    $where1_filter =  array_values(array_filter($where1));
    $where2_filter =  array_values(array_filter($where2));

    //Create account
    $currentUser = \Drupal::currentUser();
    if ($currentUser->isAnonymous()) {
      $user = User::create();
      $user->setPassword($pass);
      $user->enforceIsNew();
      $user->setEmail($email);
      $user->setUsername($phone); //This username must be unique and accept only a-Z,0-9, - _ @ .
      $user->addRole('member');
      $user->activate();
      $user->save();
      $uid = $user->id();
    }
    else {
      $uid = $currentUser->id();
      $user = User::load($uid);
      $user->set('mail', $email);
      $user->set('name', $phone);
      $user->save();
    }



    //Create profile
    if ($currentUser->isAnonymous()) {
      $profile = Profile::create(['type' => 'member']);
    }
    else {
      $storage = \Drupal::entityTypeManager()->getStorage('profile');
      $profile = $storage->loadByUser($currentUser, 'member');
    }

    $profile->set('uid', $uid);
    $profile->set('field_phone', $phone);
    $profile->set('field_attention1',$attention1_filter);
    $profile->set('field_zh_name', $zh_name);
    $profile->set('field_en_name', $en_name);
    $profile->set('field_identification_last4num', $identification_last4num);
    $profile->set('field_birthday', $birthday);
    $profile->set('field_gender', $gender);
    $profile->set('field_attention2', $attention2_filter);
    $profile->set('field_communication_mode', $communication_mode_filter);
    $profile->set('field_no_medicine_using', $no_medicine_using);
    $profile->set('field_ta_type', $ta_type_filter);
    $profile->set('field_medicine_using', $medicine_using);
    $profile->set('field_where1', $where1_filter);
    $profile->set('field_where2', $where2_filter);
    $profile->save();

    // auto login
    if ($currentUser->isAnonymous()) {
      user_login_finalize($user);
    }

    $this->deleteStore();
    \Drupal::messenger()->addMessage($this->t('The form has been saved.'));
  }

  /**
  * Helper method that removes all the keys from the store collection used for
  * the multistep form.
  */
  protected function deleteStore() {
    $keys = ['phone', 'email', 'pass', 'attention1', 'vrf_code_time_uuid',
      'zh_name', 'en_name', 'identification_last4num', 'birthday', 'gender', 'attention2', 'communication_mode',
      'no_medicine_using', 'ta_type', 'medicine_using', 'where1', 'where2'
    ];
    foreach ($keys as $key) {
      $this->store->delete($key);
    }
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state); // TODO: Change the autogenerated stub
  }

}
