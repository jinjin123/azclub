<?php

namespace Drupal\azhealthclub_modify\Controller;

use Drupal\Core\Controller\ControllerBase;

class WelcomeController extends ControllerBase {

  public function index() {
    $member = \Drupal::currentUser();
    $variables['memberId'] = $member->id();
    return [
      '#theme' => 'azhealthclub_welcome',
      '#type' => 'markup',
      '#variables' => $variables
    ];
  }

}
