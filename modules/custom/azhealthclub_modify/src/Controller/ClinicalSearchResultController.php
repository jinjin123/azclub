<?php
namespace Drupal\azhealthclub_modify\Controller;

use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Controller\ControllerBase;


class ClinicalSearchResultController extends ControllerBase {

  public function index() {
    $variables['aa'] = 'a';
    return [
      '#theme' => 'azhealthclub_ClinicalSearchResult',
      '#type' => 'markup',
      '#variables' => $variables
    ];
  }
}
