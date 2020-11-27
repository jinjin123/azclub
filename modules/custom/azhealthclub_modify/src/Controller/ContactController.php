<?php
namespace Drupal\azhealthclub_modify\Controller;

use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Controller\ControllerBase;

class ContactController extends ControllerBase {

  public function index() {
    $variables['aa'] = 'a';
    return [
      '#theme' => 'azhealthclub_contact',
      '#type' => 'markup',
      '#variables' => $variables
    ];
  }

  public function health(){
    return [];
  }
  public function askfk(){
    $variables['aa'] = 'a';
    return [
      '#theme' => 'azhealthclub_faq',
      '#type' => 'markup',
      '#variables' => $variables
    ];
  }
}
