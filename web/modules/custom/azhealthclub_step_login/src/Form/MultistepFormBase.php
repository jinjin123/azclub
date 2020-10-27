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
    // Start a manual session for anonymous users.
    if ($this->currentUser->isAnonymous() && !isset($_SESSION['multistep_form_holds_session'])) {
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
    // Logic for saving data goes here...
    $this->deleteStore();
    \Drupal::messenger()->addMessage($this->t('The form has been saved.'));

  }

  /**
  * Helper method that removes all the keys from the store collection used for
  * the multistep form.
  */
  protected function deleteStore() {
    $keys = ['name', 'email', 'age', 'location'];
    foreach ($keys as $key) {
      $this->store->delete($key);
    }
  }

}
