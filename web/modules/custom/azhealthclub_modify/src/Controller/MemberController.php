<?php

namespace Drupal\azhealthclub_modify\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\profile\Entity\Profile;
use function Webmozart\Assert\Tests\StaticAnalysis\inArray;

class MemberController extends ControllerBase {

  public function index() {
    $member = \Drupal::currentUser();
    $variables['memberId'] = $member->id();
    return [
      '#theme' => 'azhealthclub_welcome',
      '#type' => 'markup',
      '#variables' => $variables
    ];
  }

  public function dashboard() {
    $member = \Drupal::currentUser();
    $roles = $member->getRoles();
    if(!in_array('member', $roles)) {
      // todo: not has member role
    }
    $variables['memberId'] = $member->id();
    $storage = \Drupal::entityTypeManager()->getStorage('profile');
    $profile = $storage->loadByUser($member, 'member');
    if ($profile) {
      if ($profile->get('field_gender') == 'female') {
        $variables['genderName'] = '女士';
      }
      else {
        $variables['genderName'] = '先生';
      }
      //$variables['name'] = $member->getDisplayName();
      //$profile->get('field_en_name')->getValue();
      $variables['name'] = $profile->get('field_zh_name')->getValue()[0]['value'];

      return [
        '#theme' => 'azhealthclub_dashboard',
        '#type' => 'markup',
        '#variables' => $variables
      ];
    }
    else {
      // todo: not has member profile
    }

  }

}
